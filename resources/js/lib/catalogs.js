// Caché versionada de catálogos del frontend.
// Mantiene una copia en localStorage (o memoria si no está disponible) y
// revalida contra el servidor usando ETag para abaratar la comprobación.

export const CATALOG_SCHEMA_VERSION = 3;

export const CATALOG_ENDPOINT = '/api/static-data-for-frontend';

// La caché local se revalida contra el servidor cada 5 minutos.
export const CATALOG_REVALIDATE_AFTER_MS = 5 * 60 * 1000;

export const STORAGE_KEYS = {
    payload: 'lis.catalogs.payload',
    etag: 'lis.catalogs.etag',
    savedAt: 'lis.catalogs.savedAt',
    version: 'lis.catalogs.version',
};

// Claves que usaban las versiones anteriores del frontend y deben limpiarse.
export const LEGACY_STORAGE_KEYS = [
    'frontend_data_last_update',
    'documentos_paciente_data',
    'documentos_pagador_data',
    'paises_data',
    'municipios_data',
    'eps_data',
    'cups_data',
    'default_cies_data',
    'servicios_habilitados_data',
    'via_ingreso_data',
    'vias_ingreso_data',
    'modalidad_atencion_data',
    'tipos_atencion_data',
    'finalidad_consulta_data',
    'causas_externas_data',
    'tipos_afiliacion_data',
    'tipos_documento_data',
    'paises',
    'municipios',
];

// Storage mínimo en memoria para entornos sin localStorage (tests, SSR).
export function createMemoryStorage() {
    const datos = new Map();

    return {
        getItem: (clave) => (datos.has(clave) ? datos.get(clave) : null),
        setItem: (clave, valor) => {
            datos.set(clave, String(valor));
        },
        removeItem: (clave) => {
            datos.delete(clave);
        },
    };
}

const storagePredeterminado = () => {
    if (typeof window !== 'undefined') {
        try {
            if (window.localStorage) {
                return window.localStorage;
            }
        } catch {
            // Acceder a localStorage puede lanzar en contextos restringidos.
        }
    }

    return createMemoryStorage();
};

const esObjetoPlano = (valor) =>
    typeof valor === 'object' && valor !== null && !Array.isArray(valor);

export function createCatalogCache({
    storage = storagePredeterminado(),
    fetchImpl = (...args) => fetch(...args),
    now = () => Date.now(),
    revalidateAfterMs = CATALOG_REVALIDATE_AFTER_MS,
} = {}) {
    // Último payload válido conocido en memoria. Sirve de respaldo cuando el
    // almacenamiento falla o cuando este no persiste entre llamadas.
    let memoria = null;
    let inFlight = null;

    const clearStored = () => {
        for (const clave of Object.values(STORAGE_KEYS)) {
            try {
                storage.removeItem(clave);
            } catch {
                // La limpieza es best-effort.
            }
        }
    };

    const readSavedAt = () => {
        const valor = storage.getItem(STORAGE_KEYS.savedAt);

        if (valor === null || valor === '') {
            return null;
        }

        const fecha = Number(valor);

        return Number.isFinite(fecha) ? fecha : null;
    };

    const readStored = () => {
        try {
            const version = storage.getItem(STORAGE_KEYS.version);

            if (version === null || version !== String(CATALOG_SCHEMA_VERSION)) {
                clearStored();
                return null;
            }

            const serializado = storage.getItem(STORAGE_KEYS.payload);

            if (serializado === null) {
                clearStored();
                return null;
            }

            let data;

            try {
                data = JSON.parse(serializado);
            } catch {
                clearStored();
                return null;
            }

            return {
                data,
                etag: storage.getItem(STORAGE_KEYS.etag),
                // Si savedAt falta o no es válido, la entrada se trata como vieja.
                savedAt: readSavedAt(),
            };
        } catch {
            // Un storage roto no debe impedir seguir usando la memoria.
            return null;
        }
    };

    const writeStored = (data, etag, savedAt) => {
        try {
            storage.setItem(STORAGE_KEYS.payload, JSON.stringify(data));
        } catch {
            // Cuota agotada: se conserva el payload en memoria y no se escribe
            // savedAt, para que la próxima carga vuelva a intentar persistir.
            return;
        }

        try {
            storage.setItem(STORAGE_KEYS.etag, etag === null || etag === undefined ? '' : etag);
            storage.setItem(STORAGE_KEYS.version, String(CATALOG_SCHEMA_VERSION));
            storage.setItem(STORAGE_KEYS.savedAt, String(savedAt));
        } catch {
            // Un fallo en el resto de claves no invalida la respuesta obtenida.
        }
    };

    const usarCache = (guardado) => {
        memoria = guardado.data;

        return {
            data: guardado.data,
            etag: guardado.etag,
            savedAt: guardado.savedAt,
            fromCache: true,
        };
    };

    const ejecutarCarga = async (force) => {
        const guardado = readStored();
        const ahora = now();

        if (
            !force &&
            guardado &&
            guardado.savedAt !== null &&
            ahora - guardado.savedAt < revalidateAfterMs
        ) {
            return usarCache(guardado);
        }

        const headers = { Accept: 'application/json' };

        // Solo se condiciona la petición si hay un payload que conservar.
        if (guardado && guardado.data && guardado.etag) {
            headers['If-None-Match'] = guardado.etag;
        }

        let respuesta;

        try {
            respuesta = await fetchImpl(CATALOG_ENDPOINT, {
                headers,
                credentials: 'same-origin',
                cache: 'no-cache',
            });
        } catch (error) {
            if (guardado && guardado.data) {
                return usarCache(guardado);
            }

            if (memoria) {
                return {
                    data: memoria,
                    etag: null,
                    savedAt: null,
                    fromCache: true,
                };
            }

            throw error;
        }

        if (respuesta.status === 304 && guardado && guardado.data) {
            const savedAt = now();

            try {
                storage.setItem(STORAGE_KEYS.savedAt, String(savedAt));
            } catch {
                // Si no se puede reescribir savedAt, la entrada seguirá vieja.
            }

            return {
                data: guardado.data,
                etag: guardado.etag,
                savedAt,
                fromCache: true,
            };
        }

        if (!respuesta.ok) {
            if (guardado && guardado.data) {
                return usarCache(guardado);
            }

            if (memoria) {
                return {
                    data: memoria,
                    etag: null,
                    savedAt: null,
                    fromCache: true,
                };
            }

            const error = new Error(`HTTP ${respuesta.status}`);
            error.status = respuesta.status;
            throw error;
        }

        const body = await respuesta.json();

        if (!esObjetoPlano(body)) {
            throw new Error('La respuesta de catálogos no es un objeto válido');
        }

        const etag = respuesta.headers.get('etag');
        const savedAt = now();

        memoria = body;
        writeStored(body, etag, savedAt);

        return { data: body, etag, savedAt, fromCache: false };
    };

    const load = ({ force = false } = {}) => {
        if (inFlight) {
            return inFlight;
        }

        inFlight = ejecutarCarga(force).finally(() => {
            inFlight = null;
        });

        return inFlight;
    };

    const getCatalogs = () => {
        if (memoria) {
            return memoria;
        }

        const guardado = readStored();

        if (guardado) {
            memoria = guardado.data;
            return guardado.data;
        }

        return {};
    };

    const getCatalog = (nombre) => {
        const valor = getCatalogs()[nombre];

        return Array.isArray(valor) ? valor : [];
    };

    const invalidate = () => {
        try {
            storage.removeItem(STORAGE_KEYS.savedAt);
        } catch {
            // La invalidación es best-effort.
        }

        try {
            storage.removeItem(STORAGE_KEYS.etag);
        } catch {
            // La invalidación es best-effort.
        }
    };

    const clear = () => {
        clearStored();

        for (const clave of LEGACY_STORAGE_KEYS) {
            try {
                storage.removeItem(clave);
            } catch {
                // La limpieza es best-effort.
            }
        }

        memoria = null;
    };

    return { load, getCatalogs, getCatalog, invalidate, clear };
}

// Instancia única para toda la aplicación.
const cachePredeterminada = createCatalogCache();

export const loadCatalogs = (opciones) => cachePredeterminada.load(opciones);

export const getCatalogs = () => cachePredeterminada.getCatalogs();

export const getCatalog = (nombre) => cachePredeterminada.getCatalog(nombre);

export const invalidateCatalogs = () => cachePredeterminada.invalidate();

export const clearCatalogs = () => cachePredeterminada.clear();

// "Recientes" de búsquedas CIE, separados del catálogo de CIE y por usuario.
// Se guardan en localStorage (o memoria cuando no está disponible).

import { createMemoryStorage } from './catalogs.js';

export const CIE_RECENTS_LIMIT = 10;

// Clave de almacenamiento para los recientes de un usuario concreto.
export function cieRecentsKey(userId) {
    const id =
        typeof userId === 'string' || typeof userId === 'number'
            ? String(userId).trim()
            : '';

    return `lis.cie.recientes.${id || 'anon'}`;
}

// Lee el id del usuario desde el meta que renderiza el layout.
export function currentUserId() {
    if (typeof document === 'undefined') {
        return null;
    }

    const contenido = document.querySelector('meta[name="user-id"]')?.content;

    return contenido ? contenido : null;
}

const esEntradaValida = (item) =>
    typeof item === 'object' &&
    item !== null &&
    typeof item.codigo === 'string' &&
    typeof item.nombre === 'string';

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

export function createCieRecents({
    storage = storagePredeterminado(),
    userId = currentUserId(),
    limit = CIE_RECENTS_LIMIT,
} = {}) {
    const key = cieRecentsKey(userId);

    // Copia en memoria que se usa cuando el almacenamiento falla (cuota,
    // modo privado, etc.) para que la funcionalidad siga operando.
    let memoria = null;
    let usarMemoria = false;

    const list = () => {
        if (usarMemoria) {
            return (memoria ?? []).slice();
        }

        let crudo;

        try {
            crudo = storage.getItem(key);
        } catch {
            usarMemoria = true;
            return (memoria ?? []).slice();
        }

        if (crudo === null || crudo === '') {
            return [];
        }

        try {
            const datos = JSON.parse(crudo);

            if (!Array.isArray(datos)) {
                return [];
            }

            return datos.filter(esEntradaValida);
        } catch {
            return [];
        }
    };

    const add = (item) => {
        const actuales = list().filter((reciente) => reciente.codigo !== item.codigo);
        const siguiente = [item, ...actuales].slice(0, limit);

        try {
            storage.setItem(key, JSON.stringify(siguiente));
            memoria = siguiente;
        } catch {
            // Sin almacenamiento disponible: seguir en memoria.
            usarMemoria = true;
            memoria = siguiente;
        }

        return siguiente;
    };

    const clear = () => {
        memoria = null;

        try {
            storage.removeItem(key);
        } catch {
            usarMemoria = true;
        }
    };

    return { key, list, add, clear };
}

// Instancia única para toda la aplicación.
export const cieRecents = createCieRecents();

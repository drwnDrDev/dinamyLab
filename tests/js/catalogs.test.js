import test from 'node:test';
import assert from 'node:assert/strict';
import {
    CATALOG_SCHEMA_VERSION,
    STORAGE_KEYS,
    createCatalogCache,
    createMemoryStorage,
} from '../../resources/js/lib/catalogs.js';

const payload = { paises: [{ codigo_iso: 'CO', nombre: 'Colombia' }], eps: [] };

const ok = (body, etag = '"v1"') => ({
    status: 200,
    ok: true,
    headers: { get: (name) => (name.toLowerCase() === 'etag' ? etag : null) },
    json: async () => body,
});

const notModified = (etag = '"v1"') => ({
    status: 304,
    ok: false,
    headers: { get: (name) => (name.toLowerCase() === 'etag' ? etag : null) },
    json: async () => {
        throw new Error('304 no debe leerse');
    },
});

// Envuelve un storage en memoria para simular fallos puntuales de escritura.
const storageConFallo = (claveQueFalla) => {
    const base = createMemoryStorage();

    return {
        base,
        getItem: (clave) => base.getItem(clave),
        setItem: (clave, valor) => {
            if (clave === claveQueFalla) {
                const error = new Error('Cuota de almacenamiento agotada');
                error.name = 'QuotaExceededError';
                throw error;
            }
            base.setItem(clave, valor);
        },
        removeItem: (clave) => base.removeItem(clave),
    };
};

test('guarda y devuelve los catálogos', async () => {
    const storage = createMemoryStorage();
    const cache = createCatalogCache({
        storage,
        fetchImpl: async () => ok(payload),
    });

    const resultado = await cache.load();

    assert.deepEqual(resultado.data, payload);
    assert.equal(resultado.fromCache, false);
    assert.deepEqual(cache.getCatalog('paises'), payload.paises);
    assert.equal(storage.getItem(STORAGE_KEYS.version), String(CATALOG_SCHEMA_VERSION));
    assert.equal(storage.getItem(STORAGE_KEYS.etag), '"v1"');
    assert.ok(storage.getItem(STORAGE_KEYS.payload) !== null);
    assert.ok(storage.getItem(STORAGE_KEYS.savedAt) !== null);
});

test('expone el catálogo de sexos cuando el payload lo incluye', async () => {
    const sexos = [
        { codigo: 'F', nombre: 'Femenino', cod_sexo: '02' },
        { codigo: 'I', nombre: 'Indeterminado o Intersexual', cod_sexo: '03' },
        { codigo: 'M', nombre: 'Masculino', cod_sexo: '01' },
    ];
    const cache = createCatalogCache({
        storage: createMemoryStorage(),
        fetchImpl: async () => ok({ ...payload, sexos }),
    });

    await cache.load();

    assert.deepEqual(cache.getCatalog('sexos'), sexos);
});

test('no vuelve a pedir dentro de la ventana de revalidación', async () => {
    let llamadas = 0;
    const cache = createCatalogCache({
        storage: createMemoryStorage(),
        now: () => 1000,
        revalidateAfterMs: 5000,
        fetchImpl: async () => {
            llamadas += 1;
            return ok(payload);
        },
    });

    const primero = await cache.load();
    const segundo = await cache.load();

    assert.equal(llamadas, 1);
    assert.deepEqual(primero.data, payload);
    assert.deepEqual(segundo.data, payload);
    assert.equal(segundo.fromCache, true);
});

test('revalida con If-None-Match y acepta 304', async () => {
    let ahora = 0;
    const peticiones = [];
    const storage = createMemoryStorage();
    const cache = createCatalogCache({
        storage,
        now: () => ahora,
        revalidateAfterMs: 5000,
        fetchImpl: async (url, options) => {
            peticiones.push({ url, options });
            return peticiones.length === 1 ? ok(payload, '"v1"') : notModified('"v1"');
        },
    });

    await cache.load();
    ahora += 6000;
    const resultado = await cache.load();

    assert.equal(peticiones.length, 2);
    assert.equal(peticiones[1].url, '/api/static-data-for-frontend');
    assert.equal(peticiones[1].options.headers['If-None-Match'], '"v1"');
    assert.deepEqual(resultado.data, payload);
    assert.equal(resultado.fromCache, true);
    assert.equal(resultado.savedAt, ahora);
    assert.equal(storage.getItem(STORAGE_KEYS.savedAt), String(ahora));
});

test('si falta el payload no envía If-None-Match', async () => {
    const storage = createMemoryStorage();
    storage.setItem(STORAGE_KEYS.version, String(CATALOG_SCHEMA_VERSION));
    storage.setItem(STORAGE_KEYS.etag, '"v1"');

    let peticion;
    const cache = createCatalogCache({
        storage,
        fetchImpl: async (url, options) => {
            peticion = { url, options };
            return ok(payload);
        },
    });

    const resultado = await cache.load();

    assert.equal(peticion.options.headers['If-None-Match'], undefined);
    assert.deepEqual(resultado.data, payload);
    assert.equal(resultado.fromCache, false);
});

test('descarta una caché de otra versión de esquema', async () => {
    const storage = createMemoryStorage();
    storage.setItem(STORAGE_KEYS.version, '1');
    storage.setItem(STORAGE_KEYS.etag, '"viejo"');
    storage.setItem(
        STORAGE_KEYS.payload,
        JSON.stringify({ paises: [{ codigo_iso: 'XX', nombre: 'Viejo' }] }),
    );
    storage.setItem(STORAGE_KEYS.savedAt, '1000');

    let peticiones = 0;
    const cache = createCatalogCache({
        storage,
        fetchImpl: async () => {
            peticiones += 1;
            return ok(payload, '"nuevo"');
        },
    });

    const resultado = await cache.load();

    assert.equal(peticiones, 1);
    assert.deepEqual(resultado.data, payload);
    assert.equal(resultado.fromCache, false);
    assert.deepEqual(cache.getCatalog('paises'), payload.paises);
    assert.equal(storage.getItem(STORAGE_KEYS.version), String(CATALOG_SCHEMA_VERSION));
});

test('sigue funcionando si el almacenamiento lanza QuotaExceededError', async () => {
    const storage = storageConFallo(STORAGE_KEYS.payload);
    const cache = createCatalogCache({
        storage,
        fetchImpl: async () => ok(payload),
    });

    const resultado = await cache.load();

    assert.deepEqual(resultado.data, payload);
    assert.equal(resultado.fromCache, false);
    assert.deepEqual(cache.getCatalog('paises'), payload.paises);
    // No se escribe savedAt cuando el payload no pudo persistirse.
    assert.equal(storage.base.getItem(STORAGE_KEYS.savedAt), null);
});

test('deduplica cargas concurrentes', async () => {
    let llamadas = 0;
    const cache = createCatalogCache({
        storage: createMemoryStorage(),
        fetchImpl: async () => {
            llamadas += 1;
            await Promise.resolve();
            return ok(payload);
        },
    });

    const [primero, segundo] = await Promise.all([cache.load(), cache.load()]);

    assert.equal(llamadas, 1);
    assert.deepEqual(primero.data, payload);
    assert.deepEqual(segundo.data, payload);
});

test('invalidate obliga a pedir de nuevo sin ETag', async () => {
    let ahora = 0;
    const peticiones = [];
    const storage = createMemoryStorage();
    const cache = createCatalogCache({
        storage,
        now: () => ahora,
        revalidateAfterMs: 5000,
        fetchImpl: async (url, options) => {
            peticiones.push({ url, options });
            return ok(payload, '"v1"');
        },
    });

    await cache.load();
    cache.invalidate();
    ahora += 6000;
    await cache.load();

    assert.equal(peticiones.length, 2);
    assert.equal(peticiones[1].options.headers['If-None-Match'], undefined);
    // invalidate conserva el payload en storage.
    assert.ok(storage.getItem(STORAGE_KEYS.payload) !== null);
});

test('si la red falla, devuelve la caché existente', async () => {
    let fallar = false;
    const cache = createCatalogCache({
        storage: createMemoryStorage(),
        fetchImpl: async () => {
            if (fallar) {
                throw new Error('Sin conexión');
            }
            return ok(payload);
        },
    });

    await cache.load();
    cache.invalidate();
    fallar = true;
    const resultado = await cache.load();

    assert.deepEqual(resultado.data, payload);
    assert.equal(resultado.fromCache, true);
});

test('devuelve un arreglo vacío para un catálogo ausente', async () => {
    const cache = createCatalogCache({
        storage: createMemoryStorage(),
        fetchImpl: async () => ok(payload),
    });

    await cache.load();

    assert.deepEqual(cache.getCatalog('desconocido'), []);
    assert.deepEqual(cache.getCatalog('eps'), []);
});

test('rechaza si no hay caché y la respuesta no es ok', async () => {
    const cache = createCatalogCache({
        storage: createMemoryStorage(),
        fetchImpl: async () => ({
            status: 401,
            ok: false,
            headers: { get: () => null },
            json: async () => ({ message: 'No autorizado' }),
        }),
    });

    await assert.rejects(() => cache.load(), /HTTP 401/);
});

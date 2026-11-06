import test from 'node:test';
import assert from 'node:assert/strict';
import {
    CIE_RECENTS_LIMIT,
    cieRecentsKey,
    createCieRecents,
} from '../../resources/js/lib/cieRecents.js';
import { createMemoryStorage } from '../../resources/js/lib/catalogs.js';

test('agrega al inicio, deduplica y limita a 10', () => {
    const storage = createMemoryStorage();
    const recents = createCieRecents({ storage, userId: 7 });

    for (let i = 1; i <= 12; i++) {
        recents.add({ codigo: `Z${i}`, nombre: `CIE ${i}` });
    }
    recents.add({ codigo: 'Z12', nombre: 'CIE 12 actualizado' });

    const list = recents.list();
    assert.equal(list.length, CIE_RECENTS_LIMIT);
    assert.equal(list[0].codigo, 'Z12');
    assert.equal(list.filter((item) => item.codigo === 'Z12').length, 1);
});

test('devuelve lista vacía si el JSON está corrupto', () => {
    const storage = createMemoryStorage();
    storage.setItem(cieRecentsKey(7), '{no-json');
    assert.deepEqual(createCieRecents({ storage, userId: 7 }).list(), []);
});

test('separa los recientes por usuario', () => {
    const storage = createMemoryStorage();
    createCieRecents({ storage, userId: 1 }).add({ codigo: 'A01', nombre: 'Uno' });
    createCieRecents({ storage, userId: 2 }).add({ codigo: 'B02', nombre: 'Dos' });

    assert.equal(createCieRecents({ storage, userId: 1 }).list()[0].codigo, 'A01');
    assert.equal(createCieRecents({ storage, userId: 2 }).list()[0].codigo, 'B02');
});

test('clear borra solo los recientes del usuario', () => {
    const storage = createMemoryStorage();
    createCieRecents({ storage, userId: 1 }).add({ codigo: 'A01', nombre: 'Uno' });
    createCieRecents({ storage, userId: 2 }).add({ codigo: 'B02', nombre: 'Dos' });

    createCieRecents({ storage, userId: 1 }).clear();

    assert.deepEqual(createCieRecents({ storage, userId: 1 }).list(), []);
    assert.equal(createCieRecents({ storage, userId: 2 }).list().length, 1);
});

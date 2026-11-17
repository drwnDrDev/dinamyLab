import test from 'node:test';
import assert from 'node:assert/strict';
import {
    haCambiado,
    estadoRango,
    resumirCambios,
} from '../../resources/js/lib/edicionResultados.js';

test('haCambiado ignora espacios y trata null como vacío', () => {
    assert.equal(haCambiado('12.5', '12.5'), false);
    assert.equal(haCambiado('12.5', ' 12.5 '), false);
    assert.equal(haCambiado(null, ''), false);
    assert.equal(haCambiado(null, '3'), true);
    assert.equal(haCambiado('12.5', '13.1'), true);
    assert.equal(haCambiado('12.5', ''), true);
});

test('estadoRango compara contra min y max', () => {
    assert.equal(estadoRango('11', 12, 16), 'bajo');
    assert.equal(estadoRango('17', 12, 16), 'alto');
    assert.equal(estadoRango('12', 12, 16), 'normal');
    assert.equal(estadoRango('16', 12, 16), 'normal');
    assert.equal(estadoRango('20', null, 16), 'alto');
    assert.equal(estadoRango('1', 5, null), 'bajo');
});

test('estadoRango acepta coma decimal', () => {
    assert.equal(estadoRango('11,9', 12, 16), 'bajo');
});

test('estadoRango no evalúa sin referencia, vacío o texto', () => {
    assert.equal(estadoRango('11', null, null), null);
    assert.equal(estadoRango('', 12, 16), null);
    assert.equal(estadoRango(null, 12, 16), null);
    assert.equal(estadoRango('Negativo', 12, 16), null);
    assert.equal(estadoRango('12abc', 12, 16), null);
});

test('resumirCambios devuelve solo los parámetros modificados con su rango', () => {
    const filas = [
        { id: 1, nombre: 'Hemoglobina', original: '13.2', actual: '11.0', min: 12, max: 16 },
        { id: 2, nombre: 'Plaquetas', original: '250', actual: '250', min: 150, max: 450 },
        { id: 3, nombre: 'Observaciones', original: null, actual: 'Muestra lipémica', min: null, max: null },
    ];

    assert.deepEqual(resumirCambios(filas), [
        { id: 1, nombre: 'Hemoglobina', anterior: '13.2', nuevo: '11.0', rango: 'bajo' },
        { id: 3, nombre: 'Observaciones', anterior: '', nuevo: 'Muestra lipémica', rango: null },
    ]);
});

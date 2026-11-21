import test from 'node:test';
import assert from 'node:assert/strict';
import { crearRemisionSeleccionada, filtrarRemisiones } from '../../resources/js/lib/remisiones.js';

const remision = { cup: '901101', nombre: 'Baciloscopía ZN 1 muestra' };
const tarifa = { id: 12, ips_externa_id: 3, precio: '20000.00', valor_remitido: null, ips_externa: { nombre: 'Colcan' } };

test('la búsqueda encuentra CUPS y nombres sin depender de tildes o mayúsculas', () => {
    const catalogo = [remision, { cup: '903809', nombre: 'Bilirrubinas' }];
    assert.deepEqual(filtrarRemisiones(catalogo, ' BACILOSCOPIA '), [remision]);
    assert.deepEqual(filtrarRemisiones(catalogo, '90110'), [remision]);
    assert.equal(filtrarRemisiones(catalogo, '').length, 2);
});

test('seleccionar la tarifa completa el destino y permite elegir el precio', () => {
    const seleccion = crearRemisionSeleccionada(remision, tarifa, '25000');
    assert.equal(seleccion.valor_remision_id, 12);
    assert.equal(seleccion.ips_externa_id, 3);
    assert.equal(seleccion.destino, 'Colcan');
    assert.equal(seleccion.valor, 25000);
    assert.equal(seleccion.valor_remitido, null);
    assert.equal(seleccion.cup, '901101');
    assert.equal(crearRemisionSeleccionada(remision, tarifa).valor, 20000);
    assert.equal(crearRemisionSeleccionada(remision, tarifa, 0).valor, 0);
    assert.equal(crearRemisionSeleccionada(remision, tarifa, '').valor, '');
});

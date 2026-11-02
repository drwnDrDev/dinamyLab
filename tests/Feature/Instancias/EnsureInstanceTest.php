<?php

beforeEach(function () {
    config(['instancias.actual' => null, 'instancias.hosts' => []]);
});

test('sin instancia configurada no bloquea en entorno local', function () {
    $this->get('http://biotek.test/login')->assertOk();
});

test('en produccion sin dominios configurados responde 503', function () {
    $this->app['env'] = 'production';
    config(['instancias.actual' => 'biotek', 'instancias.hosts' => []]);

    $this->get('http://biotek.test/login')->assertStatus(503);
});

test('acepta el dominio declarado por la instancia', function () {
    config(['instancias.actual' => 'biotek', 'instancias.hosts' => ['biotek.test']]);

    $this->get('http://biotek.test/login')->assertOk();
});

test('rechaza un dominio ajeno', function () {
    config(['instancias.actual' => 'biotek', 'instancias.hosts' => ['biotek.test']]);

    $this->get('http://sanpablo.test/login')->assertStatus(503);
});

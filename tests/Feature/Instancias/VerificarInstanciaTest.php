<?php

function configValida(): array
{
    return [
        'instancias.actual' => 'biotek',
        'instancias.hosts' => ['biotek.test'],
        'app.url' => 'https://biotek.test',
        'app.key' => 'base64:'.base64_encode(random_bytes(32)),
        'session.domain' => null,
        'session.cookie' => 'biotek_session',
        'app.debug' => false,
    ];
}

test('falla si no hay instancia configurada', function () {
    config(['instancias.actual' => null]);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si el dominio de sesion es compartido', function () {
    config(configValida());
    config(['session.domain' => '.dinamylab.test']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si la cookie no lleva el nombre de la instancia', function () {
    config(configValida());
    config(['session.cookie' => 'laravel_session']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si APP_URL no esta en APP_HOSTS', function () {
    config(configValida());
    config(['app.url' => 'https://otro.test']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('pasa con una configuracion correcta', function () {
    config(configValida());

    $this->artisan('instancia:verificar')->assertExitCode(0);
});

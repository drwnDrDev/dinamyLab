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
        'app.env' => 'production',
        'app.debug' => false,
        'instancias.cliente.razon_social' => 'Laboratorio Biotek SAS',
        'instancias.cliente.nit' => '900123456-7',
        'instancias.cliente.sede' => 'Sede Principal',
        'instancias.cliente.codigo_prestador' => '9001234567',
        'instancias.cliente.admin_email' => 'admin@biotek.test',
        'instancias.cliente.admin_password' => 'clave-larga-y-unica',
        'instancias.cliente.admin_documento' => '1000000001',
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

test('falla si falta la razon social del cliente', function () {
    config(configValida());
    config(['instancias.cliente.razon_social' => '']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si falta el NIT del cliente', function () {
    config(configValida());
    config(['instancias.cliente.nit' => '']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si el email del admin no es valido', function () {
    config(configValida());
    config(['instancias.cliente.admin_email' => 'no-es-un-email']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si la contrasena del admin esta vacia', function () {
    config(configValida());
    config(['instancias.cliente.admin_password' => '']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si APP_DEBUG esta activo en produccion', function () {
    config(configValida());
    config(['app.debug' => true]);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si APP_ENV no es production', function () {
    config(configValida());
    config(['app.env' => 'local']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('pasa con una configuracion correcta', function () {
    config(configValida());

    $this->artisan('instancia:verificar')->assertExitCode(0);
});

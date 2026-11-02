<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Instancia actual
    |--------------------------------------------------------------------------
    |
    | Identifica qué cliente sirve este despliegue. Se declara en el .env de
    | cada instancia (APP_INSTANCE). Si está vacío, la guarda de instancia no
    | actúa: es el comportamiento de desarrollo y de los tests.
    |
    */

    'actual' => env('APP_INSTANCE'),

    /*
    |--------------------------------------------------------------------------
    | Dominios permitidos
    |--------------------------------------------------------------------------
    |
    | Lista separada por comas de los hostnames que esta instancia puede
    | atender (APP_HOSTS). Una petición dirigida a otro dominio se rechaza
    | con 503 en producción.
    |
    */

    'hosts' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('APP_HOSTS', ''))
    ))),

    /*
    |--------------------------------------------------------------------------
    | Datos del cliente
    |--------------------------------------------------------------------------
    |
    | Datos de negocio de la instancia. Se cargan desde el .env para que el
    | código no conozca a ningún cliente. Los consume ClienteSeeder.
    |
    */

    'cliente' => [
        'razon_social' => env('CLIENTE_RAZON_SOCIAL', 'Laboratorio Demo'),
        'nit' => env('CLIENTE_NIT', '000000000-0'),
        'sede' => env('CLIENTE_SEDE', 'Sede Principal'),
        'codigo_prestador' => env('CLIENTE_CODIGO_PRESTADOR', '0000000000'),
        'admin_email' => env('CLIENTE_ADMIN_EMAIL', 'admin@ejemplo.test'),
        'admin_password' => env('CLIENTE_ADMIN_PASSWORD', 'password'),
        'admin_documento' => env('CLIENTE_ADMIN_DOCUMENTO', '1000000000'),
    ],

];

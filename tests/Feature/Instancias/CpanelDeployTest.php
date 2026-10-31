<?php

test('el archivo de despliegue no contiene rutas absolutas', function () {
    $yaml = file_get_contents(base_path('.cpanel.yml'));

    expect($yaml)->not->toContain('/home/')
        ->and($yaml)->not->toContain('$HOME')
        ->and($yaml)->not->toContain('~/');
});

test('el archivo de despliegue no ejecuta migraciones', function () {
    $yaml = file_get_contents(base_path('.cpanel.yml'));

    expect($yaml)->not->toContain('migrate');
});

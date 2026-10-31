<?php

test('la vista base usa el nombre configurado y no un titulo fijo', function () {
    $blade = file_get_contents(resource_path('views/layouts/app.blade.php'));

    expect($blade)->toContain("config('app.name')")
        ->and($blade)->not->toContain('Laboratorio  Clinico');
});

<?php

use App\Services\ElegirEmpresa;
use Illuminate\Foundation\Testing\RefreshDatabase;
uses(RefreshDatabase::class);
test('elegir sede', function () {
    $sede = ElegirEmpresa::elegirSede();
    $this->assertNull($sede);
});

test('elegir sede con empleado en session', function () {
    $this->seed(); // Ejecuta DatabaseSeeder
    $empleado = \App\Models\Empleado::find(1);
    session(['empleado' => $empleado]);
    $sede = ElegirEmpresa::elegirSede();
    $this->assertNotNull($sede);

});

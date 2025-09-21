<?php

use App\Services\ElegirEmpresa;
test('elegir sede', function () {
    $sede = ElegirEmpresa::elegirSede();
    $this->assertNull($sede);
});

// test('elegir sede con empresa en session', function () {
//     $empresa = \App\Models\Empresa::find(1);
//     $this->assertNotNull($empresa);

// });

test('elegir sede con empleado en session', function () {
    $empleado = \App\Models\Empleado::find(1);
    $this->assertNotNull($empleado);
    // $sede = ElegirEmpresa::elegirSede();
    // session(['empleado' => $empleado]);
    // $sede = ElegirEmpresa::elegirSede();
    // $this->assertEquals($empleado->sede->id, $sede->id);
});

<?php

use App\Models\IpsExterna;
use App\Models\Remision;
use App\Models\User;
use App\Models\ValorRemision;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\ValorRemisionSeeder;

it('importa el CSV sin duplicados y conserva costos ausentes y precios originales', function () {
    $this->seed(ValorRemisionSeeder::class);
    IpsExterna::where('nombre', 'Idime')->update(['nit' => '123-4', 'telefono' => '3001234567']);
    $this->seed(ValorRemisionSeeder::class);

    expect(Remision::count())->toBe(51);
    expect(IpsExterna::count())->toBe(3);
    expect(ValorRemision::count())->toBe(53);
    expect(IpsExterna::where('nombre', 'Idime')->first())
        ->nit->toBe('123-4')->telefono->toBe('3001234567');
    expect(IpsExterna::where('nombre', 'Colcan')->first())->nit->toBeNull()->telefono->toBeNull();
    expect(Remision::where('cup', '901101')->first()->valores()->with('ipsExterna')->get()->pluck('ipsExterna.nombre')->sort()->values()->all())
        ->toBe(['Colcan', 'Idime']);
    expect(Remision::where('cup', '20261')->first()->valores->first()->valor_remitido)->toBeNull();
    expect(Remision::where('cup', '904927')->first()->valores->first())
        ->precio->toBe('14000.00')->valor_remitido->toBe('95000.00');
});

it('entrega el catálogo completo con las tarifas e IPS a quien puede crear órdenes', function () {
    $this->seed([RolesYPermisosSeeder::class, ValorRemisionSeeder::class]);
    $this->getJson('/api/remisiones')->assertUnauthorized();
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum')->getJson('/api/remisiones')->assertForbidden();
    $user->assignRole('prestador');

    $respuesta = $this->getJson('/api/remisiones')->assertOk()->assertJsonCount(51, 'data')
        ->assertJsonStructure(['data' => [['id', 'cup', 'nombre', 'valores' => [
            ['id', 'precio', 'valor_remitido', 'ips_externa_id', 'ips_externa' => ['id', 'nombre', 'nit', 'telefono']],
        ]]]]);
    $baciloscopia = collect($respuesta->json('data'))->firstWhere('cup', '901101');
    expect($baciloscopia['valores'])->toHaveCount(2);
});

it('guarda un CUP manual en el catálogo y permite reutilizarlo', function (string $rol) {
    $this->seed(RolesYPermisosSeeder::class);
    $user = User::factory()->create()->assignRole($rol);

    $respuesta = $this->actingAs($user, 'sanctum')->postJson('/api/remisiones', [
        'cup' => '009001', 'nombre' => 'Examen nuevo', 'valor' => 12345.67, 'destino' => 'IPS nueva',
    ])->assertCreated()
        ->assertJsonPath('seleccion.cup', '009001')
        ->assertJsonPath('seleccion.nombre', 'Examen nuevo')
        ->assertJsonPath('seleccion.destino', 'IPS nueva')
        ->assertJsonPath('seleccion.valor', '12345.67')
        ->assertJsonPath('seleccion.valor_remitido', null);

    $this->assertDatabaseCount('remisiones', 1);
    $this->assertDatabaseCount('ips_externas', 1);
    $this->assertDatabaseCount('valores_remision', 1);
    $this->assertDatabaseHas('valores_remision', [
        'id' => $respuesta->json('seleccion.valor_remision_id'), 'precio' => 12345.67, 'valor_remitido' => null,
    ]);
    $this->getJson('/api/remisiones')->assertOk()
        ->assertJsonPath('data.0.cup', '009001')
        ->assertJsonPath('data.0.valores.0.ips_externa.nombre', 'IPS nueva');
})->with(['agente', 'prestador', 'admin']);

it('exige autenticación y permiso para guardar remisiones', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $datos = ['cup' => '999001', 'nombre' => 'Examen', 'valor' => 0, 'destino' => 'IPS'];
    $this->postJson('/api/remisiones', $datos)->assertUnauthorized();
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum')->postJson('/api/remisiones', $datos)->assertForbidden();
    $this->assertDatabaseCount('remisiones', 0);
    $this->assertDatabaseCount('ips_externas', 0);
    $this->assertDatabaseCount('valores_remision', 0);
});

it('valida los datos manuales antes de modificar el catálogo', function (array $cambios, array $errores) {
    $this->seed(RolesYPermisosSeeder::class);
    $user = User::factory()->create()->assignRole('agente');
    $datos = ['cup' => '999001', 'nombre' => 'Examen', 'valor' => 0, 'destino' => 'IPS'];
    $this->actingAs($user, 'sanctum')->postJson('/api/remisiones', array_replace($datos, $cambios))
        ->assertUnprocessable()->assertJsonValidationErrors($errores);
    $this->assertDatabaseCount('remisiones', 0);
    $this->assertDatabaseCount('ips_externas', 0);
    $this->assertDatabaseCount('valores_remision', 0);
})->with([
    'campos incompletos' => [['cup' => str_repeat('1', 21), 'nombre' => ' ', 'destino' => '', 'accion' => 'otra'], ['cup', 'nombre', 'destino', 'accion']],
    'precio negativo' => [['valor' => -1], ['valor']],
    'precio fuera de rango' => [['valor' => 100000000], ['valor']],
    'más de dos decimales' => [['valor' => 12.345], ['valor']],
]);

it('pide una decisión para un CUP existente sin cambiar ni crear datos', function () {
    $this->seed([RolesYPermisosSeeder::class, ValorRemisionSeeder::class]);
    $user = User::factory()->create()->assignRole('agente');
    $remision = Remision::where('cup', '901101')->firstOrFail();
    $tarifa = $remision->valores()->with('ipsExterna')->orderBy('precio')->orderBy('id')->firstOrFail();
    $antes = $remision->toArray();

    $this->actingAs($user, 'sanctum')->postJson('/api/remisiones', [
        'cup' => $remision->cup, 'nombre' => 'Nombre nuevo', 'valor' => 999, 'destino' => 'IPS sin registrar',
    ])->assertConflict()
        ->assertJsonPath('data.nombre', $remision->nombre)
        ->assertJsonPath('seleccion.valor_remision_id', $tarifa->id)
        ->assertJsonPath('seleccion.destino', $tarifa->ipsExterna->nombre)
        ->assertJsonPath('seleccion.valor', $tarifa->precio);

    expect($remision->fresh()->toArray())->toBe($antes);
    $this->assertDatabaseCount('remisiones', 51);
    $this->assertDatabaseCount('ips_externas', 3);
    $this->assertDatabaseCount('valores_remision', 53);
    $this->assertDatabaseMissing('ips_externas', ['nombre' => 'IPS sin registrar']);
});

it('conserva los datos anteriores al elegir esa opción', function (bool $mismoDestino) {
    $this->seed([RolesYPermisosSeeder::class, ValorRemisionSeeder::class]);
    $user = User::factory()->create()->assignRole('agente');
    $remision = Remision::where('cup', '901101')->firstOrFail();
    $tarifas = $remision->valores()->with('ipsExterna')->orderBy('precio')->orderBy('id')->get();
    $tarifa = $mismoDestino ? $tarifas->last() : $tarifas->first();

    $this->actingAs($user, 'sanctum')->postJson('/api/remisiones', [
        'cup' => $remision->cup, 'nombre' => 'Nombre nuevo', 'valor' => 999,
        'destino' => $mismoDestino ? $tarifa->ipsExterna->nombre : 'IPS sin registrar', 'accion' => 'conservar',
    ])->assertOk()
        ->assertJsonPath('seleccion.nombre', $remision->nombre)
        ->assertJsonPath('seleccion.valor_remision_id', $tarifa->id)
        ->assertJsonPath('seleccion.destino', $tarifa->ipsExterna->nombre)
        ->assertJsonPath('seleccion.valor', $tarifa->precio);

    expect($remision->fresh()->nombre)->toBe($remision->nombre);
    foreach ($tarifas as $original) {
        expect($original->fresh()->getAttributes())->toBe($original->getAttributes());
    }
    $this->assertDatabaseCount('ips_externas', 3);
    $this->assertDatabaseCount('valores_remision', 53);
})->with([true, false]);

it('actualiza el CUP y su tarifa sin borrar los otros destinos ni sus costos', function (bool $mismoDestino) {
    $this->seed([RolesYPermisosSeeder::class, ValorRemisionSeeder::class]);
    $user = User::factory()->create()->assignRole('agente');
    $remision = Remision::where('cup', '901101')->firstOrFail();
    $tarifas = $remision->valores()->with('ipsExterna')->get();
    $tarifa = $tarifas->first();
    $destino = $mismoDestino ? mb_strtoupper($tarifa->ipsExterna->nombre) : 'IPS nueva';

    $respuesta = $this->actingAs($user, 'sanctum')->postJson('/api/remisiones', [
        'cup' => $remision->cup, 'nombre' => 'Nombre actualizado', 'valor' => 25000,
        'destino' => $destino, 'accion' => 'actualizar',
    ])->assertOk()
        ->assertJsonPath('seleccion.nombre', 'Nombre actualizado')
        ->assertJsonPath('seleccion.valor', '25000.00')
        ->assertJsonPath('seleccion.valor_remitido', $mismoDestino ? $tarifa->valor_remitido : null);

    expect($remision->fresh()->nombre)->toBe('Nombre actualizado');
    foreach ($tarifas as $original) {
        if ($original->id !== $respuesta->json('seleccion.valor_remision_id')) {
            expect($original->fresh()->precio)->toBe($original->precio);
        }
        expect($original->fresh()->valor_remitido)->toBe($original->valor_remitido);
    }
    $this->assertDatabaseCount('remisiones', 51);
    $this->assertDatabaseCount('ips_externas', $mismoDestino ? 3 : 4);
    $this->assertDatabaseCount('valores_remision', $mismoDestino ? 53 : 54);
})->with([true, false]);

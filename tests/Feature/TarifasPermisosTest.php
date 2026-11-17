<?php

use App\Models\Examen;
use App\Models\Sede;
use App\Models\Tarifa;
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;

beforeEach(function () {
    $this->seed(RolesYPermisosSeeder::class);
});

it('permite consultar precios por sede y reserva los cambios al administrador', function (string $rol) {
    $usuario = User::factory()->create();
    $usuario->assignRole($rol);
    $sede = Sede::factory()->create();
    $examen = Examen::factory()->create();
    $tarifa = Tarifa::create([
        'precio' => 45000,
        'tarifable_type' => Examen::class,
        'tarifable_id' => $examen->id,
    ]);
    $tarifa->sedes()->attach($sede);

    $this->actingAs($usuario)
        ->get(route('sedes.show', $sede))
        ->assertOk()
        ->assertSee('data-can-edit="'.($rol === 'admin' ? 'true' : 'false').'"', false);
    $this->getJson('/api/tarifas?sede_id='.$sede->id.'&tarifable_type=examen')
        ->assertOk()->assertJsonFragment(['id' => $tarifa->id]);
    $this->getJson('/api/tarifas/'.$tarifa->id)->assertOk();

    if ($rol === 'admin') {
        $this->putJson('/api/tarifas/'.$tarifa->id, ['precio' => 50000])->assertOk();
        expect((float) $tarifa->fresh()->precio)->toBe(50000.0);
    } else {
        $this->postJson('/api/tarifas', [
            'precio' => 50000,
            'tarifable_type' => Examen::class,
            'tarifable_id' => $examen->id,
        ])->assertForbidden();
        $this->putJson('/api/tarifas/'.$tarifa->id, ['precio' => 50000])->assertForbidden();
        $this->deleteJson('/api/tarifas/'.$tarifa->id)->assertForbidden();
        expect((float) $tarifa->fresh()->precio)->toBe(45000.0);
    }
})->with(['agente', 'prestador', 'admin']);

it('exige autenticacion para consultar tarifas', function () {
    $this->getJson('/api/tarifas')->assertUnauthorized();
});

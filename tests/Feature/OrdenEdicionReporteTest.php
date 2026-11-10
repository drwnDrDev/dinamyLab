<?php

use App\Estado;
use App\Models\Empleado;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Resultado;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;

beforeEach(function () {
    $this->seed(PaisSeeder::class);
    $this->seed(TipoDocumentoSeeder::class);
    $this->seed(TestDataSeeder::class);
    $this->seed(RolesYPermisosSeeder::class);

    $this->user = User::factory()->create();
    $this->user->assignRole('prestador');
    $this->sede = Sede::factory()->create();
    $empleado = Empleado::factory()->create([
        'user_id' => $this->user->id,
        'empresa_id' => $this->sede->empresa_id,
        'tipo_documento_id' => 1,
    ]);
    $empleado->sedes()->attach($this->sede->id);
    $this->withSession(['sede' => $this->sede]);
});

function crearOrdenParaEdicion($test, Examen $examen, string $estado = 'en proceso'): Orden
{
    $orden = Orden::create([
        'sede_id' => $test->sede->id,
        'numero' => 872,
        'paciente_id' => Persona::factory()->create()->id,
        'user_id' => $test->user->id,
        'total' => $examen->valor,
    ]);
    $orden->examenes()->attach($examen->id, ['cantidad' => 1]);
    $orden->procedimientos()->create([
        'examen_id' => $examen->id,
        'fecha' => now(),
        'sede_id' => $test->sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => $estado,
    ]);

    return $orden;
}

it('permite editar examenes y destinos, y agregar examenes y remisiones', function () {
    $examenExistente = Examen::factory()->create(['valor' => 100]);
    $examenNuevo = Examen::factory()->create(['valor' => 50]);
    $orden = crearOrdenParaEdicion($this, $examenExistente);
    $remision = $orden->remisiones()->create([
        'cup' => '890201',
        'nombre' => 'Consulta externa',
        'valor' => 25,
        'destino' => 'IPS anterior',
    ]);

    $this->actingAs($this->user)
        ->put(route('ordenes.update', $orden), [
            'examenes' => [$examenExistente->id => 1],
            'examenes_nuevos' => [['id' => $examenNuevo->id, 'cantidad' => 2]],
            'remisiones' => [$remision->id => [
                'id' => $remision->id,
                'destino' => 'IPS actualizada',
            ]],
            'nuevas_remisiones' => [[
                'cup' => '902210',
                'nombre' => 'Estudio remitido',
                'valor' => 30,
                'destino' => 'Nueva IPS',
            ]],
            'abono' => 0,
            'observaciones' => 'Orden actualizada',
        ])
        ->assertRedirect(route('ordenes.show', $orden));

    expect($orden->fresh()->total)->toBe('255.00');
    expect($orden->procedimientos()->count())->toBe(3);
    expect($remision->fresh()->destino)->toBe('IPS actualizada');
    expect($orden->remisiones()->count())->toBe(2);
});

it('impide quitar un procedimiento terminado y conserva sus resultados', function () {
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $procedimiento = $orden->procedimientos()->firstOrFail();
    $parametro = Parametro::create([
        'nombre' => 'Hemoglobina',
        'slug' => 'hemoglobina',
        'tipo_dato' => 'number',
        'examen_id' => $examen->id,
    ]);
    $resultado = Resultado::create([
        'parametro_id' => $parametro->id,
        'procedimiento_id' => $procedimiento->id,
        'resultado' => '13.5',
    ]);

    $this->actingAs($this->user)
        ->from(route('ordenes.edit', $orden))
        ->put(route('ordenes.update', $orden), [
            'examenes' => [$examen->id => 0],
            'abono' => 0,
            'observaciones' => null,
        ])
        ->assertRedirect(route('ordenes.edit', $orden))
        ->assertSessionHasErrors(["examenes.{$examen->id}"]);

    expect($orden->procedimientos()->count())->toBe(1);
    expect($orden->examenes()->count())->toBe(1);
    expect($resultado->fresh())->not->toBeNull();
});

it('muestra el reporte agrupado de una orden', function () {
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaEdicion($this, $examen, Estado::TERMINADO->value);
    $procedimiento = $orden->procedimientos()->firstOrFail();
    $parametro = Parametro::create([
        'nombre' => 'Hemoglobina',
        'slug' => 'hemoglobina',
        'tipo_dato' => 'number',
        'examen_id' => $examen->id,
    ]);
    Resultado::create([
        'parametro_id' => $parametro->id,
        'procedimiento_id' => $procedimiento->id,
        'resultado' => '13.5',
    ]);

    $this->actingAs($this->user)
        ->get(route('resultados.reporte_orden', $orden))
        ->assertOk()
        ->assertSee("Resultados de orden {$orden->numero}")
        ->assertSee($examen->nombre)
        ->assertSee('Hemoglobina')
        ->assertSee('13.5')
        ->assertSee(route('resultados.show', $procedimiento));
});

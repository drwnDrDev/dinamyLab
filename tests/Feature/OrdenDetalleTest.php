<?php

use App\Estado;
use App\Models\Empleado;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Persona;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PaisSeeder::class);
    $this->seed(TipoDocumentoSeeder::class);
    $this->seed(TestDataSeeder::class);
    $this->seed(RolesYPermisosSeeder::class);

    $this->sede = Sede::factory()->create();
    $this->user = User::factory()->create(['email_verified_at' => now()]);
    $this->user->assignRole('admin');
    $empleado = Empleado::factory()->create([
        'user_id' => $this->user->id,
        'empresa_id' => $this->sede->empresa_id,
    ]);
    $empleado->sedes()->attach($this->sede->id);
});

function ordenDetalle($test, array $estados, array $datos = []): Orden
{
    $orden = Orden::create([
        'sede_id' => $test->sede->id,
        'numero' => 4521,
        'paciente_id' => Persona::factory()->create()->id,
        'user_id' => $test->user->id,
        'total' => 113000,
    ] + $datos);

    foreach ($estados as $estado) {
        $orden->procedimientos()->create([
            'examen_id' => Examen::factory()->create()->id,
            'fecha' => now(),
            'sede_id' => $test->sede->id,
            'diagnostico_principal' => 'Z017',
            'codigo_finalidad' => '15',
            'codigo_modalidad' => '01',
            'codigo_via_ingreso' => '01',
            'estado' => $estado->value,
        ]);
    }

    return $orden;
}

it('muestra el pago en pesos colombianos con saldo pendiente', function () {
    $orden = ordenDetalle($this, [Estado::PROCESO], ['abono' => 13000]);

    $this->actingAs($this->user)->withSession(['sede' => $this->sede])
        ->get(route('ordenes.show', $orden))
        ->assertOk()
        ->assertSee('Orden #4521')
        ->assertSeeInOrder(['Subtotal', '$113.000', 'Total', '$113.000', 'Abonado', '$13.000', 'Saldo pendiente', '$100.000'])
        ->assertDontSee('Descuento');
});

it('muestra las observaciones reales de la orden', function () {
    $conObservacion = ordenDetalle($this, [Estado::PROCESO], ['observaciones' => 'Paciente en ayunas']);

    $this->actingAs($this->user)->withSession(['sede' => $this->sede])
        ->get(route('ordenes.show', $conObservacion))
        ->assertSee('Paciente en ayunas')
        ->assertDontSee('hola como estas');

    $sinObservacion = ordenDetalle($this, [Estado::PROCESO]);
    $this->get(route('ordenes.show', $sinObservacion))->assertSee('Sin observaciones.');
});

it('muestra el avance de los exámenes y las acciones agrupadas', function () {
    $orden = ordenDetalle($this, [Estado::TERMINADO, Estado::ENTREGADO, Estado::PROCESO]);

    $this->actingAs($this->user)->withSession(['sede' => $this->sede])
        ->get(route('ordenes.show', $orden))
        ->assertSee('2 de 3 listos')
        ->assertSee(route('resultados.reporte_orden', $orden), false)
        ->assertSee(route('resultados.reporte_pdf', $orden), false)
        ->assertSee(route('ordenes.create', $orden->paciente_id), false)
        ->assertSee('Nueva orden para el paciente');
});

it('avisa cuando una remisión completaría o reabriría la orden', function () {
    $orden = ordenDetalle($this, [Estado::TERMINADO]);
    $remision = $orden->remisiones()->create([
        'cup' => '906492', 'nombre' => 'TSH', 'valor' => 25000, 'destino' => 'Laboratorio externo',
    ]);

    expect($orden->fresh()->load('procedimientos', 'remisiones')->remisionEsLoUltimoPendiente($remision))->toBeTrue();

    $this->actingAs($this->user)->withSession(['sede' => $this->sede])
        ->get(route('ordenes.show', $orden))
        ->assertSee('data-completa="1"', false)
        ->assertSee('data-reabre="0"', false)
        ->assertSee('$25.000');

    $remision->update(['estado' => Estado::TERMINADO]);
    expect($orden->fresh()->terminada)->not->toBeNull();

    $this->get(route('ordenes.show', $orden))
        ->assertSee('data-completa="0"', false)
        ->assertSee('data-reabre="1"', false);
});

it('no avisa de completar la orden si aún quedan exámenes pendientes', function () {
    $orden = ordenDetalle($this, [Estado::PROCESO]);
    $remision = $orden->remisiones()->create([
        'cup' => '906492', 'nombre' => 'TSH', 'valor' => 25000, 'destino' => 'Laboratorio externo',
    ]);

    expect($orden->fresh()->load('procedimientos', 'remisiones')->remisionEsLoUltimoPendiente($remision))->toBeFalse();
});

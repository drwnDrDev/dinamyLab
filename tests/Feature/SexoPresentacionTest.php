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

function crearOrdenParaPresentacion(Examen $examen, Persona $paciente, Sede $sede, User $user): Orden
{
    $orden = Orden::create([
        'sede_id' => $sede->id,
        'numero' => 991,
        'paciente_id' => $paciente->id,
        'user_id' => $user->id,
        'total' => $examen->valor,
    ]);

    $procedimiento = $orden->procedimientos()->create([
        'examen_id' => $examen->id,
        'fecha' => now(),
        'sede_id' => $sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => Estado::TERMINADO->value,
    ]);

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

    return $orden;
}

it('muestra indeterminado o intersexual en el reporte y la historia', function () {
    $paciente = Persona::factory()->create([
        'sexo' => 'I',
        'fecha_nacimiento' => '1990-01-01',
    ]);
    $examen = Examen::factory()->create();
    $orden = crearOrdenParaPresentacion($examen, $paciente, $this->sede, $this->user);

    $this->actingAs($this->user)
        ->get(route('resultados.reporte_orden', $orden))
        ->assertOk()
        ->assertSee('Indeterminado o Intersexual')
        ->assertDontSee('Femenino');

    $this->actingAs($this->user)
        ->get(route('personas.show', $paciente))
        ->assertOk()
        ->assertSee('Indeterminado o Intersexual')
        ->assertDontSee('Femenino');
});

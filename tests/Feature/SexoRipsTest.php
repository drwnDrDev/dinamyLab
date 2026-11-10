<?php

use App\Estado;
use App\Models\Empleado;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Persona;
use App\Models\Procedimiento;
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
    $this->user->assignRole('admin');
    $this->sede = Sede::factory()->create();
    $empleado = Empleado::factory()->create([
        'user_id' => $this->user->id,
        'empresa_id' => $this->sede->empresa_id,
        'tipo_documento_id' => 1,
    ]);
    $empleado->sedes()->attach($this->sede->id);
    $this->withSession(['sede' => $this->sede]);
});

function crearProcedimientoRips(Persona $paciente, Examen $examen, Sede $sede, User $user, int $numero): void
{
    $orden = Orden::create([
        'sede_id' => $sede->id,
        'numero' => $numero,
        'paciente_id' => $paciente->id,
        'user_id' => $user->id,
        'total' => $examen->valor,
    ]);

    Procedimiento::create([
        'orden_id' => $orden->id,
        'examen_id' => $examen->id,
        'fecha' => now(),
        'sede_id' => $sede->id,
        'diagnostico_principal' => 'Z017',
        'codigo_finalidad' => '15',
        'codigo_modalidad' => '01',
        'codigo_via_ingreso' => '01',
        'estado' => Estado::TERMINADO->value,
    ]);
}

it('exporta codSexo RIPS para personas M F e I', function () {
    $examen = Examen::factory()->create();

    $personaM = Persona::factory()->create(['sexo' => 'M', 'fecha_nacimiento' => '1990-01-01']);
    $personaF = Persona::factory()->create(['sexo' => 'F', 'fecha_nacimiento' => '1990-01-01']);
    $personaI = Persona::factory()->create(['sexo' => 'I', 'fecha_nacimiento' => '1990-01-01']);

    crearProcedimientoRips($personaM, $examen, $this->sede, $this->user, 901);
    crearProcedimientoRips($personaF, $examen, $this->sede, $this->user, 902);
    crearProcedimientoRips($personaI, $examen, $this->sede, $this->user, 903);

    $response = $this->actingAs($this->user)->post(route('descargar.rips'), [
        'sede_id' => $this->sede->id,
        'fecha_inicio' => now()->startOfDay()->toDateTimeString(),
        'fecha_fin' => now()->endOfDay()->toDateTimeString(),
    ]);

    $response->assertOk();

    $payload = json_decode($response->getContent(), true);
    $usuarios = collect($payload['usuarios'])->keyBy('numDocumentoIdentificacion');

    expect($usuarios[$personaM->numero_documento]['codSexo'])->toBe('H');
    expect($usuarios[$personaF->numero_documento]['codSexo'])->toBe('M');
    expect($usuarios[$personaI->numero_documento]['codSexo'])->toBe('I');
});

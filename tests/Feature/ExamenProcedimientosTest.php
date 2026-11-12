<?php

namespace Tests\Feature;

use App\Models\Examen;
use App\Models\Orden;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamenProcedimientosTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_examen_tiene_muchos_procedimientos(): void
    {
        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TestDataSeeder::class);

        $user = User::factory()->create();
        $sede = Sede::factory()->create();
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create();

        $orden = Orden::create([
            'sede_id' => $sede->id,
            'numero' => 1001,
            'paciente_id' => $paciente->id,
            'total' => 0,
            'user_id' => $user->id,
        ]);

        $procedimiento = Procedimiento::create([
            'orden_id' => $orden->id,
            'examen_id' => $examen->id,
            'sede_id' => $sede->id,
            'fecha' => now(),
            'diagnostico_principal' => 'Z017',
            'codigo_finalidad' => '14',
            'codigo_modalidad' => '01',
            'codigo_via_ingreso' => '01',
        ]);

        $this->assertTrue($examen->procedimientos->contains($procedimiento));
        $this->assertTrue($procedimiento->examen->is($examen));
        $this->assertCount(1, $examen->fresh()->procedimientos);
    }

    public function test_no_incluye_procedimientos_eliminados(): void
    {
        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TestDataSeeder::class);

        $user = User::factory()->create();
        $sede = Sede::factory()->create();
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create();

        $orden = Orden::create([
            'sede_id' => $sede->id,
            'numero' => 1002,
            'paciente_id' => $paciente->id,
            'total' => 0,
            'user_id' => $user->id,
        ]);

        $procedimiento = Procedimiento::create([
            'orden_id' => $orden->id,
            'examen_id' => $examen->id,
            'sede_id' => $sede->id,
            'fecha' => now(),
            'diagnostico_principal' => 'Z017',
            'codigo_finalidad' => '14',
            'codigo_modalidad' => '01',
            'codigo_via_ingreso' => '01',
        ]);

        $procedimiento->delete();

        $this->assertCount(0, $examen->fresh()->procedimientos);
    }
}

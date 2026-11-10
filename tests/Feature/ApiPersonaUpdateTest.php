<?php

namespace Tests\Feature;

use App\Models\Persona;
use Database\Seeders\PaisSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiPersonaUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->withoutMiddleware();
    }

    public function test_actualiza_solo_las_propiedades_enviadas_y_conserva_las_omitidas(): void
    {
        $persona = Persona::factory()->create([
            'primer_nombre' => 'Nombre',
            'primer_apellido' => 'Apellido',
            'fecha_nacimiento' => '1990-01-01',
        ]);
        $persona->telefonos()->create(['numero' => '3001112233']);

        $this->putJson("/api/personas/{$persona->id}", [
            'primer_nombre' => 'Actualizado',
            'telefono' => '3001234567',
            'direccion' => 'Calle 10',
        ])
            ->assertOk()
            ->assertJsonPath('data.primer_nombre', 'Actualizado')
            ->assertJsonPath('data.primer_apellido', 'Apellido');

        $this->assertDatabaseHas('telefonos', [
            'telefonoable_id' => $persona->id,
            'telefonoable_type' => Persona::class,
            'numero' => '3001234567',
        ]);
        $this->assertDatabaseHas('direcciones', [
            'direccionable_id' => $persona->id,
            'direccionable_type' => Persona::class,
            'direccion' => 'Calle 10',
        ]);

        $this->putJson("/api/personas/{$persona->id}", [
            'codigo_postal' => '110111',
        ])->assertOk();

        $this->assertDatabaseHas('direcciones', [
            'direccionable_id' => $persona->id,
            'direccionable_type' => Persona::class,
            'direccion' => 'Calle 10',
            'codigo_postal' => '110111',
        ]);
    }

    public function test_actualiza_los_campos_del_contacto_de_emergencia(): void
    {
        $persona = Persona::factory()->create();
        $acompanante = Persona::factory()->create();

        $this->putJson("/api/personas/{$persona->id}", [
            'contacto_emergencia' => [
                'acompanante_id' => $acompanante->id,
                'parentesco' => 'Hermana',
            ],
        ])->assertOk();

        $this->assertDatabaseHas('contacto_emergencias', [
            'paciente_id' => $persona->id,
            'acompanante_id' => $acompanante->id,
            'parentesco' => 'Hermana',
        ]);

        $this->putJson("/api/personas/{$persona->id}", [
            'contacto_emergencia' => ['parentesco' => 'Madre'],
        ])->assertOk();

        $this->assertDatabaseHas('contacto_emergencias', [
            'paciente_id' => $persona->id,
            'acompanante_id' => $acompanante->id,
            'parentesco' => 'Madre',
        ]);
    }

    public function test_rechaza_el_cambio_del_numero_de_documento(): void
    {
        $persona = Persona::factory()->create(['numero_documento' => '1234567890']);

        $this->putJson("/api/personas/{$persona->id}", [
            'numero_documento' => '0987654321',
        ])
            ->assertBadRequest()
            ->assertJsonPath('message', 'El número de documento no puede ser modificado');
    }
}

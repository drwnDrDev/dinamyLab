<?php

namespace Tests\Feature;

use App\Models\Persona;
use Database\Seeders\PaisSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersonaGrupoSanguineoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->withoutMiddleware();
    }

    public function test_crea_personas_con_y_sin_datos_sanguineos(): void
    {
        foreach ([['grupo_sanguineo' => 'AB', 'factor_rh' => 'negativo'], []] as $indice => $sangre) {
            $this->postJson('/api/personas', $sangre + [
                'nombres' => 'Ana',
                'apellidos' => 'Pérez',
                'tipo_documento' => 'CC',
                'numero_documento' => '123456789'.$indice,
                'fecha_nacimiento' => '1990-01-01',
                'sexo' => 'F',
                'perfil' => 'Paciente',
                'telefono' => '3001234567',
                'municipio' => null,
                'direccion' => null,
                'correo' => null,
            ])->assertCreated()
                ->assertJsonPath('data.grupo_sanguineo', $sangre['grupo_sanguineo'] ?? null)
                ->assertJsonPath('data.factor_rh', $sangre['factor_rh'] ?? null);
        }
        $this->assertDatabaseCount('personas', 2);
        $this->assertDatabaseCount('telefonos', 2);
    }

    public function test_api_actualiza_conserva_y_permite_borrar_los_datos(): void
    {
        $persona = Persona::factory()->create(['grupo_sanguineo' => 'A', 'factor_rh' => 'positivo']);
        $url = "/api/personas/{$persona->id}";
        $this->putJson($url, ['grupo_sanguineo' => 'O'])->assertOk()
            ->assertJsonPath('data.grupo_sanguineo', 'O')
            ->assertJsonPath('data.factor_rh', 'positivo');
        $this->putJson($url, ['grupo_sanguineo' => null, 'factor_rh' => null])->assertOk();
        $this->assertDatabaseHas('personas', ['id' => $persona->id, 'grupo_sanguineo' => null, 'factor_rh' => null]);
    }

    public function test_rechaza_grupos_y_factores_invalidos(): void
    {
        $persona = Persona::factory()->create();
        $this->putJson("/api/personas/{$persona->id}", ['grupo_sanguineo' => 'C', 'factor_rh' => 'desconocido'])
            ->assertUnprocessable()->assertJsonValidationErrors(['grupo_sanguineo', 'factor_rh']);
    }

    public function test_edicion_web_guarda_la_sangre_y_el_telefono_en_su_relacion(): void
    {
        // La edición web necesita el enlace de modelos de ruta y la conversión de vacíos a null.
        $this->withMiddleware();
        $this->seed(\Database\Seeders\RolesYPermisosSeeder::class);
        $usuario = \App\Models\User::factory()->create();
        $usuario->assignRole('prestador');
        $this->actingAs($usuario);
        $persona = Persona::factory()->create();
        $persona->telefonos()->create(['numero' => '3001112233']);
        $datos = [
            'primer_nombre' => $persona->primer_nombre,
            'primer_apellido' => $persona->primer_apellido,
            'grupo_sanguineo' => 'B',
            'factor_rh' => 'negativo',
            'telefono' => '3007654321',
        ];
        $this->put(route('personas.update', $persona), $datos)->assertRedirect();
        $this->assertDatabaseHas('personas', ['id' => $persona->id, 'grupo_sanguineo' => 'B', 'factor_rh' => 'negativo']);
        $this->assertTrue($persona->telefonos()->where('numero', '3007654321')->exists());

        $this->put(route('personas.update', $persona), array_merge($datos, [
            'grupo_sanguineo' => '', 'factor_rh' => '', 'telefono' => '',
        ]))->assertRedirect();
        $this->assertNull($persona->fresh()->grupo_sanguineo);
        $this->assertNull($persona->fresh()->factor_rh);
        $this->assertTrue($persona->telefonos()->where('numero', '3001112233')->exists());
    }
}

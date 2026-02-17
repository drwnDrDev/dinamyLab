<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Convenio;
use App\Models\Examen;
use App\Models\Sede;
use App\Models\Tarifa;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamenesPrecioApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $examen;
    protected $convenio;
    protected $sede;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar seeders necesarios
        $this->seed(\Database\Seeders\PaisSeeder::class);
        $this->seed(\Database\Seeders\TipoDocumentoSeeder::class);
        $this->seed(\Database\Seeders\TestDataSeeder::class);

        // Crear usuario y datos de prueba
        $this->user = User::factory()->create();
        $this->examen = Examen::factory()->create(['valor' => 100000]);
        $this->convenio = Convenio::factory()->create();
        $this->sede = Sede::factory()->create();
    }

    /**
     * Prueba: Endpoint retorna precio de tarifa por convenio + sede
     */
    public function test_obtener_precio_with_convenio_y_sede_tarifa()
    {
        // Crear tarifa específica
        $tarifa = Tarifa::create([
            'precio' => 80000,
            'tarifable_type' => 'App\Models\Examen',
            'tarifable_id' => $this->examen->id,
            'sede_id' => $this->sede->id,
        ]);

        $this->convenio->tarifas()->attach($tarifa->id);

        $response = $this->actingAs($this->user, 'sanctum')->getJson(
            '/api/examenes/precio',
            [
                'examen_id' => $this->examen->id,
                'convenio_id' => $this->convenio->id,
                'sede_id' => $this->sede->id,
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Precio obtenido exitosamente',
                'data' => [
                    'examen_id' => $this->examen->id,
                    'examen_nombre' => $this->examen->nombre,
                    'valor_base' => 100000,
                    'precio_final' => 80000,
                    'tipo_tarifa' => 'por_convenio_y_sede',
                    'convenio_id' => $this->convenio->id,
                    'sede_id' => $this->sede->id,
                ]
            ]);
    }

    /**
     * Prueba: Endpoint retorna precio base cuando no existe tarifa
     */
    public function test_obtener_precio_returns_precio_base()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson(
            '/api/examenes/precio',
            [
                'examen_id' => $this->examen->id,
                'convenio_id' => $this->convenio->id,
                'sede_id' => $this->sede->id,
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Precio obtenido exitosamente',
                'data' => [
                    'precio_final' => 100000,
                    'tipo_tarifa' => 'precio_base',
                ]
            ]);
    }

    /**
     * Prueba: Valida que examen_id sea requerido
     */
    public function test_obtener_precio_requires_examen_id()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson(
            '/api/examenes/precio',
            [
                'convenio_id' => $this->convenio->id,
                'sede_id' => $this->sede->id,
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors('examen_id');
    }

    /**
     * Prueba: Valida que convenio_id sea requerido
     */
    public function test_obtener_precio_requires_convenio_id()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson(
            '/api/examenes/precio',
            [
                'examen_id' => $this->examen->id,
                'sede_id' => $this->sede->id,
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors('convenio_id');
    }

    /**
     * Prueba: Valida que sede_id sea requerido
     */
    public function test_obtener_precio_requires_sede_id()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson(
            '/api/examenes/precio',
            [
                'examen_id' => $this->examen->id,
                'convenio_id' => $this->convenio->id,
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors('sede_id');
    }

    /**
     * Prueba: Valida que los IDs sean válidos (existan en base de datos)
     */
    public function test_obtener_precio_with_invalid_examen_id()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson(
            '/api/examenes/precio',
            [
                'examen_id' => 99999,
                'convenio_id' => $this->convenio->id,
                'sede_id' => $this->sede->id,
            ]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors('examen_id');
    }

    /**
     * Prueba: Retorna estructura completa de datos
     */
    public function test_obtener_precio_returns_complete_structure()
    {
        $response = $this->actingAs($this->user, 'sanctum')->getJson(
            '/api/examenes/precio',
            [
                'examen_id' => $this->examen->id,
                'convenio_id' => $this->convenio->id,
                'sede_id' => $this->sede->id,
            ]
        );

        $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'examen_id',
                    'examen_nombre',
                    'valor_base',
                    'precio_final',
                    'tipo_tarifa',
                    'tarifa_id',
                    'convenio_id',
                    'convenio_nombre',
                    'sede_id',
                    'sede_nombre',
                ]
            ]);
    }

    /**
     * Prueba: Retorna tarifa por sede cuando no hay tarifa de convenio
     */
    public function test_obtener_precio_returns_tarifa_by_sede()
    {
        $tarifa = Tarifa::create([
            'precio' => 90000,
            'tarifable_type' => 'App\Models\Examen',
            'tarifable_id' => $this->examen->id,
            'sede_id' => $this->sede->id,
        ]);

        // No vincular tarifa al convenio, solo crear por sede

        $response = $this->actingAs($this->user, 'sanctum')->getJson(
            '/api/examenes/precio',
            [
                'examen_id' => $this->examen->id,
                'convenio_id' => $this->convenio->id,
                'sede_id' => $this->sede->id,
            ]
        );

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'precio_final' => 90000,
                    'tipo_tarifa' => 'por_sede',
                ]
            ]);
    }
}

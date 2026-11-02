<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Persona;
use App\Models\Examen;
use App\Models\Sede;
use App\Models\Orden;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrdenesApiTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $sede;

    protected function setUp(): void
    {
        parent::setUp();

        // Ejecutar seeders necesarios para las foreign keys
        $this->seed(\Database\Seeders\PaisSeeder::class);
        $this->seed(\Database\Seeders\TipoDocumentoSeeder::class);
        $this->seed(\Database\Seeders\TestDataSeeder::class);

        $this->user = User::factory()->create();
        $this->sede = Sede::factory()->create();
        session(['sede' => $this->sede]);
    }

    public function test_can_create_orden_with_valid_data(): void
    {
        $paciente = Persona::factory()->create();
        $examen1 = Examen::factory()->create(['valor' => 100]);
        $examen2 = Examen::factory()->create(['valor' => 50]);

        $orderData = [
            'paciente_id' => $paciente->id,
            'numero_orden' => '12345',
            'fecha_orden' => now()->format('Y-m-d'),
            'examenes' => [
                [
                    'id' => $examen1->id,
                    'cantidad' => 1,
                    'valor' => 100
                ],
                [
                    'id' => $examen2->id,
                    'cantidad' => 2,
                    'valor' => 50
                ],
            ],
            'modalidad' => '01',
            'finalidad' => '15',
            'via_ingreso' => '01',
            'cie_principal' => 'Z017',
            'cie_relacionado' => null,
            'observaciones' => 'Test observation',
            'abono' => 0,
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', $orderData);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Orden creada correctamente.',
            ])
            ->assertJsonStructure([
                'message',
                'data' => [
                    'id',
                    'numero',
                    'paciente_id',
                    'total',
                ]
            ]);

        $this->assertDatabaseHas('ordenes_medicas', [
            'numero' => '12345',
            'paciente_id' => $paciente->id,
            'total' => 200,
        ]);
    }

    public function test_cannot_create_orden_without_authentication(): void
    {
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create();

        $orderData = [
            'paciente_id' => $paciente->id,
            'numero_orden' => '12345',
            'fecha_orden' => now()->format('Y-m-d'),
            'examenes' => [
                [
                    'id' => $examen->id,
                    'cantidad' => 1,
                    'valor' => 100
                ],
            ],
            'modalidad' => '01',
            'finalidad' => '15',
            'via_ingreso' => '01',
        ];

        $response = $this->postJson('/api/ordenes', $orderData);

        $response->assertStatus(401);
    }

    public function test_validation_error_when_paciente_id_is_missing(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'numero_orden' => '12345',
                'fecha_orden' => now()->format('Y-m-d'),
                'examenes' => [],
                'modalidad' => '01',
                'finalidad' => '15',
                'via_ingreso' => '01',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['paciente_id']);
    }

    public function test_validation_error_when_numero_orden_is_missing(): void
    {
        $paciente = Persona::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'fecha_orden' => now()->format('Y-m-d'),
                'examenes' => [],
                'modalidad' => '01',
                'finalidad' => '15',
                'via_ingreso' => '01',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['numero_orden']);
    }

    public function test_validation_error_when_examenes_is_missing(): void
    {
        $paciente = Persona::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => '12345',
                'fecha_orden' => now()->format('Y-m-d'),
                'modalidad' => '01',
                'finalidad' => '15',
                'via_ingreso' => '01',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['examenes']);
    }

    public function test_validation_error_when_required_fields_are_missing(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'paciente_id',
                'numero_orden',
                'fecha_orden',
                'examenes',
                'modalidad',
                'finalidad',
                'via_ingreso',
            ]);
    }

    public function test_orden_creates_procedimientos_for_each_examen(): void
    {
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create(['valor' => 100]);

        $orderData = [
            'paciente_id' => $paciente->id,
            'numero_orden' => '12345',
            'fecha_orden' => now()->format('Y-m-d'),
            'examenes' => [
                [
                    'id' => $examen->id,
                    'cantidad' => 3,
                    'valor' => 100
                ],
            ],
            'modalidad' => '01',
            'finalidad' => '15',
            'via_ingreso' => '01',
            'cie_principal' => 'Z017',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', $orderData);

        $response->assertStatus(201);

        $orden = Orden::where('numero', '12345')->first();
        $this->assertNotNull($orden);

        // Verificar que se crearon 3 procedimientos
        $this->assertDatabaseCount('procedimientos', 3);
        $this->assertDatabaseHas('procedimientos', [
            'orden_id' => $orden->id,
            'examen_id' => $examen->id,
            'diagnostico_principal' => 'Z017',
        ]);
    }

    public function test_orden_associates_examenes_correctly(): void
    {
        $paciente = Persona::factory()->create();
        $examen1 = Examen::factory()->create(['valor' => 100]);
        $examen2 = Examen::factory()->create(['valor' => 50]);

        $orderData = [
            'paciente_id' => $paciente->id,
            'numero_orden' => '12345',
            'fecha_orden' => now()->format('Y-m-d'),
            'examenes' => [
                [
                    'id' => $examen1->id,
                    'cantidad' => 1,
                    'valor' => 100
                ],
                [
                    'id' => $examen2->id,
                    'cantidad' => 2,
                    'valor' => 50
                ],
            ],
            'modalidad' => '01',
            'finalidad' => '15',
            'via_ingreso' => '01',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', $orderData);

        $response->assertStatus(201);

        $orden = Orden::where('numero', '12345')->first();
        $this->assertCount(2, $orden->examenes);

        $this->assertTrue($orden->examenes->contains($examen1));
        $this->assertTrue($orden->examenes->contains($examen2));
    }

    public function test_validation_error_when_paciente_does_not_exist(): void
    {
        $examen = Examen::factory()->create();

        $orderData = [
            'paciente_id' => 99999,
            'numero_orden' => '12345',
            'fecha_orden' => now()->format('Y-m-d'),
            'examenes' => [
                [
                    'id' => $examen->id,
                    'cantidad' => 1,
                    'valor' => 100
                ],
            ],
            'modalidad' => '01',
            'finalidad' => '15',
            'via_ingreso' => '01',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['paciente_id']);
    }

    public function test_validation_error_when_examen_does_not_exist(): void
    {
        $paciente = Persona::factory()->create();

        $orderData = [
            'paciente_id' => $paciente->id,
            'numero_orden' => '12345',
            'fecha_orden' => now()->format('Y-m-d'),
            'examenes' => [
                [
                    'id' => 99999,
                    'cantidad' => 1,
                    'valor' => 100
                ],
            ],
            'modalidad' => '01',
            'finalidad' => '15',
            'via_ingreso' => '01',
        ];

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', $orderData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['examenes.0.id']);
    }

    public function test_can_create_orden_with_alphanumeric_number(): void
    {
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create(['valor' => 100]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => 'LAB-2026-A007',
                'fecha_orden' => now()->format('Y-m-d'),
                'examenes' => [['id' => $examen->id, 'cantidad' => 1, 'valor' => 100]],
                'modalidad' => '01',
                'finalidad' => '15',
                'via_ingreso' => '01',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.numero', 'LAB-2026-A007');

        $this->assertDatabaseHas('ordenes_medicas', [
            'numero' => 'LAB-2026-A007',
            'paciente_id' => $paciente->id,
        ]);
    }

    public function test_can_create_order_number_with_255_characters(): void
    {
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create();
        $numero = str_repeat('A', 255);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => $numero,
                'fecha_orden' => now()->format('Y-m-d'),
                'examenes' => [['id' => $examen->id, 'cantidad' => 1, 'valor' => 100]],
                'modalidad' => '01',
                'finalidad' => '15',
                'via_ingreso' => '01',
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.numero', $numero);
    }

    public function test_numero_orden_may_not_exceed_255_characters(): void
    {
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => str_repeat('A', 256),
                'fecha_orden' => now()->format('Y-m-d'),
                'examenes' => [['id' => $examen->id, 'cantidad' => 1, 'valor' => 100]],
                'modalidad' => '01',
                'finalidad' => '15',
                'via_ingreso' => '01',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['numero_orden']);
    }

    public function test_numero_orden_must_be_unique_within_the_same_sede(): void
    {
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create();
        Orden::create([
            'sede_id' => $this->sede->id,
            'numero' => 'LAB-001',
            'paciente_id' => $paciente->id,
            'user_id' => $this->user->id,
            'total' => 100,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => 'LAB-001',
                'fecha_orden' => now()->format('Y-m-d'),
                'examenes' => [['id' => $examen->id, 'cantidad' => 1, 'valor' => 100]],
                'modalidad' => '01',
                'finalidad' => '15',
                'via_ingreso' => '01',
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['numero_orden']);
    }

    public function test_suggested_order_number_increments_alphanumeric_sequence(): void
    {
        $paciente = Persona::factory()->create();
        Orden::create([
            'sede_id' => $this->sede->id,
            'numero' => 'LAB-009',
            'paciente_id' => $paciente->id,
            'user_id' => $this->user->id,
            'total' => 100,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/ordenes/max-numero');

        $response->assertOk()
            ->assertJsonPath('next_numero', 'LAB-010');
    }

    public function test_number_verification_reports_existing_number_and_suggestion(): void
    {
        $paciente = Persona::factory()->create();
        Orden::create([
            'sede_id' => $this->sede->id,
            'numero' => 'LAB-009',
            'paciente_id' => $paciente->id,
            'user_id' => $this->user->id,
            'total' => 100,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/ordenes/verificar-numero?numero_orden=LAB-009');

        $response->assertOk()
            ->assertJsonPath('usado', true)
            ->assertJsonPath('sugerencia', 'LAB-010');
    }
}

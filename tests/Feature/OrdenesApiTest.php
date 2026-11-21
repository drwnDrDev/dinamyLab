<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Persona;
use App\Models\Examen;
use App\Models\Sede;
use App\Models\Orden;
use App\Models\Empleado;
use Database\Seeders\RolesYPermisosSeeder;
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
        $this->seed(RolesYPermisosSeeder::class);

        $this->user = User::factory()->create();
        $this->sede = Sede::factory()->create();
        $this->user->assignRole('prestador');
        $empleado = Empleado::factory()->create([
            'user_id' => $this->user->id,
            'empresa_id' => $this->sede->empresa_id,
            'tipo_documento_id' => 1,
        ]);
        $empleado->sedes()->attach($this->sede->id);
        session(['sede' => $this->sede]);
    }

    public function test_el_checkbox_define_el_estado_inicial_de_examenes_y_remisiones(): void
    {
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create(['valor' => 100]);
        foreach ([true, false] as $pendiente) {
            $estado = $pendiente ? \App\Estado::PENDIENTE->value : \App\Estado::PROCESO->value;
            $respuesta = $this->actingAs($this->user, 'sanctum')->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => $pendiente ? 'MUESTRA-1' : 'MUESTRA-2',
                'fecha_orden' => now()->format('Y-m-d'),
                'pendiente_muestra' => $pendiente,
                'examenes' => [['id' => $examen->id, 'cantidad' => 2]],
                'remisiones' => [[
                    'cup' => '903895', 'nombre' => 'Creatinina', 'valor' => 20000, 'destino' => 'Laboratorio externo',
                ]],
            ])->assertCreated();
            $orden = Orden::findOrFail($respuesta->json('data.id'));
            $this->assertSame([$estado, $estado], $orden->procedimientos()->pluck('estado')->all());
            $this->assertSame($estado, $orden->remisiones()->firstOrFail()->estado->value);
            $this->assertNull($orden->terminada);
        }
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

    public function test_can_create_order_with_registered_and_manual_external_destinations(): void
    {
        $paciente = Persona::factory()->create();
        $ips = \App\Models\IpsExterna::create(['nombre' => 'Laboratorio externo']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => 'REM-001',
                'fecha_orden' => now()->format('Y-m-d'),
                'remisiones' => [
                    [
                        'cup' => '890201',
                        'nombre' => 'Consulta externa',
                        'valor' => 45000,
                        'ips_externa_id' => $ips->id,
                        'destino' => $ips->nombre,
                    ],
                    [
                        'cup' => '902210',
                        'nombre' => 'Estudio remitido',
                        'valor' => 25000,
                        'ips_externa_id' => null,
                        'destino' => 'IPS Externa',
                    ],
                ],
            ]);

        $response->assertCreated();

        $orden = Orden::where('numero', 'REM-001')->firstOrFail();
        $this->assertDatabaseHas('ordenes_medicas', [
            'id' => $orden->id,
            'total' => 70000,
        ]);
        $this->assertDatabaseCount('orden_remisiones', 2);
        $this->assertDatabaseHas('orden_remisiones', [
            'orden_id' => $orden->id,
            'cup' => '890201',
            'ips_externa_id' => $ips->id,
            'valor' => 45000,
        ]);
        $this->assertDatabaseHas('orden_remisiones', [
            'orden_id' => $orden->id,
            'cup' => '902210',
            'destino' => 'IPS Externa',
            'valor' => 25000,
        ]);
        $this->assertDatabaseCount('procedimientos', 0);
    }

    public function test_order_create_redirects_to_sede_selection_when_no_sede_is_active(): void
    {
        $this->actingAs($this->user)
            ->withSession(['sede' => null])
            ->get(route('ordenes.create'))
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('error', 'Selecciona una sede antes de crear una orden médica.');
    }

    public function test_remission_requires_a_destination(): void
    {
        $paciente = Persona::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => 'REM-002',
                'fecha_orden' => now()->format('Y-m-d'),
                'remisiones' => [
                    [
                        'cup' => '890201',
                        'nombre' => 'Consulta externa',
                        'valor' => 45000,
                    ],
                ],
            ]);

        $response->assertUnprocessable()
            ->assertJsonValidationErrors(['remisiones.0.destino']);
    }

    public function test_remission_value_is_added_to_exam_total(): void
    {
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create(['valor' => 100]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => 'REM-003',
                'fecha_orden' => now()->format('Y-m-d'),
                'examenes' => [['id' => $examen->id, 'cantidad' => 1]],
                'remisiones' => [[
                    'cup' => '902210',
                    'nombre' => 'Estudio remitido',
                    'valor' => 25000,
                    'destino' => 'IPS Externa',
                ]],
                'modalidad' => '01',
                'finalidad' => '15',
                'via_ingreso' => '01',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('ordenes_medicas', [
            'numero' => 'REM-003',
            'total' => 25100,
        ]);
    }

    public function test_catalog_remission_uses_server_data_and_preserves_the_chosen_price(): void
    {
        $this->seed(\Database\Seeders\ValorRemisionSeeder::class);
        $tarifa = \App\Models\Remision::where('cup', '903871')->firstOrFail()->valores->first();
        $paciente = Persona::factory()->create();

        foreach ([null, 28000, 0] as $index => $precio) {
            $datos = ['valor_remision_id' => $tarifa->id, 'cup' => 'FALSO', 'nombre' => 'Nombre falso',
                'destino' => 'Destino falso', 'valor_remitido' => 1, 'ips_externa_id' => null];
            if ($precio !== null) {
                $datos['valor'] = $precio;
            }
            $respuesta = $this->actingAs($this->user, 'sanctum')->postJson('/api/ordenes', [
                'paciente_id' => $paciente->id,
                'numero_orden' => "CAT-{$index}",
                'fecha_orden' => now()->format('Y-m-d'),
                'remisiones' => [$datos],
            ])->assertCreated();

            $this->assertDatabaseHas('ordenes_medicas', ['id' => $respuesta->json('data.id'), 'total' => $precio ?? 25000]);
            $this->assertDatabaseHas('orden_remisiones', [
                'orden_id' => $respuesta->json('data.id'), 'cup' => '903871', 'nombre' => 'Amilasa en Suero',
                'destino' => 'Idime', 'valor' => $precio ?? 25000, 'valor_remitido' => 10000,
                'ips_externa_id' => $tarifa->ips_externa_id, 'valor_remision_id' => $tarifa->id,
            ]);
        }

        $tarifa->update(['precio' => 99000, 'valor_remitido' => 55000]);
        $this->assertDatabaseHas('orden_remisiones', ['valor_remision_id' => $tarifa->id, 'valor' => 25000, 'valor_remitido' => 10000]);
        $this->actingAs($this->user, 'sanctum')->getJson('/api/orden/'.$respuesta->json('data.id'))
            ->assertOk()->assertJsonPath('remisiones.0.ips_externa.nombre', 'Idime');
    }

    public function test_catalog_remission_rejects_invalid_tariffs_and_negative_prices(): void
    {
        $this->seed(\Database\Seeders\ValorRemisionSeeder::class);
        $paciente = Persona::factory()->create();
        $tarifa = \App\Models\ValorRemision::firstOrFail();

        $this->actingAs($this->user, 'sanctum')->postJson('/api/ordenes', [
            'paciente_id' => $paciente->id, 'numero_orden' => 'CAT-INVALIDA', 'fecha_orden' => now()->format('Y-m-d'),
            'remisiones' => [['valor_remision_id' => 999999], ['valor_remision_id' => $tarifa->id, 'valor' => -1]],
        ])->assertUnprocessable()->assertJsonValidationErrors(['remisiones.0.valor_remision_id', 'remisiones.1.valor']);
        $this->assertDatabaseCount('ordenes_medicas', 0);
    }

    public function test_upgrade_removes_legacy_destination_without_losing_the_remission(): void
    {
        $orden = Orden::create([
            'sede_id' => $this->sede->id, 'numero' => 'MIG-001', 'user_id' => $this->user->id,
            'paciente_id' => Persona::factory()->create()->id, 'total' => 25000,
        ]);
        $remision = $orden->remisiones()->create(['cup' => '20261', 'nombre' => 'Gram', 'valor' => 25000, 'destino' => $this->sede->nombre]);
        $migracion = require database_path('migrations/2026_11_20_000100_update_orden_remisiones_catalogo.php');
        $migracion->down();
        \Illuminate\Support\Facades\DB::table('orden_remisiones')->where('id', $remision->id)->update(['destino_sede_id' => $this->sede->id]);

        $migracion->up();

        $this->assertFalse(\Illuminate\Support\Facades\Schema::hasColumn('orden_remisiones', 'destino_sede_id'));
        $this->assertDatabaseHas('orden_remisiones', ['id' => $remision->id, 'cup' => '20261', 'destino' => $this->sede->nombre, 'valor' => 25000]);
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

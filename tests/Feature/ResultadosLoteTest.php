<?php

namespace Tests\Feature;

use App\Estado;
use App\Models\Empleado;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultadosLoteTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Sede $sede;

    private Examen $examen;

    private Parametro $parametroNumero;

    private Parametro $parametroTexto;

    private array $procedimientos = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TestDataSeeder::class);

        $this->user = User::factory()->create();
        $this->sede = Sede::factory()->create();
        $empleado = Empleado::factory()->create([
            'user_id' => $this->user->id,
            'empresa_id' => $this->sede->empresa_id,
            'tipo_documento_id' => 1,
        ]);
        $empleado->sedes()->attach($this->sede->id);
        session(['sede' => $this->sede]);

        $this->examen = Examen::factory()->create();
        $this->parametroNumero = Parametro::create([
            'nombre' => 'Hemoglobina',
            'grupo' => 'HEMOGRAMA',
            'posicion' => 1,
            'slug' => 'hemoglobina',
            'tipo_dato' => 'number',
            'unidades' => 'g/dL',
            'examen_id' => $this->examen->id,
        ]);
        $this->parametroTexto = Parametro::create([
            'nombre' => 'Observación',
            'posicion' => 2,
            'slug' => 'observacion',
            'tipo_dato' => 'text',
            'examen_id' => $this->examen->id,
        ]);

        foreach (range(1, 2) as $index) {
            $paciente = Persona::factory()->create();
            $orden = Orden::create([
                'sede_id' => $this->sede->id,
                'numero' => 1000 + $index,
                'paciente_id' => $paciente->id,
                'total' => 0,
                'user_id' => $this->user->id,
            ]);
            $this->procedimientos[] = Procedimiento::create([
                'orden_id' => $orden->id,
                'examen_id' => $this->examen->id,
                'sede_id' => $this->sede->id,
                'fecha' => now(),
                'diagnostico_principal' => 'Z017',
                'codigo_finalidad' => '14',
                'codigo_modalidad' => '01',
                'codigo_via_ingreso' => '01',
                'estado' => Estado::PROCESO->value,
            ]);
        }
    }

    public function test_guarda_un_lote_en_una_sola_operacion_y_omite_campos_vacios(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            route('examenes.lote.store', $this->examen),
            [
                'procedimientos' => [
                    [
                        'id' => $this->procedimientos[0]->id,
                        'resultados' => [
                            $this->parametroNumero->id => '13.5',
                            $this->parametroTexto->id => '',
                        ],
                    ],
                    [
                        'id' => $this->procedimientos[1]->id,
                        'resultados' => [
                            $this->parametroNumero->id => '',
                            $this->parametroTexto->id => 'Resultado cualitativo',
                        ],
                    ],
                ],
            ]
        );

        $response->assertOk()
            ->assertJsonPath('cantidad', 2);

        $this->assertDatabaseHas('resultados', [
            'procedimiento_id' => $this->procedimientos[0]->id,
            'parametro_id' => $this->parametroNumero->id,
            'resultado' => '13.5',
        ]);
        $this->assertDatabaseHas('resultados', [
            'procedimiento_id' => $this->procedimientos[1]->id,
            'parametro_id' => $this->parametroTexto->id,
            'resultado' => 'Resultado cualitativo',
        ]);
        $this->assertDatabaseMissing('resultados', [
            'procedimiento_id' => $this->procedimientos[0]->id,
            'parametro_id' => $this->parametroTexto->id,
        ]);
        $this->assertDatabaseHas('procedimientos', [
            'id' => $this->procedimientos[0]->id,
            'estado' => Estado::TERMINADO->value,
            'empleado_id' => $this->user->empleado->id,
        ]);
        $this->assertDatabaseHas('procedimientos', [
            'id' => $this->procedimientos[1]->id,
            'estado' => Estado::TERMINADO->value,
        ]);
    }

    public function test_no_guarda_parte_del_lote_si_una_muestra_ya_no_es_elegible(): void
    {
        $this->procedimientos[1]->update(['estado' => Estado::TERMINADO->value]);

        $response = $this->actingAs($this->user)->postJson(
            route('examenes.lote.store', $this->examen),
            [
                'procedimientos' => [
                    [
                        'id' => $this->procedimientos[0]->id,
                        'resultados' => [$this->parametroNumero->id => '13.5'],
                    ],
                    [
                        'id' => $this->procedimientos[1]->id,
                        'resultados' => [$this->parametroNumero->id => '11.2'],
                    ],
                ],
            ]
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors('procedimientos');
        $this->assertDatabaseMissing('resultados', [
            'procedimiento_id' => $this->procedimientos[0]->id,
            'parametro_id' => $this->parametroNumero->id,
        ]);
        $this->assertDatabaseHas('procedimientos', [
            'id' => $this->procedimientos[0]->id,
            'estado' => Estado::PROCESO->value,
        ]);
    }

    public function test_rechaza_un_valor_numerico_no_valido_sin_cerrar_muestras(): void
    {
        $response = $this->actingAs($this->user)->postJson(
            route('examenes.lote.store', $this->examen),
            [
                'procedimientos' => [
                    [
                        'id' => $this->procedimientos[0]->id,
                        'resultados' => [$this->parametroNumero->id => 'no numérico'],
                    ],
                ],
            ]
        );

        $response->assertUnprocessable()
            ->assertJsonValidationErrors("procedimientos.0.resultados.{$this->parametroNumero->id}");
        $this->assertDatabaseHas('procedimientos', [
            'id' => $this->procedimientos[0]->id,
            'estado' => Estado::PROCESO->value,
        ]);
    }
}

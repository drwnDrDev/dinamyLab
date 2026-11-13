<?php

namespace Tests\Feature;

use App\Estado;
use App\Models\Empleado;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Models\ResultadoCambio;
use App\Models\Sede;
use App\Models\User;
use App\Models\ValorReferencia;
use Database\Seeders\PaisSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultadosEdicionTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Sede $sede;

    private Examen $examen;

    private Procedimiento $procedimiento;

    private Parametro $parametro;

    private Resultado $resultado;

    protected function setUp(): void
    {
        parent::setUp();

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
        session(['sede' => $this->sede]);

        $paciente = Persona::factory()->create();
        $this->examen = Examen::factory()->create();
        $this->parametro = Parametro::create([
            'nombre' => 'Hemoglobina',
            'posicion' => 1,
            'slug' => 'hemoglobina',
            'tipo_dato' => 'number',
            'unidades' => 'g/dL',
            'examen_id' => $this->examen->id,
        ]);
        $orden = Orden::create([
            'sede_id' => $this->sede->id,
            'numero' => 1001,
            'paciente_id' => $paciente->id,
            'total' => 0,
            'user_id' => $this->user->id,
        ]);
        $this->procedimiento = Procedimiento::create([
            'orden_id' => $orden->id,
            'examen_id' => $this->examen->id,
            'sede_id' => $this->sede->id,
            'fecha' => now(),
            'diagnostico_principal' => 'Z017',
            'codigo_finalidad' => '14',
            'codigo_modalidad' => '01',
            'codigo_via_ingreso' => '01',
            'estado' => Estado::TERMINADO->value,
        ]);
        $this->resultado = Resultado::create([
            'procedimiento_id' => $this->procedimiento->id,
            'parametro_id' => $this->parametro->id,
            'posicion' => 1,
            'resultado' => '12.5',
        ]);
    }

    public function test_requiere_motivo_para_modificar_resultados(): void
    {
        $response = $this->actingAs($this->user)
            ->from(route('resultados.edit', $this->procedimiento))
            ->put(route('resultados.update', $this->procedimiento), [
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertRedirect(route('resultados.edit', $this->procedimiento))
            ->assertSessionHasErrors('observacion');
        $this->assertDatabaseHas('resultados', [
            'id' => $this->resultado->id,
            'resultado' => '12.5',
        ]);
        $this->assertDatabaseCount('resultado_cambios', 0);
    }

    public function test_guarda_el_historial_del_cambio_con_actor_fecha_y_motivo(): void
    {
        $response = $this->actingAs($this->user)
            ->put(route('resultados.update', $this->procedimiento), [
                'observacion' => 'Corrección de transcripción del equipo.',
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertRedirect(route('resultados.show', $this->procedimiento));
        $this->assertDatabaseHas('resultados', [
            'id' => $this->resultado->id,
            'resultado' => '13.1',
        ]);
        $this->assertDatabaseHas('resultado_cambios', [
            'procedimiento_id' => $this->procedimiento->id,
            'resultado_id' => $this->resultado->id,
            'parametro_id' => $this->parametro->id,
            'usuario_id' => $this->user->id,
            'parametro_nombre' => 'Hemoglobina',
            'valor_anterior' => '12.5',
            'valor_nuevo' => '13.1',
            'observacion' => 'Corrección de transcripción del equipo.',
            'usuario_nombre' => $this->user->name,
        ]);
    }

    public function test_rechaza_parametro_que_no_pertenece_al_examen(): void
    {
        $otroExamen = Examen::factory()->create();
        $otroParametro = Parametro::create([
            'nombre' => 'Glucosa',
            'posicion' => 1,
            'slug' => 'glucosa',
            'tipo_dato' => 'number',
            'examen_id' => $otroExamen->id,
        ]);

        $response = $this->actingAs($this->user)
            ->from(route('resultados.edit', $this->procedimiento))
            ->put(route('resultados.update', $this->procedimiento), [
                'observacion' => 'Intento de actualización inválida.',
                'resultados' => [$otroParametro->id => '13.1'],
            ]);

        $response->assertRedirect(route('resultados.edit', $this->procedimiento))
            ->assertSessionHasErrors("resultados.{$otroParametro->id}");
        $this->assertDatabaseHas('resultados', [
            'id' => $this->resultado->id,
            'resultado' => '12.5',
        ]);
        $this->assertDatabaseCount('resultado_cambios', 0);
    }

    public function test_no_permite_editar_resultados_de_otra_sede(): void
    {
        $otraSede = Sede::factory()->create();
        $this->procedimiento->update(['sede_id' => $otraSede->id]);

        $response = $this->actingAs($this->user)
            ->put(route('resultados.update', $this->procedimiento), [
                'observacion' => 'Cambio de resultado.',
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('resultados', [
            'id' => $this->resultado->id,
            'resultado' => '12.5',
        ]);
        $this->assertDatabaseCount('resultado_cambios', 0);
    }

    public function test_rechaza_resultado_numerico_invalido(): void
    {
        $response = $this->actingAs($this->user)
            ->from(route('resultados.edit', $this->procedimiento))
            ->put(route('resultados.update', $this->procedimiento), [
                'observacion' => 'Corrección de resultado.',
                'resultados' => [$this->parametro->id => 'no es numérico'],
            ]);

        $response->assertRedirect(route('resultados.edit', $this->procedimiento))
            ->assertSessionHasErrors("resultados.{$this->parametro->id}");
        $this->assertDatabaseHas('resultados', [
            'id' => $this->resultado->id,
            'resultado' => '12.5',
        ]);
        $this->assertDatabaseCount('resultado_cambios', 0);
    }

    private function empleadoEnSede(string $rol): User
    {
        $usuario = User::factory()->create([
            'email' => \Illuminate\Support\Str::uuid().'@example.test',
        ]);
        $usuario->assignRole($rol);
        $empleado = Empleado::factory()->create([
            'user_id' => $usuario->id,
            'empresa_id' => $this->sede->empresa_id,
            'tipo_documento_id' => 1,
        ]);
        $empleado->sedes()->attach($this->sede->id);

        return $usuario;
    }

    public function test_la_edicion_muestra_valor_actual_limites_e_historial(): void
    {
        ValorReferencia::create([
            'parametro_id' => $this->parametro->id,
            'demografia' => 'general',
            'salida' => '12 - 16',
            'min' => 12,
            'max' => 16,
        ]);
        ResultadoCambio::create([
            'procedimiento_id' => $this->procedimiento->id,
            'resultado_id' => $this->resultado->id,
            'parametro_id' => $this->parametro->id,
            'usuario_id' => $this->user->id,
            'parametro_nombre' => 'Hemoglobina',
            'valor_anterior' => '11.0',
            'valor_nuevo' => '12.5',
            'observacion' => 'Repetición de la muestra.',
            'usuario_nombre' => $this->user->name,
        ]);

        $this->actingAs($this->user)
            ->get(route('resultados.edit', $this->procedimiento))
            ->assertOk()
            ->assertSee('data-original="12.5"', false)
            ->assertSee('data-min="12"', false)
            ->assertSee('data-max="16"', false)
            ->assertSee('Historial de modificaciones (1)')
            ->assertSee('Repetición de la muestra.')
            ->assertSee('Revisar cambios');
    }

    public function test_la_edicion_reabre_la_revision_cuando_falta_el_motivo(): void
    {
        $this->actingAs($this->user)
            ->from(route('resultados.edit', $this->procedimiento))
            ->put(route('resultados.update', $this->procedimiento), [
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $this->actingAs($this->user)
            ->withSession(['sede' => $this->sede])
            ->get(route('resultados.edit', $this->procedimiento))
            ->assertOk()
            ->assertSee('data-abrir-revision="true"', false);
    }

    public function test_el_resultado_muestra_editar_y_el_aviso_de_guardado_al_prestador(): void
    {
        $this->actingAs($this->user)
            ->followingRedirects()
            ->put(route('resultados.update', $this->procedimiento), [
                'observacion' => 'Corrección de transcripción del equipo.',
                'resultados' => [$this->parametro->id => '13.1'],
            ])
            ->assertOk()
            ->assertSee(route('resultados.edit', $this->procedimiento), false)
            ->assertSee('Se guardaron 1 cambio(s) en los resultados.');
    }

    public function test_el_resultado_no_muestra_editar_al_agente(): void
    {
        $agente = $this->empleadoEnSede('agente');

        $this->actingAs($agente)
            ->get(route('resultados.show', $this->procedimiento))
            ->assertOk()
            ->assertDontSee(route('resultados.edit', $this->procedimiento), false);
    }
}

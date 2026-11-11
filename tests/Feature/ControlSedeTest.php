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
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * C-05: control de sede al elegir sede y al guardar resultados.
 * Casos de aceptación SEDE-01 a SEDE-08 de docs/MANUAL.md §7.5.
 */
class ControlSedeTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Empleado $empleado;

    private Sede $sedeA;

    private Sede $sedeB;

    private Parametro $parametro;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TestDataSeeder::class);
        $this->seed(RolesYPermisosSeeder::class);

        $this->sedeA = Sede::factory()->create();
        $this->sedeB = Sede::factory()->create(['empresa_id' => $this->sedeA->empresa_id]);

        // Un usuario previo hace que user_id y empleado_id difieran (detecta si se guarda uno por otro).
        User::factory()->create(['email' => 'previo@example.com']);
        $this->user = User::factory()->create();
        $this->user->assignRole('prestador');
        $this->empleado = Empleado::factory()->create([
            'user_id' => $this->user->id,
            'empresa_id' => $this->sedeA->empresa_id,
            'tipo_documento_id' => 1,
        ]);
        $this->empleado->sedes()->attach($this->sedeA->id);

        $examen = Examen::factory()->create();
        $this->parametro = Parametro::create([
            'nombre' => 'Hemoglobina',
            'posicion' => 1,
            'slug' => 'hemoglobina',
            'tipo_dato' => 'number',
            'unidades' => 'g/dL',
            'examen_id' => $examen->id,
        ]);
    }

    private function crearProcedimiento(Sede $sede): Procedimiento
    {
        $orden = Orden::create([
            'sede_id' => $sede->id,
            'numero' => fake()->unique()->numberBetween(1000, 9999),
            'paciente_id' => Persona::factory()->create()->id,
            'total' => 0,
            'user_id' => $this->user->id,
        ]);

        return Procedimiento::create([
            'orden_id' => $orden->id,
            'examen_id' => $this->parametro->examen_id,
            'sede_id' => $sede->id,
            'fecha' => now(),
            'diagnostico_principal' => 'Z017',
            'codigo_finalidad' => '14',
            'codigo_modalidad' => '01',
            'codigo_via_ingreso' => '01',
            'estado' => Estado::PROCESO->value,
        ]);
    }

    private function assertProcedimientoSinCambios(Procedimiento $procedimiento): void
    {
        $this->assertDatabaseCount('resultados', 0);
        $this->assertDatabaseHas('procedimientos', [
            'id' => $procedimiento->id,
            'estado' => Estado::PROCESO->value,
            'empleado_id' => null,
        ]);
    }

    // ---- Elegir sede ----

    public function test_sede_01_empleado_no_puede_elegir_sede_ajena(): void
    {
        $response = $this->actingAs($this->user)
            ->withSession(['sede' => $this->sedeA])
            ->get(route('elegir.sede', $this->sedeB));

        $response->assertForbidden();
        $this->assertEquals($this->sedeA->id, session('sede')->id);
    }

    public function test_sede_02_usuario_sin_empleado_no_puede_elegir_sede(): void
    {
        $response = $this->actingAs(User::factory()->create(['email' => 'sin-empleado@example.com']))
            ->get(route('elegir.sede', $this->sedeA));

        $response->assertForbidden();
        $this->assertNull(session('sede'));
    }

    public function test_sede_03_empleado_elige_su_sede(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('elegir.sede', $this->sedeA));

        $response->assertRedirect(route('inicio'));
        $this->assertEquals($this->sedeA->id, session('sede')->id);
    }

    // ---- Ver formulario de captura ----

    public function test_sede_04_no_abre_captura_de_procedimiento_de_otra_sede(): void
    {
        $procedimiento = $this->crearProcedimiento($this->sedeB);

        $this->actingAs($this->user)
            ->withSession(['sede' => $this->sedeA])
            ->get(route('resultados.create', $procedimiento))
            ->assertForbidden();
    }

    // ---- Guardar resultados (web) ----

    public function test_sede_07_no_guarda_resultado_web_de_otra_sede(): void
    {
        $procedimiento = $this->crearProcedimiento($this->sedeB);

        $response = $this->actingAs($this->user)
            ->withSession(['sede' => $this->sedeA])
            ->post(route('resultados.store', $procedimiento), [
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertForbidden();
        $this->assertProcedimientoSinCambios($procedimiento);
    }

    public function test_sede_07_no_guarda_resultado_web_con_sede_activa_ajena_en_sesion(): void
    {
        // La sesión dice sede B (p. ej. elegida antes del control), pero el empleado no pertenece a B.
        $procedimiento = $this->crearProcedimiento($this->sedeB);

        $response = $this->actingAs($this->user)
            ->withSession(['sede' => $this->sedeB])
            ->post(route('resultados.store', $procedimiento), [
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertForbidden();
        $this->assertProcedimientoSinCambios($procedimiento);
    }

    public function test_sede_06_no_guarda_resultado_web_sin_sede_activa(): void
    {
        $procedimiento = $this->crearProcedimiento($this->sedeA);

        $response = $this->actingAs($this->user)
            ->post(route('resultados.store', $procedimiento), [
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertForbidden();
        $this->assertProcedimientoSinCambios($procedimiento);
    }

    public function test_guarda_resultado_web_de_su_sede(): void
    {
        $procedimiento = $this->crearProcedimiento($this->sedeA);

        $response = $this->actingAs($this->user)
            ->withSession(['sede' => $this->sedeA])
            ->post(route('resultados.store', $procedimiento), [
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertRedirect(route('resultados.show', $procedimiento));
        $this->assertDatabaseHas('procedimientos', [
            'id' => $procedimiento->id,
            'empleado_id' => $this->empleado->id,
        ]);
        $this->assertNotNull($procedimiento->orden->fresh()->terminada);
    }

    // ---- Guardar resultados (API) ----

    public function test_sede_08_no_guarda_resultado_api_de_otra_sede(): void
    {
        $procedimiento = $this->crearProcedimiento($this->sedeB);

        $response = $this->actingAs($this->user, 'sanctum')
            ->withSession(['sede' => $this->sedeA])
            ->postJson("/api/resultados/{$procedimiento->id}", [
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertForbidden();
        $this->assertProcedimientoSinCambios($procedimiento);
    }

    public function test_sede_08_no_guarda_resultado_api_sin_sede_activa(): void
    {
        $procedimiento = $this->crearProcedimiento($this->sedeA);

        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson("/api/resultados/{$procedimiento->id}", [
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertForbidden();
        $this->assertProcedimientoSinCambios($procedimiento);
    }

    public function test_api_guarda_con_el_empleado_autenticado_y_no_el_enviado(): void
    {
        $procedimiento = $this->crearProcedimiento($this->sedeA);

        $response = $this->actingAs($this->user, 'sanctum')
            ->withSession(['sede' => $this->sedeA])
            ->postJson("/api/resultados/{$procedimiento->id}", [
                'resultados' => [$this->parametro->id => '13.1'],
            ]);

        $response->assertOk();
        $this->assertNotEquals($this->user->id, $this->empleado->id);
        $this->assertDatabaseHas('procedimientos', [
            'id' => $procedimiento->id,
            'empleado_id' => $this->empleado->id,
        ]);
        $this->assertNotNull($procedimiento->orden->fresh()->terminada);
    }
}

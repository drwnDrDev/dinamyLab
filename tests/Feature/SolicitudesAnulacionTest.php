<?php

namespace Tests\Feature;

use App\Estado;
use App\Models\Empleado;
use App\Models\EventoActividad;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Sede;
use App\Models\SolicitudAnulacion;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolicitudesAnulacionTest extends TestCase
{
    use RefreshDatabase;

    private User $prestador;

    private User $admin;

    private Sede $sede;

    private Procedimiento $procedimiento;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([PaisSeeder::class, TipoDocumentoSeeder::class, TestDataSeeder::class, RolesYPermisosSeeder::class]);
        $this->sede = Sede::factory()->create();
        $this->prestador = $this->usuario('prestador');
        $this->admin = $this->usuario('admin');
        $this->procedimiento = $this->crearProcedimiento($this->sede);
        Parametro::create([
            'examen_id' => $this->procedimiento->examen_id, 'nombre' => 'Valor', 'slug' => 'valor',
            'posicion' => 1, 'tipo_dato' => 'number',
        ]);
        session(['sede' => $this->sede]);
    }

    public function test_el_prestador_solicita_con_motivo_y_el_examen_sigue_en_proceso(): void
    {
        $antes = $this->procedimiento->fresh()->getAttributes();
        $this->actingAs($this->prestador)->get(route('anulaciones.create', $this->procedimiento))
            ->assertOk()->assertSee('Motivo de la solicitud');
        $this->get(route('examenes.lote', $this->procedimiento->examen))
            ->assertOk()->assertViewHas('muestras', fn ($muestras) => $muestras[0]['url_anulacion'] === route('anulaciones.create', $this->procedimiento));
        $this->get(route('procedimientos'))->assertOk()->assertSee('Solicitar anulación');
        $this->get(route('resultados.create', $this->procedimiento))->assertOk()->assertSee('Solicitar anulación');

        $this->enviarSolicitud();
        $solicitud = SolicitudAnulacion::sole();
        $this->assertSame('pendiente', $solicitud->estado);
        $this->assertEquals($this->prestador->id, $solicitud->solicitante_id);
        $this->assertSame($antes, $this->procedimiento->fresh()->getAttributes());
        $this->assertNull($this->procedimiento->orden->fresh()->terminada);
        $this->assertDatabaseCount('resultados', 0);
        $this->get(route('anulaciones.create', $this->procedimiento))->assertOk()
            ->assertSee('solicitud pendiente')->assertDontSee('Enviar solicitud a administración');
        $evento = EventoActividad::where('evento', 'procedimiento.anulacion_solicitada')->sole();
        $this->assertSame(['solicitud_id' => $solicitud->id], $evento->detalles);
        $this->assertStringNotContainsString('Muestra insuficiente', $evento->toJson());
        $this->actingAs($this->admin)->get(route('anulaciones.index'))->assertOk()
            ->assertSee('Muestra insuficiente')->assertSee('Aprobar y anular examen');
    }

    public function test_solo_administracion_puede_aprobar_y_no_se_puede_resolver_dos_veces(): void
    {
        $this->enviarSolicitud();
        $solicitud = SolicitudAnulacion::sole();
        $url = route('anulaciones.resolver', $solicitud);
        $this->actingAs($this->prestador)->patchJson($url, ['decision' => 'aprobar'])->assertForbidden();
        $this->assertSame(Estado::PROCESO->value, $this->procedimiento->fresh()->estado);

        $this->actingAs($this->admin)->patch($url, ['decision' => 'aprobar'])
            ->assertRedirect(route('anulaciones.index'));
        $this->assertSame(Estado::ANULADO->value, $this->procedimiento->fresh()->estado);
        $this->assertNotNull($this->procedimiento->orden->fresh()->terminada);
        $this->assertSame('aprobada', $solicitud->fresh()->estado);
        $this->assertEquals($this->admin->id, $solicitud->fresh()->resuelta_por_id);
        $this->assertNotNull($solicitud->fresh()->resuelta_at);
        $this->assertDatabaseCount('resultados', 0);
        $this->assertSame(1, EventoActividad::where('evento', 'procedimiento.anulacion_aprobada')->count());
        $this->patchJson($url, ['decision' => 'rechazar', 'respuesta' => 'Segunda decisión'])
            ->assertUnprocessable()->assertJsonValidationErrors('decision');
        $this->assertSame('aprobada', $solicitud->fresh()->estado);
    }

    public function test_rechazar_exige_una_respuesta_mantiene_el_estado_y_permite_otra_solicitud(): void
    {
        $this->enviarSolicitud();
        $solicitud = SolicitudAnulacion::sole();
        $antes = $this->procedimiento->fresh()->getAttributes();
        $this->actingAs($this->admin)->patchJson(route('anulaciones.resolver', $solicitud), ['decision' => 'rechazar'])
            ->assertUnprocessable()->assertJsonValidationErrors('respuesta');
        $this->patch(route('anulaciones.resolver', $solicitud), ['decision' => 'rechazar', 'respuesta' => 'Debe recolectarse otra muestra'])
            ->assertRedirect(route('anulaciones.index'));
        $this->assertSame('rechazada', $solicitud->fresh()->estado);
        $this->assertSame($antes, $this->procedimiento->fresh()->getAttributes());
        $this->actingAs($this->prestador)->get(route('anulaciones.create', $this->procedimiento))->assertOk()
            ->assertSee('Debe recolectarse otra muestra')->assertSee('Enviar solicitud a administración');
        $this->enviarSolicitud();
        $this->assertDatabaseCount('solicitudes_anulacion', 2);
        $this->assertSame(1, SolicitudAnulacion::where('estado', 'pendiente')->count());
    }

    public function test_exige_motivo_y_evade_duplicados_o_estados_falsificados(): void
    {
        $url = route('anulaciones.store', $this->procedimiento);
        $this->actingAs($this->prestador)->postJson($url, ['motivo' => '   '])
            ->assertUnprocessable()->assertJsonValidationErrors('motivo');
        $this->post($url, ['motivo' => 'Muestra insuficiente', 'estado' => 'aprobada', 'solicitante_id' => $this->admin->id])
            ->assertRedirect();
        $this->assertEquals($this->prestador->id, SolicitudAnulacion::sole()->solicitante_id);
        $this->assertSame('pendiente', SolicitudAnulacion::sole()->estado);
        $this->postJson($url, ['motivo' => 'Otra solicitud'])->assertUnprocessable()->assertJsonValidationErrors('motivo');
        $this->assertDatabaseCount('solicitudes_anulacion', 1);
    }

    public function test_sedes_y_roles_restringen_las_solicitudes_y_el_prestador_solo_lista_las_suyas(): void
    {
        $otraSede = Sede::factory()->create(['empresa_id' => $this->sede->empresa_id]);
        $ajeno = $this->crearProcedimiento($otraSede);
        $this->actingAs($this->prestador)->get(route('anulaciones.create', $ajeno))->assertForbidden();
        $this->postJson(route('anulaciones.store', $ajeno), ['motivo' => 'Sin acceso'])->assertForbidden();
        $this->enviarSolicitud();
        $otroPrestador = $this->usuario('prestador');
        $this->actingAs($otroPrestador)->get(route('anulaciones.index'))->assertOk()->assertDontSee('Muestra insuficiente');
        $this->actingAs($this->prestador)->get(route('anulaciones.index'))->assertOk()->assertSee('Muestra insuficiente');
        foreach (['agente', 'contable'] as $rol) {
            $this->actingAs($this->usuario($rol))->get(route('anulaciones.index'))->assertForbidden();
            $this->postJson(route('anulaciones.store', $this->procedimiento), ['motivo' => 'Sin permiso'])->assertForbidden();
        }
        $this->actingAs($this->admin)->postJson(route('anulaciones.store', $this->procedimiento), ['motivo' => 'Sin permiso'])->assertForbidden();
    }

    public function test_no_admite_solicitudes_de_examenes_finalizados_o_exportados(): void
    {
        foreach ([
            ['estado' => Estado::TERMINADO->value], ['estado' => Estado::ENTREGADO->value], ['estado' => Estado::ANULADO->value],
            ['estado' => Estado::PROCESO->value, 'rips_procesado_at' => now()],
            ['estado' => Estado::PROCESO->value, 'rips_procesado_at' => null, 'resultados_pdf_descargado_at' => now()],
        ] as $cambios) {
            $this->procedimiento->forceFill($cambios)->save();
            $this->actingAs($this->prestador)->postJson(route('anulaciones.store', $this->procedimiento), ['motivo' => 'Motivo'])
                ->assertUnprocessable()->assertJsonValidationErrors('motivo');
        }
        $this->assertDatabaseCount('solicitudes_anulacion', 0);
    }

    public function test_no_aprueba_si_el_examen_termino_despues_de_solicitar_la_anulacion(): void
    {
        $this->enviarSolicitud();
        $solicitud = SolicitudAnulacion::sole();
        $this->procedimiento->resultado()->create([
            'parametro_id' => $this->procedimiento->examen->parametros()->first()->id, 'posicion' => 1, 'resultado' => '12',
        ]);
        $this->procedimiento->update(['estado' => Estado::TERMINADO->value]);
        $this->actingAs($this->admin)->patchJson(route('anulaciones.resolver', $solicitud), ['decision' => 'aprobar'])
            ->assertUnprocessable()->assertJsonValidationErrors('decision');
        $this->assertSame(Estado::TERMINADO->value, $this->procedimiento->fresh()->estado);
        $this->assertSame('pendiente', $solicitud->fresh()->estado);
        $this->assertDatabaseHas('resultados', ['procedimiento_id' => $this->procedimiento->id, 'resultado' => '12']);
        $this->assertSame(0, EventoActividad::where('evento', 'procedimiento.anulacion_aprobada')->count());
    }

    public function test_el_prestador_no_puede_anular_directamente_ni_reabrir_un_examen_anulado(): void
    {
        $this->actingAs($this->prestador)->patchJson(route('procedimientos.observaciones', $this->procedimiento), [
            'estado' => Estado::ANULADO->value, 'observacion' => 'Anulación directa',
        ])->assertForbidden();
        $this->assertSame(Estado::PROCESO->value, $this->procedimiento->fresh()->estado);
        $this->enviarSolicitud();
        $this->actingAs($this->admin)->patch(route('anulaciones.resolver', SolicitudAnulacion::sole()), ['decision' => 'aprobar'])->assertRedirect();
        $this->actingAs($this->prestador)->patchJson(route('procedimientos.observaciones', $this->procedimiento), [
            'estado' => Estado::PROCESO->value, 'observacion' => 'Reabrir',
        ])->assertForbidden();
        $this->assertSame(Estado::ANULADO->value, $this->procedimiento->fresh()->estado);
    }

    private function enviarSolicitud(): void
    {
        $this->actingAs($this->prestador)->post(route('anulaciones.store', $this->procedimiento), ['motivo' => 'Muestra insuficiente'])
            ->assertRedirect(route('anulaciones.create', $this->procedimiento));
    }

    private function usuario(string $rol): User
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $user->assignRole($rol);
        Empleado::factory()->create(['user_id' => $user->id, 'empresa_id' => $this->sede->empresa_id, 'tipo_documento_id' => 1])
            ->sedes()->attach($this->sede->id);

        return $user;
    }

    private function crearProcedimiento(Sede $sede): Procedimiento
    {
        $orden = Orden::create([
            'sede_id' => $sede->id, 'numero' => fake()->unique()->numerify('ANUL-######'),
            'paciente_id' => Persona::factory()->create()->id, 'total' => 100, 'user_id' => $this->prestador->id,
        ]);

        return Procedimiento::create([
            'orden_id' => $orden->id, 'examen_id' => Examen::factory()->create()->id, 'sede_id' => $sede->id,
            'fecha' => now(), 'diagnostico_principal' => 'Z017', 'codigo_finalidad' => '14',
            'codigo_modalidad' => '01', 'codigo_via_ingreso' => '01', 'estado' => Estado::PROCESO->value,
        ]);
    }
}

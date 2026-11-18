<?php

namespace Tests\Feature;

use App\Http\Controllers\ActividadController;
use App\Models\EventoActividad;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Resultado;
use App\Models\Sede;
use App\Models\User;
use App\Services\RegistrarActividad;
use Database\Seeders\PaisSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class MonitorActividadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TestDataSeeder::class);
        $this->seed(RolesYPermisosSeeder::class);
    }

    public function test_registra_inicio_y_cierre_con_usuario_y_hora_sin_credenciales(): void
    {
        $usuario = User::factory()->create();
        $this->post('/login', ['email' => $usuario->email, 'password' => 'incorrecta']);
        $this->assertDatabaseCount('eventos_actividad', 0);
        $this->post('/login', ['email' => $usuario->email, 'password' => 'password'])->assertRedirect();
        $this->assertDatabaseHas('eventos_actividad', ['evento' => 'sesion.iniciada', 'usuario_id' => $usuario->id]);
        $evento = EventoActividad::first();
        $this->assertNotNull($evento->ocurrio_en);
        $this->assertStringNotContainsString('password', $evento->toJson());
        $this->post('/logout')->assertRedirect('/');
        $this->assertDatabaseHas('eventos_actividad', ['evento' => 'sesion.cerrada', 'usuario_id' => $usuario->id]);
    }

    public function test_registra_alta_de_usuario(): void
    {
        $this->post('/register', [
            'name' => 'Usuario nuevo', 'email' => 'nuevo@example.test',
            'password' => 'Password123!', 'password_confirmation' => 'Password123!',
        ])->assertRedirect();
        $this->assertDatabaseHas('eventos_actividad', ['evento' => 'usuario.registrado', 'usuario_nombre' => 'Usuario nuevo']);
    }

    public function test_actualizacion_de_contacto_registra_campos_pero_no_sus_valores(): void
    {
        $persona = Persona::factory()->create();
        $usuario = User::factory()->create();
        $usuario->assignRole('agente');
        $this->actingAs($usuario)->putJson('/api/personas/'.$persona->id, ['telefono' => '3009998877'])->assertOk();
        $evento = EventoActividad::where('evento', 'paciente.actualizado')->sole();
        $this->assertEquals($persona->id, $evento->recurso_id);
        $this->assertSame(['telefono'], $evento->detalles['campos']);
        $this->assertStringNotContainsString('3009998877', $evento->toJson());
        $this->putJson('/api/personas/'.$persona->id, ['numero_documento' => 'otro'])->assertBadRequest();
        $this->assertSame(1, EventoActividad::where('evento', 'paciente.actualizado')->count());
    }

    public function test_creaciones_conservan_referencia_y_se_revierten_con_la_transaccion(): void
    {
        $usuario = User::factory()->create();
        $sede = Sede::factory()->create();
        $this->actingAs($usuario)->withSession(['sede' => $sede]);
        $nivel = DB::transactionLevel();
        DB::beginTransaction();
        $persona = Persona::factory()->create();
        $orden = Orden::create(['sede_id' => $sede->id, 'paciente_id' => $persona->id, 'user_id' => $usuario->id, 'numero' => 444, 'total' => 0]);
        $this->assertDatabaseHas('eventos_actividad', ['evento' => 'paciente.creado', 'recurso_id' => $persona->id]);
        $this->assertDatabaseHas('eventos_actividad', ['evento' => 'orden.creada', 'recurso_id' => $orden->id, 'empresa_id' => $sede->empresa_id]);
        DB::rollBack($nivel);
        $this->assertDatabaseCount('eventos_actividad', 0);
    }

    public function test_monitor_solo_admin_y_filtra_por_usuario_evento_y_fecha_colombiana(): void
    {
        $usuario = User::factory()->create();
        $this->get(route('actividad.index'))->assertRedirect('/login');
        $this->actingAs($usuario)->get(route('actividad.index'))->assertForbidden();
        $usuario->assignRole('admin');
        $this->travelTo(now('UTC')->setDate(2026, 11, 18)->setTime(2, 15));
        RegistrarActividad::registrar('sesion.iniciada', $usuario);
        $this->travelBack();
        $this->get(route('actividad.index', ['desde' => '2026-11-17', 'hasta' => '2026-11-17', 'usuario_id' => $usuario->id, 'evento' => 'sesion.iniciada']))
            ->assertOk()->assertSee('17/11/2026 21:15:00')->assertSee('1 eventos encontrados');
        $this->get(route('actividad.index', ['hasta' => '2026-11-16']))->assertOk()->assertSee('0 eventos encontrados');
        $this->get(route('actividad.index', ['evento' => 'orden.creada']))->assertOk()->assertSee('0 eventos encontrados');
        $this->delete('/admin/actividad/1')->assertNotFound();
    }

    public function test_impresion_valida_reporte_usuario_vigencia_y_acceso(): void
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('admin');
        $this->actingAs($usuario);
        $sede = Sede::factory()->create();
        $persona = Persona::factory()->create();
        $orden = Orden::create(['sede_id' => $sede->id, 'paciente_id' => $persona->id, 'user_id' => $usuario->id, 'numero' => 445, 'total' => 0]);
        $examen = Examen::factory()->create();
        $procedimiento = $orden->procedimientos()->create([
            'examen_id' => $examen->id, 'sede_id' => $sede->id, 'fecha' => now(), 'estado' => 'terminado',
            'diagnostico_principal' => 'Z017', 'codigo_finalidad' => '15', 'codigo_modalidad' => '01', 'codigo_via_ingreso' => '01',
        ]);
        $parametro = Parametro::create(['examen_id' => $examen->id, 'nombre' => 'Prueba', 'slug' => 'prueba-monitor', 'tipo_dato' => 'number']);
        Resultado::create(['procedimiento_id' => $procedimiento->id, 'parametro_id' => $parametro->id, 'posicion' => 1, 'resultado' => '13.5']);
        $this->assertDatabaseHas('eventos_actividad', ['evento' => 'resultado.creado', 'recurso_id' => $procedimiento->id]);
        $token = ActividadController::tokenImpresion(collect([$procedimiento]));
        $this->postJson(route('actividad.impresion'), ['token' => $token])->assertNoContent();
        $this->assertDatabaseHas('eventos_actividad', ['evento' => 'resultados.impresion_solicitada', 'usuario_id' => $usuario->id]);
        $this->postJson(route('actividad.impresion'), ['token' => 'alterado'])->assertStatus(422);
        $otro = User::factory()->create(['email' => 'otro-monitor@example.test']);
        $otro->assignRole('admin');
        $this->actingAs($otro)->postJson(route('actividad.impresion'), ['token' => $token])->assertForbidden();
        // Incluso un token válido no elude el control actual de sede.
        $usuario->syncRoles(['agente']);
        $this->actingAs($usuario)->postJson(route('actividad.impresion'), ['token' => $token])->assertForbidden();
        $usuario->syncRoles(['admin']);
        $expirado = Crypt::encryptString(json_encode(['usuario_id' => $usuario->id, 'procedimientos' => [$procedimiento->id], 'expira' => now()->subMinute()->timestamp]));
        $this->postJson(route('actividad.impresion'), ['token' => $expirado])->assertForbidden();
        $this->assertSame(1, EventoActividad::where('evento', 'resultados.impresion_solicitada')->count());
        $this->assertStringNotContainsString('13.5', EventoActividad::where('evento', 'resultado.creado')->sole()->toJson());
    }

    public function test_registra_acceso_a_ficha_api_e_historia_sin_copiar_datos_del_paciente(): void
    {
        $paciente = Persona::factory()->create();
        $usuario = User::factory()->create();
        $usuario->assignRole('admin');
        $this->actingAs($usuario)->get(route('personas.show', $paciente))->assertOk();
        $this->getJson('/api/personas/'.$paciente->id)->assertOk();
        $this->get(route('resultados.historia', $paciente))->assertOk();
        $eventos = EventoActividad::where('evento', 'paciente.historia_consultada')->get();
        $this->assertCount(3, $eventos);
        $this->assertSame(['personas.show', 'api.personas.show', 'resultados.historia'], $eventos->pluck('detalles.origen')->all());
        foreach ($eventos as $evento) {
            $this->assertEquals($paciente->id, $evento->recurso_id);
            $this->assertEquals($usuario->id, $evento->usuario_id);
            $this->assertStringNotContainsString($paciente->numero_documento, $evento->toJson());
        }
        $this->get(route('personas.show', 99999999))->assertNotFound();
        $this->getJson('/api/personas/99999999')->assertNotFound();
        $usuario->syncRoles(['contable']);
        $this->get(route('personas.show', $paciente))->assertForbidden();
        $this->get(route('resultados.historia', $paciente))->assertForbidden();
        $this->assertSame(3, EventoActividad::where('evento', 'paciente.historia_consultada')->count());
    }

    public function test_audita_consulta_y_descarga_rips_con_la_sede_exportada_y_el_intervalo(): void
    {
        $usuario = User::factory()->create();
        $usuario->assignRole('admin');
        $sede = Sede::factory()->create();
        $otraSede = Sede::factory()->create();
        $paciente = Persona::factory()->create();
        $orden = Orden::create(['sede_id' => $sede->id, 'paciente_id' => $paciente->id, 'user_id' => $usuario->id, 'numero' => 446, 'total' => 0]);
        $orden->procedimientos()->create([
            'examen_id' => Examen::factory()->create()->id, 'sede_id' => $sede->id, 'fecha' => '2026-11-10 12:00:00', 'estado' => 'terminado',
            'diagnostico_principal' => 'Z017', 'codigo_finalidad' => '15', 'codigo_modalidad' => '01', 'codigo_via_ingreso' => '01',
        ]);
        $fechas = ['fecha_inicio' => '2026-11-01', 'fecha_fin' => '2026-11-17'];
        $this->actingAs($usuario)->withSession(['sede' => $otraSede])->get(route('reportes', $fechas))->assertOk();
        $this->assertDatabaseHas('eventos_actividad', ['evento' => 'rips.reporte_consultado', 'usuario_id' => $usuario->id, 'sede_id' => null, 'empresa_id' => null]);
        $this->post(route('descargar.rips'), $fechas + ['sede_id' => $sede->id])->assertOk()->assertHeader('content-type', 'application/json');
        $evento = EventoActividad::where('evento', 'rips.descargados')->sole();
        $this->assertEquals($sede->id, $evento->sede_id);
        $this->assertEquals($sede->empresa_id, $evento->empresa_id);
        $this->assertSame('Sede', $evento->recurso_tipo);
        $this->assertSame(1, $evento->detalles['cantidad_usuarios']);
        $this->assertSame(1, $evento->detalles['cantidad_procedimientos']);
        $this->assertSame('2026-11-01', $evento->detalles['fecha_inicio']);
        $this->assertStringNotContainsString($paciente->numero_documento, $evento->toJson());
        $this->postJson(route('descargar.rips'), $fechas + ['sede_id' => 99999999])->assertUnprocessable();
        $usuario->syncRoles(['agente']);
        $this->postJson(route('descargar.rips'), $fechas + ['sede_id' => $sede->id])->assertForbidden();
        $this->assertSame(1, EventoActividad::where('evento', 'rips.descargados')->count());
    }
}

<?php

namespace Tests\Feature\Authorization;

use App\Estado;
use App\Jobs\RevertirEstadoProcedimientos;
use App\Models\Empleado;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PermissionProfilesTest extends TestCase
{
    use RefreshDatabase;

    private Sede $sede;

    private User $admin;

    private User $prestador;

    private User $agente;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TestDataSeeder::class);
        $this->seed(RolesYPermisosSeeder::class);

        $this->sede = Sede::factory()->create();
        $this->admin = $this->userForRole('admin');
        $this->prestador = $this->userForRole('prestador');
        $this->agente = $this->userForRole('agente');
    }

    public function test_agente_puede_consultar_personas_ordenes_procedimientos_y_resultados(): void
    {
        $persona = Persona::factory()->create();
        $orden = $this->crearOrden($persona, $this->sede, $this->agente);

        $this->actingAs($this->agente, 'sanctum')
            ->withSession(['sede' => $this->sede])
            ->getJson('/api/personas')
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('ordenes'))
            ->assertOk();

        $this->actingAs($this->agente, 'sanctum')
            ->withSession(['sede' => $this->sede])
            ->getJson("/api/orden/{$orden->id}")
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('resultados'))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('procedimientos'))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('personas.create'))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('personas.edit', $persona))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('ordenes.create', $persona))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('ordenes.edit', $orden))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('resultados.reporte_pdf', $orden))
            ->assertRedirect(route('resultados.reporte_orden', $orden));

        $this->actingAs($this->agente, 'sanctum')
            ->postJson('/api/ordenes', [])
            ->assertUnprocessable();

        $this->actingAs($this->agente, 'sanctum')
            ->patchJson('/api/ordenes-medicas/'.$orden->id, [])
            ->assertUnprocessable();
    }

    public function test_agente_puede_ver_examenes_reportes_y_descargar_rips(): void
    {
        $examen = Examen::factory()->create();
        $orden = $this->crearOrden(Persona::factory()->create(), $this->sede, $this->agente);
        $procedimiento = $this->crearProcedimiento($orden, $examen, $this->sede);
        $procedimiento->update(['estado' => Estado::TERMINADO->value]);
        $parametro = Parametro::create([
            'nombre' => 'Hemoglobina',
            'slug' => 'hemoglobina-agente',
            'tipo_dato' => 'number',
            'examen_id' => $examen->id,
        ]);
        Resultado::create([
            'parametro_id' => $parametro->id,
            'procedimiento_id' => $procedimiento->id,
            'resultado' => '13.5',
        ]);

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('examenes'))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('examenes.show', $examen))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('reportes'))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('resultados.show', $procedimiento))
            ->assertOk()
            ->assertSee('Hemoglobina');

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('resultados.reporte_pdf', $orden))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->post(route('descargar.rips'), [
                'sede_id' => $this->sede->id,
                'fecha_inicio' => now()->startOfMonth()->toDateString(),
                'fecha_fin' => now()->endOfMonth()->toDateString(),
            ])
            ->assertOk()
            ->assertHeader('Content-Disposition');
    }

    public function test_admin_y_agente_no_pueden_crear_ni_editar_resultados(): void
    {
        $orden = $this->crearOrden(Persona::factory()->create(), $this->sede, $this->prestador);
        $examen = Examen::factory()->create();
        $procedimiento = $this->crearProcedimiento($orden, $examen, $this->sede);

        foreach ([$this->admin, $this->agente] as $user) {
            $this->assertFalse($user->can('registrar_resultado'));
            $this->assertFalse($user->can('editar_resultado'));

            $this->actingAs($user)
                ->withSession(['sede' => $this->sede])
                ->get(route('resultados.create', $procedimiento))
                ->assertForbidden();

            $this->actingAs($user)
                ->withSession(['sede' => $this->sede])
                ->post(route('resultados.store', $procedimiento), [])
                ->assertForbidden();

            $this->actingAs($user)
                ->withSession(['sede' => $this->sede])
                ->get(route('resultados.edit', $procedimiento))
                ->assertForbidden();

            $this->actingAs($user)
                ->withSession(['sede' => $this->sede])
                ->put(route('resultados.update', $procedimiento), [])
                ->assertForbidden();

            $this->actingAs($user, 'sanctum')
                ->postJson("/api/resultados/{$procedimiento->id}", [])
                ->assertForbidden();

            $this->actingAs($user)
                ->withSession(['sede' => $this->sede])
                ->get(route('examenes.lote', $examen))
                ->assertForbidden();

            $this->actingAs($user)
                ->withSession(['sede' => $this->sede])
                ->post(route('examenes.lote.store', $examen), [])
                ->assertForbidden();
        }

        $this->admin->assignRole('prestador');
        $this->admin->givePermissionTo(['registrar_resultado', 'editar_resultado']);

        $this->actingAs($this->admin)
            ->withSession(['sede' => $this->sede])
            ->get(route('resultados.create', $procedimiento))
            ->assertForbidden();

        $this->actingAs($this->admin)
            ->withSession(['sede' => $this->sede])
            ->put(route('resultados.update', $procedimiento), [])
            ->assertForbidden();

        $this->actingAs($this->admin, 'sanctum')
            ->postJson("/api/resultados/{$procedimiento->id}", [])
            ->assertForbidden();
    }

    public function test_agente_no_puede_eliminar_personas_ni_editar_examenes(): void
    {
        $persona = Persona::factory()->create();
        $examen = Examen::factory()->create();

        $this->actingAs($this->agente, 'sanctum')
            ->deleteJson("/api/personas/{$persona->id}")
            ->assertForbidden();

        $this->actingAs($this->agente, 'sanctum')
            ->patchJson("/api/examenes/{$examen->id}/update", ['descripcion' => 'Cambio no autorizado'])
            ->assertForbidden();
    }

    public function test_busqueda_de_orden_para_agente_no_revela_ordenes_de_otra_sede(): void
    {
        $persona = Persona::factory()->create();
        $ordenPropia = $this->crearOrden($persona, $this->sede, $this->agente);
        $otraSede = Sede::factory()->create();
        $ordenAjena = $this->crearOrden($persona, $otraSede, $this->agente);

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get('/search?search='.$ordenPropia->numero)
            ->assertRedirect(route('ordenes.show', $ordenPropia));

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get('/search?search='.$ordenAjena->numero)
            ->assertOk();
    }

    public function test_prestador_puede_actualizar_solo_la_descripcion_del_examen(): void
    {
        $examen = Examen::factory()->create([
            'descripcion' => 'Descripción anterior',
            'nombre' => 'Examen de prueba',
        ]);

        $this->actingAs($this->prestador, 'sanctum')
            ->withSession(['sede' => $this->sede])
            ->patchJson("/api/examenes/{$examen->id}/update", [
                'descripcion' => 'Descripción actualizada',
                'nombre' => 'Nombre que no debe cambiar',
            ])
            ->assertOk()
            ->assertJsonPath('data.examen.descripcion', 'Descripción actualizada')
            ->assertJsonPath('data.examen.nombre', 'Examen de prueba');

        $this->actingAs($this->prestador, 'sanctum')
            ->getJson('/api/cie10')
            ->assertOk();

        $this->actingAs($this->prestador, 'sanctum')
            ->postJson('/api/cie10', [])
            ->assertForbidden();

        $this->assertDatabaseHas('examenes', [
            'id' => $examen->id,
            'descripcion' => 'Descripción actualizada',
            'nombre' => 'Examen de prueba',
        ]);

        $this->actingAs($this->prestador, 'sanctum')
            ->withSession(['sede' => null])
            ->patchJson("/api/examenes/{$examen->id}/update", [
                'descripcion' => 'Cambio sin sede activa',
            ])
            ->assertForbidden();

        $this->actingAs($this->prestador)
            ->get('/cups')
            ->assertForbidden();
    }

    public function test_admin_conserva_la_edicion_completa_del_examen(): void
    {
        $examen = Examen::factory()->create([
            'nombre' => 'Examen original',
            'valor' => 100,
            'nombre_alternativo' => null,
            'activo' => true,
        ]);

        $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/examenes/{$examen->id}/update", [
                'nombre' => 'Examen actualizado',
                'valor' => 125,
                'nombre_alternativo' => 'Alternativo',
                'descripcion' => 'Descripción completa',
                'activo' => false,
            ])
            ->assertOk()
            ->assertJsonPath('data.examen.nombre', 'Examen actualizado')
            ->assertJsonPath('data.examen.descripcion', 'Descripción completa');

        $this->assertDatabaseHas('examenes', [
            'id' => $examen->id,
            'nombre' => 'Examen actualizado',
            'valor' => 125,
            'nombre_alternativo' => 'Alternativo',
            'descripcion' => 'Descripción completa',
            'activo' => false,
        ]);
    }

    public function test_prestador_puede_eliminar_procedimientos_de_su_sede_y_no_de_otra(): void
    {
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create();
        $propio = $this->crearOrden($paciente, $this->sede, $this->prestador);
        $procedimientoPropio = $this->crearProcedimiento($propio, $examen, $this->sede);

        $otraSede = Sede::factory()->create();
        $ajeno = $this->crearOrden($paciente, $otraSede, $this->prestador);
        $procedimientoAjeno = $this->crearProcedimiento($ajeno, $examen, $otraSede);

        $this->actingAs($this->prestador, 'sanctum')
            ->withSession(['sede' => $this->sede])
            ->deleteJson("/api/procedimientos/{$procedimientoPropio->id}")
            ->assertOk();

        $this->assertSoftDeleted('procedimientos', ['id' => $procedimientoPropio->id]);

        $this->actingAs($this->prestador, 'sanctum')
            ->withSession(['sede' => $this->sede])
            ->deleteJson("/api/procedimientos/{$procedimientoAjeno->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('procedimientos', [
            'id' => $procedimientoAjeno->id,
            'deleted_at' => null,
        ]);
    }

    public function test_prestador_puede_editar_estado_del_procedimiento_y_guardar_observacion(): void
    {
        $persona = Persona::factory()->create();
        $examen = Examen::factory()->create();
        $orden = $this->crearOrden($persona, $this->sede, $this->prestador);
        $procedimiento = $this->crearProcedimiento($orden, $examen, $this->sede);

        $this->actingAs($this->prestador)
            ->withSession(['sede' => $this->sede])
            ->patchJson(route('procedimientos.observaciones', $procedimiento), [
                'estado' => 'pendiente de muestra',
                'observacion' => 'Se requiere repetir la toma.',
            ])
            ->assertOk()
            ->assertJsonPath('estado', 'pendiente de muestra')
            ->assertJsonPath('observacion', 'Se requiere repetir la toma.');

        $this->assertDatabaseHas('observaciones', [
            'observable_id' => $procedimiento->id,
            'observable_type' => Procedimiento::class,
            'observacion' => 'Se requiere repetir la toma.',
        ]);
    }

    public function test_admin_puede_acceder_a_configuracion_y_usuario_sin_perfil_clinico_es_rechazado(): void
    {
        $this->actingAs($this->admin)
            ->get('/cups')
            ->assertOk();

        $sinPerfil = User::factory()->create([
            'email' => Str::uuid().'@example.test',
        ]);

        $this->actingAs($sinPerfil, 'sanctum')
            ->getJson('/api/personas')
            ->assertForbidden();
    }

    public function test_admin_puede_seleccionar_cualquier_sede_y_acceder_a_ordenes_sin_sede_activa(): void
    {
        $otraSede = Sede::factory()->create();
        $paciente = Persona::factory()->create();
        $orden = $this->crearOrden($paciente, $otraSede, $this->admin);

        $this->actingAs($this->admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($otraSede->nombre);

        $this->actingAs($this->admin)
            ->get(route('elegir.sede', $otraSede))
            ->assertRedirect(route('inicio'));

        $this->actingAs($this->admin)
            ->withSession(['sede' => null])
            ->get(route('inicio'))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('ordenes'))
            ->assertOk()
            ->assertSee((string) $orden->numero);

        $procedimiento = $this->crearProcedimiento($orden, Examen::factory()->create(), $otraSede);
        $this->actingAs($this->admin)
            ->withSession(['sede' => null])
            ->get(route('resultados.reporte_pdf', $orden))
            ->assertRedirect(route('resultados.reporte_orden', $orden));

        $this->actingAs($this->admin)
            ->withSession(['sede' => null])
            ->get(route('procedimientos.show', $procedimiento))
            ->assertOk();
    }

    public function test_admin_prestador_y_agente_pueden_ver_reportes(): void
    {
        Role::findByName('admin')->revokePermissionTo('ver_reportes');

        $this->actingAs($this->admin)
            ->get(route('reportes'))
            ->assertOk();

        $this->actingAs($this->prestador)
            ->withSession(['sede' => $this->sede])
            ->get(route('reportes'))
            ->assertOk();

        $this->actingAs($this->agente)
            ->withSession(['sede' => $this->sede])
            ->get(route('reportes'))
            ->assertOk();
    }

    public function test_la_orden_se_cierra_solo_cuando_todos_sus_procedimientos_terminan(): void
    {
        $persona = Persona::factory()->create();
        $examen = Examen::factory()->create();
        $orden = $this->crearOrden($persona, $this->sede, $this->prestador);
        $primero = $this->crearProcedimiento($orden, $examen, $this->sede);
        $segundo = $this->crearProcedimiento($orden, $examen, $this->sede);

        $primero->update(['estado' => Estado::TERMINADO->value]);
        $this->assertNull($orden->fresh()->terminada);

        $segundo->update(['estado' => Estado::ENTREGADO->value]);
        $this->assertNotNull($orden->fresh()->terminada);

        $segundo->update(['estado' => Estado::PROCESO->value]);
        $this->assertNull($orden->fresh()->terminada);

        $segundo->update(['estado' => Estado::ANULADO->value]);
        $this->assertNotNull($orden->fresh()->terminada);

        $nuevo = $this->crearProcedimiento($orden, $examen, $this->sede);
        $this->assertNull($orden->fresh()->terminada);

        $nuevo->update(['estado' => Estado::ANULADO->value]);
        $this->assertNotNull($orden->fresh()->terminada);
    }

    public function test_admin_puede_imprimir_historia_de_todas_las_sedes_sin_sede_activa(): void
    {
        $persona = Persona::factory()->create();
        $examen = Examen::factory()->create();
        $otraSede = Sede::factory()->create();
        $ordenes = [
            $this->crearOrden($persona, $this->sede, $this->admin),
            $this->crearOrden($persona, $otraSede, $this->admin),
        ];
        $procedimientos = collect($ordenes)->map(function (Orden $orden) use ($examen) {
            $procedimiento = $this->crearProcedimiento($orden, $examen, $orden->sede);
            $procedimiento->update(['estado' => Estado::TERMINADO->value]);
            $parametro = Parametro::create([
                'nombre' => 'Hemoglobina '.$orden->id,
                'slug' => 'hemoglobina-'.$orden->id,
                'tipo_dato' => 'number',
                'examen_id' => $examen->id,
            ]);
            Resultado::create([
                'parametro_id' => $parametro->id,
                'procedimiento_id' => $procedimiento->id,
                'resultado' => '13.5',
            ]);

            return $procedimiento;
        });

        $response = $this->actingAs($this->admin)
            ->withSession(['sede' => null])
            ->post(route('resultados.historia_show', $persona), [
                'procedimientos' => $procedimientos->pluck('id')->all(),
            ])
            ->assertOk()
            ->assertSee('Hemoglobina '.$ordenes[0]->id)
            ->assertSee('Hemoglobina '.$ordenes[1]->id)
            ->assertSee('Reporte consolidado')
            ->assertSee('Varias sedes')
            ->assertSee('page-break-before: always');

        $this->assertSame(2, substr_count($response->getContent(), 'class="orden-seccion"'));
    }

    public function test_el_job_sincroniza_la_orden_al_marcar_procedimientos_con_resultados(): void
    {
        $persona = Persona::factory()->create();
        $examen = Examen::factory()->create();
        $orden = $this->crearOrden($persona, $this->sede, $this->prestador);
        $procedimiento = $this->crearProcedimiento($orden, $examen, $this->sede);
        $parametro = Parametro::create([
            'nombre' => 'Hemoglobina',
            'slug' => 'hemoglobina-job',
            'tipo_dato' => 'number',
            'examen_id' => $examen->id,
        ]);
        Resultado::create([
            'parametro_id' => $parametro->id,
            'procedimiento_id' => $procedimiento->id,
            'resultado' => '13.5',
        ]);

        (new RevertirEstadoProcedimientos)->handle();

        $this->assertSame(Estado::TERMINADO->value, $procedimiento->fresh()->estado);
        $this->assertNotNull($orden->fresh()->terminada);
    }

    private function userForRole(string $role): User
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
            'email' => Str::uuid().'@example.test',
        ]);
        $user->assignRole($role);

        $empleado = Empleado::factory()->create([
            'user_id' => $user->id,
            'empresa_id' => $this->sede->empresa_id,
        ]);
        $empleado->sedes()->attach($this->sede->id);

        return $user;
    }

    private function crearOrden(Persona $persona, Sede $sede, User $user): Orden
    {
        return Orden::create([
            'sede_id' => $sede->id,
            'numero' => random_int(10000, 99999),
            'paciente_id' => $persona->id,
            'total' => 100,
            'user_id' => $user->id,
        ]);
    }

    private function crearProcedimiento(Orden $orden, Examen $examen, Sede $sede): Procedimiento
    {
        return Procedimiento::create([
            'orden_id' => $orden->id,
            'examen_id' => $examen->id,
            'sede_id' => $sede->id,
            'fecha' => now(),
            'diagnostico_principal' => 'Z017',
            'codigo_finalidad' => '15',
            'codigo_modalidad' => '01',
            'codigo_via_ingreso' => '01',
            'estado' => Estado::PROCESO->value,
        ]);
    }
}

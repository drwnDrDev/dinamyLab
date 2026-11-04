<?php

namespace Tests\Feature\Authorization;

use App\Estado;
use App\Models\Empleado;
use App\Models\Examen;
use App\Models\Orden;
use App\Models\Persona;
use App\Models\Procedimiento;
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

    public function test_agente_puede_consultar_pacientes_ordenes_resultados_y_facturas_pero_no_operar_ordenes(): void
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
            ->get(route('facturas'))
            ->assertOk();

        $this->actingAs($this->agente, 'sanctum')
            ->postJson('/api/ordenes', [])
            ->assertForbidden();

        $this->actingAs($this->agente, 'sanctum')
            ->patchJson('/api/ordenes-medicas/'.$orden->id, [])
            ->assertForbidden();
    }

    public function test_agente_puede_borrar_logicamente_pacientes_y_no_editar_examenes(): void
    {
        $persona = Persona::factory()->create();
        $examen = Examen::factory()->create();

        $this->actingAs($this->agente, 'sanctum')
            ->deleteJson("/api/personas/{$persona->id}")
            ->assertOk();

        $this->assertSoftDeleted('personas', ['id' => $persona->id]);

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
    }

    public function test_admin_y_prestador_pueden_ver_reportes_y_agente_no(): void
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
            ->get(route('reportes'))
            ->assertForbidden();
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

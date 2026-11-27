<?php

namespace Tests\Feature;

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
use Tests\TestCase;

/**
 * Vistas de listado (index) rediseñadas: conteos agregados y estructura común.
 */
class VistasIndexTest extends TestCase
{
    use RefreshDatabase;

    private Sede $sede;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TestDataSeeder::class);
        $this->seed(RolesYPermisosSeeder::class);

        $this->sede = Sede::factory()->create();
        $this->admin = User::factory()->create([
            'email_verified_at' => now(),
            'email' => Str::uuid().'@example.test',
        ]);
        $this->admin->assignRole('admin');
        $empleado = Empleado::factory()->create([
            'user_id' => $this->admin->id,
            'empresa_id' => $this->sede->empresa_id,
        ]);
        $empleado->sedes()->attach($this->sede->id);
    }

    public function test_ordenes_muestra_total_de_examenes_y_cuantos_estan_listos(): void
    {
        $orden = $this->crearOrden(Persona::factory()->create());
        $this->crearProcedimiento($orden, Estado::TERMINADO);
        $this->crearProcedimiento($orden, Estado::ENTREGADO);
        $this->crearProcedimiento($orden, Estado::PROCESO);

        $this->actingAs($this->admin)
            ->withSession(['sede' => $this->sede])
            ->get(route('ordenes'))
            ->assertOk()
            ->assertSee('#'.$orden->numero)
            ->assertSee('3 exámenes')
            ->assertSee('· 2 listos')
            ->assertSee('aria-valuenow="2"', false)
            ->assertSee('Mostrando 1–1 de 1');
    }

    public function test_ordenes_no_cuenta_procedimientos_eliminados_y_mantiene_el_filtro_de_estado(): void
    {
        $pendiente = $this->crearOrden(Persona::factory()->create());
        $this->crearProcedimiento($pendiente, Estado::PROCESO);
        $this->crearProcedimiento($pendiente, Estado::TERMINADO)->delete();

        $completada = $this->crearOrden(Persona::factory()->create());
        $completada->forceFill(['terminada' => now()])->saveQuietly();

        $this->actingAs($this->admin)
            ->withSession(['sede' => $this->sede])
            ->get(route('ordenes', ['estado' => 'pendiente']))
            ->assertOk()
            ->assertSee('#'.$pendiente->numero)
            ->assertDontSee('#'.$completada->numero)
            ->assertSee('1 examen')
            ->assertSee('· 0 listos');
    }

    public function test_pacientes_muestra_conteo_y_fecha_de_ultima_orden(): void
    {
        $conOrdenes = Persona::factory()->create();
        $this->crearOrden($conOrdenes, now()->setDate(2026, 11, 5));
        $this->crearOrden($conOrdenes, now()->setDate(2026, 11, 15));
        $this->crearOrden($conOrdenes, now())->delete();

        $sinOrdenes = Persona::factory()->create();

        $this->actingAs($this->admin)
            ->withSession(['sede' => $this->sede])
            ->get(route('personas'))
            ->assertOk()
            ->assertSee($conOrdenes->numero_documento)
            ->assertSee($sinOrdenes->numero_documento)
            ->assertSee('2 órdenes')
            ->assertSee('Últ. 15/11/2026')
            ->assertSee('Sin órdenes')
            ->assertSee(route('ordenes.create', $conOrdenes), false);
    }

    public function test_busqueda_general_muestra_pacientes_por_cualquiera_de_sus_nombres_y_apellidos(): void
    {
        $pacientes = collect();
        foreach (['primer_nombre', 'segundo_nombre', 'primer_apellido', 'segundo_apellido'] as $campo) {
            $pacientes->push(Persona::factory()->create(array_replace([
                'primer_nombre' => 'Nombre', 'segundo_nombre' => 'Otro',
                'primer_apellido' => 'Apellido', 'segundo_apellido' => 'Segundo',
            ], [$campo => 'Coincidencia'])));
        }
        $sinCoincidencia = Persona::factory()->create(['primer_nombre' => 'Sin', 'segundo_nombre' => null,
            'primer_apellido' => 'Resultado', 'segundo_apellido' => null]);
        $this->crearOrden($pacientes->first(), now()->setDate(2026, 11, 15));

        $respuesta = $this->actingAs($this->admin)
            ->withSession(['sede' => $this->sede])
            ->followingRedirects()
            ->post(route('search'), ['search' => 'Coincidencia'])
            ->assertOk()
            ->assertSee('4 pacientes')
            ->assertSee('Mostrando 1–4 de 4')
            ->assertSee('Últ. 15/11/2026')
            ->assertSee('1 orden')
            ->assertDontSee($sinCoincidencia->numero_documento);

        foreach ($pacientes as $paciente) {
            $respuesta->assertSee($paciente->numero_documento);
        }
    }

    public function test_busqueda_general_conserva_el_nombre_buscado_al_cambiar_de_pagina(): void
    {
        Persona::factory()->count(26)->create(['primer_nombre' => 'Coincidencia']);

        $respuesta = $this->actingAs($this->admin)
            ->withSession(['sede' => $this->sede])
            ->followingRedirects()
            ->get(route('search', ['search' => 'Coincidencia']))
            ->assertOk()
            ->assertSee('26 pacientes')
            ->assertSee('Mostrando 1–25 de 26');
        $siguiente = $respuesta->viewData('personas')->nextPageUrl();

        $this->get($siguiente)
            ->assertOk()
            ->assertSee('26 pacientes')
            ->assertSee('Mostrando 26–26 de 26')
            ->assertSee('value="Coincidencia"', false);
    }

    public function test_procedimientos_muestra_estado_como_etiqueta(): void
    {
        $orden = $this->crearOrden(Persona::factory()->create());
        $procedimiento = $this->crearProcedimiento($orden, Estado::PROCESO);

        $this->actingAs($this->admin)
            ->withSession(['sede' => $this->sede])
            ->get(route('procedimientos'))
            ->assertOk()
            ->assertSee($procedimiento->examen->nombre)
            ->assertSee('badge badge-warning', false)
            ->assertSee('En proceso')
            ->assertSee('1 procedimiento en proceso');
    }

    public function test_resultados_indica_la_ventana_de_48_horas(): void
    {
        $this->actingAs($this->admin)
            ->withSession(['sede' => $this->sede])
            ->get(route('resultados'))
            ->assertOk()
            ->assertSee('0 resultados terminados en las últimas 48 horas')
            ->assertSee('No se encontraron resultados recientes.');
    }

    private function crearOrden(Persona $persona, $fecha = null): Orden
    {
        $orden = Orden::create([
            'sede_id' => $this->sede->id,
            'numero' => random_int(10000, 99999),
            'paciente_id' => $persona->id,
            'total' => 100,
            'user_id' => $this->admin->id,
        ]);

        if ($fecha) {
            $orden->forceFill(['created_at' => $fecha])->saveQuietly();
        }

        return $orden;
    }

    private function crearProcedimiento(Orden $orden, Estado $estado): Procedimiento
    {
        return Procedimiento::create([
            'orden_id' => $orden->id,
            'examen_id' => Examen::factory()->create()->id,
            'sede_id' => $this->sede->id,
            'fecha' => now(),
            'diagnostico_principal' => 'Z017',
            'codigo_finalidad' => '15',
            'codigo_modalidad' => '01',
            'codigo_via_ingreso' => '01',
            'estado' => $estado->value,
        ]);
    }
}

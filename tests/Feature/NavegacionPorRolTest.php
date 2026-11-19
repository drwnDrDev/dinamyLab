<?php

use App\Models\Empleado;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(PaisSeeder::class);
    $this->seed(TipoDocumentoSeeder::class);
    $this->seed(TestDataSeeder::class);
    $this->seed(RolesYPermisosSeeder::class);
    $this->sede = Sede::factory()->create();
});

function usuarioConRoles($test, array $roles): User
{
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole($roles);
    Empleado::factory()->create(['user_id' => $user->id, 'empresa_id' => $test->sede->empresa_id])
        ->sedes()->attach($test->sede->id);

    return $user;
}

it('muestra el acceso rápido a nueva orden en el menú lateral de todas las vistas', function () {
    $prestador = usuarioConRoles($this, ['prestador']);

    foreach (['ordenes', 'resultados', 'procedimientos', 'personas'] as $ruta) {
        $this->actingAs($prestador)->withSession(['sede' => $this->sede])
            ->get(route($ruta))
            ->assertOk()
            ->assertSee('aria-label="Nueva orden"', false)
            ->assertSee('href="'.route('ordenes.create').'"', false);
    }
});

it('limita el menú del agente a órdenes y resultados y oculta la búsqueda global', function () {
    $agente = usuarioConRoles($this, ['agente']);

    $this->actingAs($agente)->withSession(['sede' => $this->sede])
        ->get(route('ordenes'))
        ->assertOk()
        ->assertSee('href="'.route('ordenes').'"', false)
        ->assertSee('href="'.route('resultados').'"', false)
        ->assertDontSee('href="'.route('personas').'"', false)
        ->assertDontSee('href="'.route('procedimientos').'"', false)
        ->assertDontSee('href="'.route('reportes').'"', false)
        ->assertDontSee('action="'.route('search').'"', false)
        ->assertSee('aria-label="Nueva orden"', false);
});

it('no recorta el menú a usuarios que además del rol agente tienen otro rol', function () {
    $mixto = usuarioConRoles($this, ['agente', 'prestador']);

    $this->actingAs($mixto)->withSession(['sede' => $this->sede])
        ->get(route('ordenes'))
        ->assertOk()
        ->assertSee('href="'.route('personas').'"', false)
        ->assertSee('href="'.route('procedimientos').'"', false)
        ->assertSee('action="'.route('search').'"', false);
});

it('no muestra nueva orden a quien no puede crear órdenes', function () {
    $contable = usuarioConRoles($this, ['contable']);
    $contable->givePermissionTo('ver_orden');

    $this->actingAs($contable)->withSession(['sede' => $this->sede])
        ->get(route('ordenes'))
        ->assertOk()
        ->assertDontSee('aria-label="Nueva orden"', false);
});

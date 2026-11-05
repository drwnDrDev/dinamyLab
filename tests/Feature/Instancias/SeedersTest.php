<?php

use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\ServicioHabilitado;
use App\Models\User;
use Database\Seeders\ClienteSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\ServicioHabilitadoSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Spatie\Permission\Models\Role;

test('el seeder de roles es idempotente', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(RolesYPermisosSeeder::class);

    expect(Role::count())->toBe(5);
});

test('el seeder de servicios habilitados carga el catalogo y es idempotente', function () {
    $this->seed(ServicioHabilitadoSeeder::class);
    $cantidadInicial = ServicioHabilitado::count();

    $this->seed(ServicioHabilitadoSeeder::class);

    expect($cantidadInicial)->toBe(157)
        ->and(ServicioHabilitado::count())->toBe($cantidadInicial);

    $this->assertDatabaseHas('servicios_habilitados', [
        'codigo' => 706,
        'nombre' => 'LABORATORIO CLINICO',
        'grupo' => 'APOYO DIAGNOSTICO Y COMPLEMENTACION TERAPEUTICA',
        'codigo_grupo' => '02',
        'activo' => true,
    ]);
    $this->assertDatabaseHas('servicios_habilitados', [
        'codigo' => 712,
        'nombre' => 'TOMA DE MUESTRAS DE LABORATORIO CLINICO',
        'activo' => true,
    ]);
    $this->assertDatabaseHas('servicios_habilitados', [
        'codigo' => 105,
        'activo' => false,
    ]);
    expect(ServicioHabilitado::where('activo', true)->pluck('codigo')->all())
        ->toBe([706, 712]);
});

test('el seeder de cliente crea empresa, sede y empleado del admin', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(TipoDocumentoSeeder::class);
    $this->seed(ClienteSeeder::class);

    $admin = User::where('email', config('instancias.cliente.admin_email'))->first();

    expect(Empresa::count())->toBe(1)
        ->and(Sede::count())->toBe(1)
        ->and($admin)->not->toBeNull()
        ->and(Empleado::where('user_id', $admin->id)->exists())->toBeTrue();
});

test('el admin sembrado inicia sesion y llega al dashboard', function () {
    config([
        'instancias.cliente.admin_email' => 'admin-seeder@example.test',
        'instancias.cliente.admin_password' => 'password-seeder-test',
    ]);

    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(TipoDocumentoSeeder::class);
    $this->seed(ClienteSeeder::class);

    $this->post('/login', [
        'email' => config('instancias.cliente.admin_email'),
        'password' => config('instancias.cliente.admin_password'),
    ])->assertRedirect(route('dashboard'));

    $this->get(route('dashboard'))->assertOk();
});

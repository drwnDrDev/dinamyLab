<?php

use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\ClienteSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Spatie\Permission\Models\Role;

test('el seeder de roles es idempotente', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(RolesYPermisosSeeder::class);

    expect(Role::count())->toBe(5);
});

test('el seeder de cliente crea una sola empresa y su sede', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(ClienteSeeder::class);

    expect(Empresa::count())->toBe(1)
        ->and(Sede::count())->toBe(1)
        ->and(User::where('email', config('instancias.cliente.admin_email'))->exists())->toBeTrue();
});

test('el admin sembrado puede iniciar sesion', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(ClienteSeeder::class);

    $this->post('/login', [
        'email' => config('instancias.cliente.admin_email'),
        'password' => config('instancias.cliente.admin_password'),
    ])->assertRedirect(route('dashboard'));
});

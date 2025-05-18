<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->create([
            'name' => 'Carlos Ramirez',
            'email' => 'admon@dinamycode.com',
            'password' => Hash::make('DinamycodeDC'),
        ]);
        $prestador = User::factory()->create([
            'name' => 'Claudia Patricia Buitrago',
            'email' => 'cbbuitrago69@yahoo.com',
            'password' => Hash::make('Danielito2006'),
        ]);

         $coordinador = User::factory()->create([
            'name' => 'Juan Perez',
            'email' => 'coordinador@dinamycode.com',
            'password' => Hash::make('DinamycodeDC'),
        ]);
        $empleado = User::factory()->create([
            'name' => 'Pedro Perez',
            'email' => 'info@dinamycode.com',
            'password' => Hash::make('DinamycodeDC'),
        ]);

        Role::create(['name' => 'admin']);
        Role::create(['name' => 'agente']);
        Role::create(['name' => 'prestador']);
        Role::create(['name' => 'paciente']);
        Role::create(['name' => 'contable']);
        Permission::create(['name' => 'ver_resultado']);
        Permission::create(['name' => 'crear_persona']);
        Permission::create(['name' => 'editar_persona']);
        Permission::create(['name' => 'ver_persona']);
        Permission::create(['name' => 'ver_orden']);
        Permission::create(['name' => 'eliminar_persona']);
        Permission::create(['name' => 'ver_cuentas']);


        $role = Role::findByName('admin');
        $role->givePermissionTo(Permission::all());

        $role = Role::findByName('prestador');
        $role->givePermissionTo(['ver_resultado', 'crear_persona', 'editar_persona', 'ver_persona', 'ver_orden', 'eliminar_persona']);

        $role = Role::findByName('agente');
        $role->givePermissionTo(['ver_resultado', 'crear_persona', 'editar_persona', 'ver_persona', 'ver_orden']);

        
        $empleado->assignRole('agente');
        $admin->assignRole('admin');
        $prestador->assignRole('prestador');

        $this->call([
            MunicipioSeeder::class,
            EmpresaSeeder::class,
            ExamenSeeder::class,
        ]);

    }
}

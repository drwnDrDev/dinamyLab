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
        Role::create(['name' => 'coordinador']);
        Role::create(['name' => 'empleado']);
        Role::create(['name' => 'prestador']);
        Role::create(['name' => 'paciente']);
        Permission::create(['name' => 'crear_paciente']);
        Permission::create(['name' => 'editar_paciente']);
        Permission::create(['name' => 'eliminar_paciente']);
        Permission::create(['name' => 'ver_paciente']);
        Permission::create(['name' => 'asignar_paciente']);
        Permission::create(['name' => 'ver_asignacion']);
        Permission::create(['name' => 'editar_asignacion']);
        Permission::create(['name' => 'eliminar_asignacion']);
        Permission::create(['name' => 'ver_empleado']);
        Permission::create(['name' => 'crear_empleado']);
        Permission::create(['name' => 'editar_empleado']);
        Permission::create(['name' => 'eliminar_empleado']);
        Permission::create(['name' => 'ver_registro']);
        Permission::create(['name' => 'crear_registro']);
        Permission::create(['name' => 'editar_registro']);
        Permission::create(['name' => 'eliminar_registro']);
        Permission::create(['name' => 'ver_usuario']);
        Permission::create(['name' => 'crear_usuario']);
        Permission::create(['name' => 'editar_usuario']);
        Permission::create(['name' => 'eliminar_usuario']);
        Permission::create(['name' => 'ver_permiso']);
        Permission::create(['name' => 'crear_permiso']);
        Permission::create(['name' => 'editar_permiso']);
        Permission::create(['name' => 'eliminar_permiso']);
        Permission::create(['name' => 'ver_rol']);
        Permission::create(['name' => 'crear_rol']);
        Permission::create(['name' => 'editar_rol']);
        Permission::create(['name' => 'eliminar_rol']);
        Permission::create(['name' => 'gestion_emresa']);
        Permission::create(['name' => 'gestion_sede']);
        Permission::create(['name' => 'gestion_prestador']);

        $role = Role::findByName('admin');
        $role->givePermissionTo(Permission::all());

        $role = Role::findByName('coordinador');
        $role->givePermissionTo(['crear_paciente', 'editar_paciente', 'eliminar_paciente', 'ver_paciente', 'asignar_paciente', 'ver_asignacion', 'editar_asignacion', 'eliminar_asignacion', 'ver_empleado', 'crear_empleado', 'editar_empleado', 'eliminar_empleado', 'ver_registro', 'crear_registro', 'editar_registro', 'eliminar_registro']);

        $role = Role::findByName('empleado');
        $role->givePermissionTo(['ver_paciente', 'ver_asignacion', 'ver_empleado', 'ver_registro', 'crear_registro', 'editar_registro', 'eliminar_registro']);

        $coordinador->assignRole('coordinador');
        $empleado->assignRole('empleado');
        $admin->assignRole('admin');
        $prestador->assignRole('prestador');

    }
}

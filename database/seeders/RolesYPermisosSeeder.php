<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Roles y permisos del sistema. Idempotente: se puede ejecutar varias veces.
 *
 * No asigna roles a usuarios: eso corresponde a ClienteSeeder y DemoSeeder.
 */
class RolesYPermisosSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (['admin', 'agente', 'prestador', 'paciente', 'contable'] as $rol) {
            Role::firstOrCreate(['name' => $rol, 'guard_name' => 'web']);
        }

        $permisos = [
            'ver_resultado', 'crear_persona', 'editar_persona', 'ver_persona',
            'ver_orden', 'eliminar_persona', 'ver_cuentas', 'crear_cuenta',
            'editar_cuenta', 'ver_examen', 'crear_examen', 'editar_examen',
            'ver_empresa', 'ver_facturas',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        Role::findByName('admin')->syncPermissions(Permission::all());

        Role::findByName('prestador')->syncPermissions($permisos);

        Role::findByName('agente')->syncPermissions([
            'ver_resultado', 'crear_persona', 'editar_persona', 'ver_persona', 'ver_orden',
        ]);

        Role::findByName('contable')->syncPermissions(['ver_facturas']);
    }
}

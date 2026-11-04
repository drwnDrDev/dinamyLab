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
            'ver_resultado',
            'crear_persona',
            'editar_persona',
            'ver_persona',
            'eliminar_persona',
            'ver_orden',
            'crear_orden',
            'editar_orden',
            'eliminar_orden',
            'ver_procedimiento',
            'gestionar_procedimiento',
            'eliminar_procedimiento',
            'registrar_resultado',
            'editar_resultado',
            'ver_examen',
            'editar_descripcion_examen',
            'ver_facturas',
            'ver_reportes',
            'ver_cuentas',
            'crear_cuenta',
            'editar_cuenta',
            'crear_examen',
            'editar_examen',
            'ver_empresa',
            'editar_empresa',
            'crear_sedes',
            'editar_sedes',
            'eliminar_sedes',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        Role::findByName('admin')->syncPermissions(Permission::all());

        Role::findByName('prestador')->syncPermissions([
            'ver_resultado',
            'ver_persona', 'crear_persona', 'editar_persona', 'eliminar_persona',
            'ver_orden', 'crear_orden', 'editar_orden', 'eliminar_orden',
            'ver_procedimiento', 'gestionar_procedimiento', 'eliminar_procedimiento',
            'registrar_resultado', 'editar_resultado',
            'ver_examen', 'editar_descripcion_examen',
            'ver_reportes',
        ]);

        Role::findByName('agente')->syncPermissions([
            'ver_resultado',
            'ver_persona', 'crear_persona', 'editar_persona', 'eliminar_persona',
            'ver_orden',
            'ver_facturas',
        ]);

        Role::findByName('contable')->syncPermissions(['ver_facturas']);
    }
}

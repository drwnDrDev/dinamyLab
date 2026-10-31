<?php

namespace Database\Seeders;

use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Datos propios de la instancia: una empresa, su sede y su usuario admin.
 *
 * Todo sale de config/instancias.php (variables CLIENTE_* del .env), de modo
 * que el código no conoce a ningún cliente. Idempotente.
 *
 * Requiere que RolesYPermisosSeeder se haya ejecutado antes.
 */
class ClienteSeeder extends Seeder
{
    public function run(): void
    {
        $datos = config('instancias.cliente');

        $empresa = Empresa::firstOrCreate(
            ['nit' => $datos['nit']],
            ['razon_social' => $datos['razon_social']]
        );

        Sede::firstOrCreate(
            ['codigo_prestador' => $datos['codigo_prestador']],
            ['nombre' => $datos['sede'], 'empresa_id' => $empresa->id]
        );

        $admin = User::firstOrCreate(
            ['email' => $datos['admin_email']],
            ['name' => 'Administrador', 'password' => $datos['admin_password']]
        );

        $admin->assignRole('admin');
    }
}

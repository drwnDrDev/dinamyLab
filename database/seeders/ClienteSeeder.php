<?php

namespace Database\Seeders;

use App\Cargo;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\TipoDocumento;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Datos propios de la instancia: una empresa, su sede y su usuario admin.
 *
 * Todo sale de config/instancias.php (variables CLIENTE_* del .env), de modo
 * que el código no conoce a ningún cliente. Idempotente.
 *
 * Requiere que RolesYPermisosSeeder y TipoDocumentoSeeder se hayan ejecutado antes
 * (CatalogoBaseSeeder ejecuta ambos).
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

        $sede = Sede::firstOrCreate(
            ['codigo_prestador' => $datos['codigo_prestador']],
            ['nombre' => $datos['sede'], 'empresa_id' => $empresa->id]
        );

        $admin = User::firstOrCreate(
            ['email' => $datos['admin_email']],
            ['name' => 'Administrador', 'password' => $datos['admin_password']]
        );

        $admin->assignRole('admin');

        // Sin Empleado el dashboard falla (sedes/select.blade.php accede a
        // $empleado->user) y el admin no puede elegir sede.
        $empleado = Empleado::firstOrCreate(
            ['numero_documento' => $datos['admin_documento']],
            [
                'cargo' => Cargo::ADMINISTRADOR->value,
                'tipo_documento_id' => TipoDocumento::idPorCodigoRips('CC'),
                'empresa_id' => $empresa->id,
                'user_id' => $admin->id,
            ]
        );

        $empleado->sedes()->syncWithoutDetaching([$sede->id]);
    }
}

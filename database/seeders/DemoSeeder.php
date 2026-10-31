<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Datos de demostración para desarrollo local. No usar en producción.
 *
 * Reproduce el seeding histórico: catálogo completo, usuarios de ejemplo y
 * las dos empresas de prueba (Biotek y San Pablo).
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CatalogoBaseSeeder::class);

        $admin = User::factory()->create([
            'name' => 'Carlos Ramirez',
            'email' => 'admon@dinamycode.com',
            'password' => Hash::make('DinamycodeDC'),
        ]);
        $prestador = User::factory()->create([
            'name' => 'Claudia Patricia Buitrago',
            'email' => 'cpbuitrago69@yahoo.com',
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
        $usuarioSanPablo = User::factory()->create([
            'name' => 'Pilar Rocha',
            'email' => 'pilar_rocha@dinamycode.com',
            'password' => Hash::make('SanPabloL7'),
        ]);

        $empleado->assignRole('agente');
        $admin->assignRole('admin');
        $usuarioSanPablo->assignRole('prestador');
        $prestador->assignRole('prestador');
        $coordinador->assignRole('contable');

        $this->call(EmpresaSeeder::class);
    }
}

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
            'name' => 'Admin Genenral',
            'email' => 'admon@dinamycode.com',
            'password' => Hash::make('DinamycodeDC'),
        ]);
        $prestador = User::factory()->create([
            'name' => 'Claudia Patricia Buitrago',
            'email' => 'cpbuitrago69@yahoo.com',
            'password' => Hash::make('Danielito2006'),
        ]);
        $coordinador = User::factory()->create([
            'name' => 'Toma de muestras AM',
            'email' => 'coordinador1@dinamycode.com',
            'password' => Hash::make('BiotekBosa26'),
        ]);
        $empleado = User::factory()->create([
            'name' => 'Toma de muestras PM',
            'email' => 'coordinador2@dinamycode.com',
            'password' => Hash::make('BiotekBosa20'),
        ]);

        $empleado->assignRole('agente');
        $admin->assignRole('admin');
        $prestador->assignRole('prestador');
        $coordinador->assignRole('agente');

        $this->call(EmpresaSeeder::class);
    }
}

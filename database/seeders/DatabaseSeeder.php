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
            'email' => 'cpbuitrago69@yahoo.com',
            'password' => Hash::make('DinamycodeDC'),
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
        Permission::create(['name' => 'crear_cuenta']);
        Permission::create(['name' => 'editar_cuenta']);
        Permission::create(['name' => 'ver_examen']);
        Permission::create(['name' => 'crear_examen']);
        Permission::create(['name' => 'editar_examen']);
        Permission::create(['name' => 'ver_empresa']);
        Permission::create(['name' => 'ver_facturas']);


        $role = Role::findByName('admin');
        $role->givePermissionTo(Permission::all());

        $role = Role::findByName('prestador');
        $role->givePermissionTo(['ver_resultado', 'crear_persona', 'editar_persona', 'ver_persona', 'ver_orden', 'eliminar_persona', 'ver_cuentas', 'crear_cuenta', 'editar_cuenta', 'ver_examen', 'crear_examen', 'editar_examen', 'ver_empresa','ver_facturas']);

        $role = Role::findByName('agente');
        $role->givePermissionTo(['ver_resultado', 'crear_persona', 'editar_persona', 'ver_persona', 'ver_orden']);

        $role = Role::findByName('contable');
        $role->givePermissionTo(['ver_facturas']);

        $empleado->assignRole('agente');
        $admin->assignRole('admin');

        $prestador->assignRole('prestador');
        $coordinador->assignRole('contable');

        $this->call([
            TipoDocumentoSeeder::class,
            MunicipioSeeder::class,
            EmpresaSeeder::class,
            ExamenSeeder::class,
            HemogramaCompletoSeeder::class,
            FrotisUretraSeeder::class,
            UroanalisisSeeder::class,
            GlucosaSeeder::class,
            TrigliceridosSeeder::class,
            ColesterolTotalSeeder::class,
            ColesterolHDLSeeder::class,
            ColesterolLDLSeeder::class,
            AcidoUricoSeeder::class,
        ]);

    }
}

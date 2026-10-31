<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * En desarrollo se siembran los datos de demostración. En una instancia de
     * cliente se ejecutan explícitamente CatalogoBaseSeeder y ClienteSeeder
     * (ver docs/DESPLIEGUE_INSTANCIAS.md).
     */
    public function run(): void
    {
        $this->call(DemoSeeder::class);
    }
}

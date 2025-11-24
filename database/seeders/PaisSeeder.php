<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('paises')->insert([
            [
                'codigo_iso' => '170',
                'codigo_iso_2' => 'CO',
                'codigo_iso_3' => 'COL',
                'nombre' => 'Colombia',
                'nivel' => 0,
            ],
            [
                'codigo_iso' => '840',
                'codigo_iso_2' => 'US',
                'codigo_iso_3' => 'USA',
                'nombre' => 'Estados Unidos',
                'nivel' => 0,
            ],
            [
                'codigo_iso' => '076',
                'codigo_iso_2' => 'BR',
                'codigo_iso_3' => 'BRA',
                'nombre' => 'Brasil',
                'nivel' => 0,
            ],
        ]);
    }
}

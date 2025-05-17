<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\models\Examen;

class ExamenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Examen::create([
            'nombre'=>'Cuadro Hematico',
            'cup'=>'4210',
            'valor'=>18000,
        ]);
        Examen::create([
            'nombre'=>'Grupo Sanguineo RH',
            'cup'=>'4215',
            'valor'=>10000,
        ]);
        Examen::create([
            'nombre'=>'Serología',
            'cup'=>'4211',
            'valor'=>20000,
        ]);
    }
}

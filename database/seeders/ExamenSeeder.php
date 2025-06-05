<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\models\Examen;
use Illuminate\Support\Facades\DB;
use App\Models\Eps;

class ExamenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $epsCSV = "EPS
    $epsCSV ="Dusakawi,Salud Bolívar,Savia Salud,Mutual Ser,Salud Total,Coomeva,Compensar,Sanitas,Aliansalud,SOS(Servicio Occidental de Salud)";

        $epsArray = explode(',', $epsCSV);
        $epsArray = array_map(function($prestador){
            return[
                'nombre' => $prestador,
                'verificada' => true,
            ];
        }
        , $epsArray);

        DB::table('eps')->insert($epsArray);
        Examen::create([
            'nombre'=>'Hemoclasificación',
            'cup'=>'4212',
            'valor'=>15000,
            'plantilla'=>'[{\"parametro\": \"Grupo sanguíneo\", \"resultado\": {\"tipo\": \"lista\", \"nombre\": \"gs\", \"items\": [\"A\", \"B\", \"AB\", \"O\"]}}, {\"parametro\": \"RH\", \"resultado\": {\"tipo\": \"lista\", \"nombre\": \"rh\", \"items\": [\"positivo\", \"negativo\"]}}]'
        ]);
        Examen::create([
            'nombre'=>'Colesterol Total',
            'cup'=>'4213',
            'valor'=>15000,
        ]);
        Examen::create([
            'nombre'=>'Trigliceridos',
            'cup'=>'4214',
            'valor'=>15000,
        ]);
        Examen::create([
            'nombre'=>'Creatinina',
            'cup'=>'4216',
            'valor'=>15000,
        ]);
        Examen::create([
            'nombre'=>'Urea',
            'cup'=>'4217',
            'valor'=>15000,
        ]);

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

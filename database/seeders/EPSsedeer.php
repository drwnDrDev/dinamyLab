<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\EPS;
use App\Models\TipoAfiliacion;

class EPSsedeer extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
                // $epsCSV = "EPS
        $epsCSV = "Dusakawi,Salud Bolívar,Savia Salud,Mutual Ser,Salud Total,Coomeva,Compensar,Sanitas,Aliansalud,SOS(Servicio Occidental de Salud)";

        $epsArray = explode(',', $epsCSV);
        $epsArray = array_map(
            function ($prestador) {
                return [
                    'nombre' => $prestador,
                    'verificada' => true,
                ];
            },
            $epsArray
        );

        DB::table('eps')->insert($epsArray);

$tipoAfiliacion = "01	Contributivo cotizante
02	Contributivo beneficiario
03	Contributivo adicional
04	Subsidiado
05	No afiliado
06	Especial o Excepcion cotizante
07	Especial o Excepcion beneficiario
08	Personas privadas de la libertad Fondo Nacional de Salud
09	Tomador - Amparado ARL
10	Tomador - Amparado SOAT
11	Tomador - Amparado Planes  voluntarios de salud
12	Particular
13	Especial o Exepcion no cotizante Ley 352 de 1997";
        $tipoAfiliacionArray = explode("\n", $tipoAfiliacion);
        $tipoAfiliacionArray = array_map(
            function ($tipo) {
                $parts = explode("\t", $tipo);
                return [
                    'codigo' => trim($parts[0]),
                    'descripcion' => trim($parts[1]),
                ];
            },
            $tipoAfiliacionArray
        );

        DB::table('tipos_afiliaciones')->insert($tipoAfiliacionArray);


    }
}

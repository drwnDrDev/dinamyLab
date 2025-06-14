<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\models\Examen;
use Illuminate\Support\Facades\DB;

use App\Models\Opcion;
use App\Models\Parametro;

class ExamenSeeder extends Seeder
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
        $examenesData = array( // Renombrado para evitar confusión con la instancia del modelo
            array(
                'nombre' => 'Hemoclasificación',
                'CUP' => '911017',
                'valor' => '10000.00',
                'descripcion' => 'permite determinar los grupos sanguíneos en el sistema ABO y el factor RH',
                'nombre_alternativo' => 'RH',
                'parametros' => array(
                    array('nombre' => 'Grupo Sanguineo', 'slug' => 'gs', 'tipo_dato' => 'select', 'orden' => 1, 'opciones' => array('A', 'B', 'O', 'AB')),
                    array('nombre' => 'RH', 'slug' => 'rh', 'tipo_dato' => 'select', 'orden' => 2, 'opciones' => array('Positivo', 'Negativo'))
                )
            ),
            array(
                'nombre' => 'Prueba de embarazo',
                'CUP' => '904508',
                'valor' => '16000.00',
                'descripcion' => 'prueba de sangre cuantitativa mide la cantidad exacta de GCH en la sangre, y una prueba de sangre cualitativa de GCH le da un simple sí o no respuesta a si usted está embarazada o no',
                'nombre_alternativo' => 'Test de embarazo en sangre o en orina',
                'parametros' => array(
                    array('nombre' => 'Resultado', 'slug' => 'resultado', 'tipo_dato' => 'select', 'orden' => 1, 'opciones' => array('Positivo', 'Negativo')),
                    array('nombre' => 'FUR', 'slug' => 'fur', 'tipo_dato' => 'date', 'orden' => 2)
                )
            )
        );

        foreach ($examenesData as $examenData) {
            // Crea el registro del Examen en la tabla 'examenes'
            $examen = Examen::create([
                'nombre' => $examenData['nombre'],
                'cup' => $examenData['CUP'],
                'valor' => $examenData['valor'],
                'nombre_alternativo' => $examenData['nombre_alternativo'] ?? null,
                'descripcion' => $examenData['descripcion'] ?? null,
            ]);

            // Itera sobre los parámetros definidos para cada examen
            foreach ($examenData['parametros'] as $parametroData) {
                // Crea el registro del Parámetro en la tabla 'parametros'
                $parametro = Parametro::create([
                    'nombre' => $parametroData['nombre'],
                    'slug' => $parametroData['slug'],
                    'tipo_dato' => $parametroData['tipo_dato'],
                    'orden' => $parametroData['orden']
                ]);

                // Adjunta el Parámetro recién creado al Examen
                // Esto crea la entrada en la tabla intermedia 'examen_parametro' (o 'resultados')
                // Se usa el ID del parámetro ($parametro->id) para adjuntarlo al examen ($examen)
                $examen->parametros()->attach($parametro->id);


                // Si el parámetro tiene opciones, itera y crea los registros de Opcion
                if (isset($parametroData['opciones'])) {
                    foreach ($parametroData['opciones'] as $opcionValor) {
                        Opcion::create([
                            'parametro_id' => $parametro->id, // Asocia la opción con el parámetro correcto
                            'valor' => $opcionValor // Usar 'valor' como nombre de columna para la opción
                        ]);
                    }
                }
            }
        }
    }
}

<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Examen;
use App\Models\Opcion;
use App\Models\Parametro;
use App\Models\ValorReferencia;
use Illuminate\Support\Str; // Para generar slugs

class AcidoUricoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define los datos del examen principal
        $examenData = [
            'nombre' => 'Ácido Úrico',
            'CUP' => '903801',
            'valor' => '18000.00', // Un valor de ejemplo
            'descripcion' => 'mide la cantidad de ácido úrico en una muestra de sangre u orina. El ácido úrico es un producto de desecho que se forma cuando el cuerpo descompone purinas, que son sustancias que se encuentran en ciertos alimentos y bebidas. Un nivel elevado de ácido úrico puede indicar problemas de salud como gota o enfermedad renal.',
            'nombre_alternativo' => null,
        ];

        // Crea el examen
        $examen = Examen::firstOrCreate(
            ['nombre' => $examenData['nombre']], // Busca por nombre para evitar duplicados si se ejecuta varias veces
            $examenData
        );

        // Define los datos de los parámetros y referencias
        $parametrosYReferencias = json_decode('    {
        "data": [
            {
                "parametro": "Ácido úrico",
                "resultado": {
                    "nombre": "acido_urico",
                    "tipo": "number"
                },
                "unidades": "mg/dL",
                "referencia":{

            "hombres":{
                        "salida":"2.5 - 7.0",
                        "minimo":2.5,
                        "maximo":7.0
                    },
            "mujeres":{
                    "salida":"1.5 - 6.0",
                    "minimo":1.5,
                    "maximo":6.0
                 }
                }
            }
        ]
    }', true)['data']; // El 'true' es importante para obtener un array asociativo

        // Función auxiliar para procesar parámetros (incluyendo sub-parámetros)
        $processParam = function($paramData, $examenInstance,$orden) {
            // Genera un slug basado en el nombre del parámetro (o nombre del grupo si es un grupo)
            $slug = Str::slug($paramData['resultado']['nombre'] ?? $paramData['grupo'] ?? $paramData['parametro']);

            // Crea o encuentra el Parámetro
            $parametro = Parametro::create(
                [
                    'nombre' => $paramData['parametro'],
                    'grupo'=> $paramData['grupo']??null,
                    'slug' => $slug,
                    'tipo_dato' => $paramData['resultado']['tipo'] ?? 'text', // Por defecto 'text' si no hay tipo
                    'unidades' => $paramData['unidades'] ?? null,
                    'metodo'=>$paramData['subtitulo'] ?? null,
                    'examen_id' => $examenInstance->id,
                    'posicion' => $orden, // Asignar la posición
                ]
            );

            // Procesa las referencias si existen
            if (isset($paramData['referencia'])) {
                foreach ($paramData['referencia'] as $demografia => $refData) { // Cambiado $grupoPoblacional a $demografia
                    ValorReferencia::firstOrCreate(
                        [
                            'parametro_id' => $parametro->id,
                            'demografia' => $demografia, // Usamos 'demografia' aquí
                        ],
                        [
                            'salida' => $refData['salida'] ?? null, // Columna 'salida'
                            'min' => $refData['minimo'] ?? null,   // Columna 'min'
                            'max' => $refData['maximo'] ?? null,   // Columna 'max'
                        ]
                    );
                }
            }

            if (isset($paramData['resultado']['items'])) {

                    foreach ($paramData['resultado']['items'] as $opcionValor) {
                        Opcion::create([
                            'parametro_id' => $parametro->id, // Asocia la opción con el parámetro correcto
                            'valor' => $opcionValor // Usar 'valor' como nombre de columna para la opción
                        ]);
                    }
                }

        };

        $orden = 0;

        // Itera sobre los datos y procesa cada parámetro/grupo
        foreach ($parametrosYReferencias as $item) {
            $orden++;
            $processParam($item, $examen,$orden);
        }

    }
}

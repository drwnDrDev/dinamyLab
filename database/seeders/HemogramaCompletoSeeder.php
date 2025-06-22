<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Examen;
use App\Models\Parametro;
use App\Models\ValorReferencia;
use Illuminate\Support\Str; // Para generar slugs

class HemogramaCompletoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define los datos del examen principal
        $examenData = [
            'nombre' => 'Cuadro Hemático',
            'CUP' => '902207',
            'valor' => '18000.00', // Un valor de ejemplo
            'descripcion' => 'Este examen evalúa los componentes de la sangre, incluyendo glóbulos rojos, glóbulos blancos y plaquetas.',
            'nombre_alternativo' => 'Hemograma',
        ];

        // Crea el examen
        $examen = Examen::firstOrCreate(
            ['nombre' => $examenData['nombre']], // Busca por nombre para evitar duplicados si se ejecuta varias veces
            $examenData
        );

        // Define los datos de los parámetros y referencias
        $parametrosYReferencias = json_decode('{
            "data": [
                {
                    "parametro": "hematocrito",
                    "resultado": { "tipo": "number", "nombre": "hto" },
                    "unidades": "%",
                    "referencia": {
                        "adultos": { "salida": "42-52", "minimo": 42, "maximo": 52 },
                        "menores": { "salida": "35-44", "minimo": 35, "maximo": 44 }
                    }
                },
                {
                    "parametro": "hemoglobina",
                    "resultado": { "tipo": "number", "nombre": "hb" },
                    "unidades": "g%",
                    "referencia": {
                        "adultos": { "salida": "13.5-16.5", "minimo": 13.5, "maximo": 16.5 },
                        "menores": { "salida": "11.0-13.5", "minimo": 11, "maximo": 13.5 }
                    }
                },
                {
                    "parametro": "recuento de leucocitos",
                    "resultado": { "tipo": "number", "nombre": "leu" },
                    "unidades": "leu/mm³",
                    "referencia": {
                        "adultos": { "salida": "5000-10000", "minimo": 5000, "maximo": 10000 },
                        "menores": { "salida": "7000-13000", "minimo": 7000, "maximo": 13000 }
                    }
                },
                {           "grupo": "recuento diferencial",
                            "parametro": "neutrofilos",
                            "resultado": { "tipo": "number", "nombre": "neutrofilos" },
                            "unidades": "%",
                            "referencia": {
                                "adultos": { "salida": "52-67", "minimo": 52, "maximo": 67 },
                                "menores": { "salida": "35-60", "minimo": 35, "maximo": 60 }
                            }
                },
                {
                        "grupo": "recuento diferencial",
                            "parametro": "linfocitos",
                            "resultado": { "tipo": "number", "nombre": "linfocitos" },
                            "unidades": "%",
                            "referencia": {
                                "adultos": { "salida": "27-42", "minimo": 27, "maximo": 42 },
                                "menores": { "salida": "25-50", "minimo": 25, "maximo": 50 }
                            }
                        },
                        {
                         "grupo": "recuento diferencial",
                            "parametro": "eosinofilos",
                            "resultado": { "tipo": "number", "nombre": "eosinofilos" },
                            "unidades": "%",
                            "referencia": {
                                "adultos": { "salida": "0-3", "maximo": 3 }
                            }
                        },
                        {
                         "grupo": "recuento diferencial",
                            "parametro": "monocitos",
                            "resultado": { "tipo": "number", "nombre": "monocitos" },
                            "unidades": "%",
                            "referencia": {
                                "adultos": { "salida": "3-7", "minimo": 3, "maximo": 7 }
                            }
                        },
                        {
                         "grupo": "recuento diferencial",
                            "parametro": "celulas inmaduras",
                            "resultado": { "tipo": "number", "nombre": "inmaduras" },
                            "unidades": "%"
                        },

                {
                    "parametro": "recuento de plaquetas",
                    "resultado": { "tipo": "number", "nombre": "rto_plaquetas" },
                    "unidades": "plaq/mm³",
                    "referencia": {
                        "adultos": { "salida": "150000-450000", "minimo": 150000, "maximo": 450000 }
                    }
                },
                {
                    "parametro": "vsg",
                    "resultado": { "tipo": "number", "nombre": "vsg" },
                    "unidades": "mm/h",
                    "referencia": {
                        "adultos": { "salida": "0-22", "minimo": 0, "maximo": 22 }
                    }
                },
                {
                    "parametro": "observaciones",
                    "resultado": { "tipo": "textarea", "nombre": "observacion", "items": [ "muestra contaminda, se solicita nueva muestra" ] }
                }
            ]
        }', true)['data']; // El 'true' es importante para obtener un array asociativo

        // Función auxiliar para procesar parámetros (incluyendo sub-parámetros)
        $processParam = function($paramData, $examenInstance,$orden) {
            // Genera un slug basado en el nombre del parámetro (o nombre del grupo si es un grupo)
            $slug = Str::slug($paramData['resultado']['nombre'] ?? $paramData['grupo'] ?? $paramData['parametro']);

            // Crea o encuentra el Parámetro
            $parametro = Parametro::firstOrCreate(
                ['slug' => $slug], // Usar slug para buscar, asumiendo que es único
                [
                    'nombre' => $paramData['parametro'],
                    'grupo'=> $paramData['grupo']??null,
                    'slug' => $slug,
                    'tipo_dato' => $paramData['resultado']['tipo'] ?? 'text', // Por defecto 'text' si no hay tipo
                    'unidades' => $paramData['unidades'] ?? null,
                    'metodo'=>$paramData['subtitulo'] ?? null,
                ]
            );

            // Adjunta el parámetro al examen
            $examenInstance->parametros()->syncWithoutDetaching([
                $parametro->id => ['posicion' => $orden]
            ]);

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


        };

        $orden = 0;

        // Itera sobre los datos y procesa cada parámetro/grupo
        foreach ($parametrosYReferencias as $item) {
            $orden++;
            $processParam($item, $examen,$orden);
        }

    }
}

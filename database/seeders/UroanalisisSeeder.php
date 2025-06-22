<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Examen;
use App\Models\Opcion;
use App\Models\Parametro;
use App\Models\ValorReferencia;
use Illuminate\Support\Str; // Para generar slugs

class UroanalisisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define los datos del examen principal
        $examenData = [
            'nombre' => 'Uroanalisis',
            'CUP' => '907106',
            'valor' => '18000.00', // Un valor de ejemplo
            'descripcion' => 'Ayuda a identificar infecciones del tracto urinario, enfermedades renales, diabetes, y otras condiciones metabólicas.',
            'nombre_alternativo' => 'Parcial de orina',
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
                    "grupo": "examen fisico-quimico",
                    "parametro": "color",
                    "resultado": {
                        "tipo": "list",
                        "nombre": "color",
                        "items": [
                            "hidrico",
                            "amarillo",
                            "ambar",
                            "amarillo intenso",
                            "rojo"
                        ]
                    }
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "aspecto",
                    "resultado": {
                        "tipo": "list",
                        "nombre": "aspecto",
                        "items": [
                            "lig turbio",
                            "turbio",
                            "limpido"
                        ]
                    }
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "densidad",
                    "resultado": {
                        "tipo": "number",
                        "nombre": "densidad"
                    },
                    "unidades": "g/dL"
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "pH",
                    "resultado": {
                        "tipo": "number",
                        "nombre": "ph"
                    }
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "glucosa",
                    "resultado": {
                        "tipo": "text",
                        "default": "negativo",
                        "nombre": "glucosa"
                    },
                    "unidades": "mg/dL"
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "cetonas",
                    "resultado": {
                        "tipo": "text",
                        "default": "negativo",
                        "nombre": "cetonas"
                    },
                    "unidades": "mg/dL"
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "leucocito esterasa",
                    "resultado": {
                        "tipo": "select",
                        "items":["positivo","negativo"],
                        "nombre": "leucocito"
                    }

                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "proteinas",
                    "resultado": {
                        "tipo": "text",
                        "default": "negativo",
                        "nombre": "proteinas"
                    },
                    "unidades": "mg/dL"
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "pigmentos biliares",
                    "resultado": {
                        "tipo": "text",
                        "default": "negativo",
                        "nombre": "pigmentos"
                    },
                    "unidades": "mg/dL"
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "hemoglobina",
                    "resultado": {
                        "tipo": "text",
                        "default": "negativo",
                        "nombre": "hemoglobina"
                    }
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "nitritos",
                    "resultado": {
                        "tipo": "select",
                        "items":["positivo","negativo"],
                        "nombre": "nitritos"
                    }
                },
                {
                    "grupo": "examen fisico-quimico",
                    "parametro": "urobilinogeno",
                    "resultado": {
                        "tipo": "text",
                        "default": "normal",
                        "nombre": "urobilinogeno"
                    },
                    "unidades": "mg/dL"
                },
                {
                    "grupo": "examen microscopico",
                    "parametro": "cel epiteliales",
                    "resultado": {
                        "tipo": "text",
                        "nombre": "epiteliales"
                    },
                    "unidades": "x campo"
                },
                {
                     "grupo": "examen microscopico",
                    "parametro": "leucocitos",
                    "resultado": {
                        "tipo": "text",
                        "nombre": "leucocitos"
                    },
                    "unidades": "x campo"
                },
                {
                     "grupo": "examen microscopico",
                    "parametro": "hematies",
                    "resultado": {
                        "tipo": "text",
                        "nombre": "hematies"
                    },
                    "unidades": "x campo"
                },
                {
                     "grupo": "examen microscopico",
                    "parametro": "bacterias",
                    "resultado": {
                        "tipo": "text",
                        "nombre": "bacterias"
                    }
                },
                {
                     "grupo": "examen microscopico",
                    "parametro": "moco",
                    "resultado": {
                        "tipo": "text",
                        "nombre": "moco"
                    }
                },

        {
            "parametro": "observaciones",
            "resultado": {
                "tipo": "textarea",
                "nombre": "observacion"
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
                    'posicion' => $orden,
                    'examen_id' => $examenInstance->id,
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
            };


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

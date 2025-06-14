<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Examen;
use App\Models\Parametro;
use App\Models\ValorReferencia;
use Illuminate\Support\Str; // Para generar slugs

class FrotisUretraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define los datos del examen principal
        $examenData = [
            'nombre' => 'Frotis de secreción uretral',
            'CUP' => '901107',
            'valor' => '18000.00', // Un valor de ejemplo
            'descripcion' => 'técnica de tinción bacteriana que diferencia las bacterias en dos grupos principales: grampositivas y gramnegativas, según la estructura de sus paredes celulares',
            'nombre_alternativo' => 'Coloración de Gram',
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
    "parametro":"Coloración de gram",
    "resultado":{
        "tipo":"datalist",
        "nombre":"gram_a",
        "items":[
            "cocos Gram positivos aislados escasos",
            "negativo para diplococos gram Negativos intra y extra celulares"
        ]
    }
},{
"parametro":"reacción leucocitaria",
"resultado":{
    "tipo":"lista",
    "nombre":"r_leuc",
    "items":["escasa","moderada","aumentada"]
}
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
                ]
            );

            // Adjunta el parámetro al examen
            $examenInstance->parametros()->syncWithoutDetaching([
                $parametro->id => ['orden' => $orden]
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
                            // Las columnas 'sexo', 'edad_min', 'edad_max', 'unidad', 'descripcion' ya no están en tu migración
                            // Si las necesitas, tu migración de valor_referencias debe incluirlas
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

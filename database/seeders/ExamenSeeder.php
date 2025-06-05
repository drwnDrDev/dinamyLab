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
        $examens = array(
            array('nombre' => 'Hemoclasificación', 'CUP' => '911017', 'valor' => '10000.00', 'descripcion' => 'permite determinar los grupos sanguíneos en el sistema ABO y el factor RH', 'nombre_alternativo' => 'RH', 'componente' => 'rh'),
            array('nombre' => 'Prueba de embarazo', 'CUP' => '904508', 'valor' => '16000.00', 'descripcion' => 'prueba de sangre cuantitativa mide la cantidad exacta de GCH en la sangre, y una prueba de sangre cualitativa de GCH le da un simple sí o no respuesta a si usted está embarazada o no', 'nombre_alternativo' => 'Test de embarazo en sangre o en orina', 'componente' => 'gravitest'),
            array('nombre' => 'Cuadro Hematico', 'CUP' => '902207', 'valor' => '16000.00', 'descripcion' => 'Conjunto de pruebas de laboratorio médico realizadas a la sangre del paciente con el fin de obtener información sobre el número, composición y proporciones de los elementos figurados de la sangre. ', 'nombre_alternativo' => 'CH', 'componente' => 'cuadro_hematico'),
            array('nombre' => 'Frotis de secreción uretral ', 'CUP' => '901107', 'valor' => '16000.00', 'descripcion' => 'análisis de sangre que mide la cantidad de glóbulos blancos en el cuerpo. Los glóbulos blancos, también llamados leucocitos', 'nombre_alternativo' => 'COLORACIÓN DE GRAM', 'componente' => 'frotis_uretral'),
            array('nombre' => 'uroanalisis', 'CUP' => '907106', 'valor' => '16000.00', 'descripcion' => '', 'nombre_alternativo' => 'parcial de orina', 'componente' => 'uroanalisis'),
            array('nombre' => 'Glicemia', 'CUP' => '903841', 'valor' => '16000.00', 'descripcion' => 'concentración de glucosa libre en la sangre, ​​ suero o plasma sanguíneo', 'nombre_alternativo' => 'glucosa en la sangre', 'componente' => 'glicemia'),
            array('nombre' => 'Colesterol Total', 'CUP' => '903818', 'valor' => '16000.00', 'descripcion' => 'Cantidad total de colesterol en la sangre. Incluye ambos tipos: El colesterol de lipoproteína de baja densidad y el colesterol de lipoproteína de alta densidad ', 'nombre_alternativo' => 'colesterol en la sangre', 'componente' => 'colesterol_total'),
            array('nombre' => 'Triglicéridos', 'CUP' => '903868', 'valor' => '16000.00', 'descripcion' => 'cantidad de triglicéridos(son un tipo de grasa) en la sangre', 'nombre_alternativo' => 'lípidos', 'componente' => 'trigliceridos'),
            array('nombre' => 'Colesterol LDL', 'CUP' => '903816', 'valor' => '16000.00', 'descripcion' => 'se le llama colesterol "malo" porque un nivel alto de LDL lleva a una acumulación de colesterol en las arteria', 'nombre_alternativo' => '', 'componente' => 'colesterol_ldl'),
            array('nombre' => 'Colesterol HDL', 'CUP' => '903815', 'valor' => '16000.00', 'descripcion' => 'se le llama colesterol "bueno" porque transporta el colesterol de otras partes de su cuerpo a su hígado. Su hígado luego elimina el colesterol de su cuerpo,El HDL desempeña un papel crucial en la prevención de la aterosclerosis, una enfermedad que estrecha y endurece las arterias, aumentando el riesgo de ataques cardíacos y accidentes cerebrovasculares', 'nombre_alternativo' => 'lipoproteínas de alta densidad', 'componente' => 'colesterol_hdl'),
            array('nombre' => 'Ácido Úrico', 'CUP' => '903801', 'valor' => '16000.00', 'descripcion' => 'mide la cantidad de ácido úrico en una muestra de sangre u orina', 'nombre_alternativo' => NULL, 'componente' => 'acido_urico'),
            array('nombre' => 'Frotis de flujo vaginal', 'CUP' => '901107', 'valor' => '16000.00', 'descripcion' => '', 'nombre_alternativo' => 'Coloracion de Gram', 'componente' => 'frotis_de_flujo_vaginal'),
            array('nombre' => 'Coproanalisis', 'CUP' => '902207', 'valor' => '16000.00', 'descripcion' => 'análisis de heces o análisis de materia fecal, es un examen de laboratorio que analiza una muestra de materia fecal para detectar sangre oculta, bacterias, parásitos, inflamación, sobrecrecimiento de bacterias o levaduras (hongos), y presencia o infección con bacterias.', 'nombre_alternativo' => 'Coprológico', 'componente' => 'coproanalisis'),
            array('nombre' => 'Frotis de garganta', 'CUP' => '901305', 'valor' => '12000.00', 'descripcion' => 'prueba de laboratorio que identifica microorganismos que pueden causar una infección en la garganta', 'nombre_alternativo' => NULL, 'componente' => 'frotis_de_garganta'),
            array('nombre' => 'Koh Piel y Uñas', 'CUP' => '906002', 'valor' => '12000.00', 'descripcion' => 'examen cutáneo simple que se utiliza para identificar infecciones fúngicas en las uñas, la piel y el cabello. La prueba consiste en tratar muestras de una zona infectada con KOH, que disuelve las células de la piel y deja las células del hongo visibles bajo un microscopio', 'nombre_alternativo' => 'hidróxido de potasio', 'componente' => 'koh_piel_y_unas'),
            array('nombre' => 'factor reumatoideo', 'CUP' => '906911', 'valor' => '16000.00', 'descripcion' => 'anticuerpo que puede indicar trastornos autoinmunitarios o infecciones', 'nombre_alternativo' => 'RA TEST', 'componente' => 'factor_reumatoideo'),
            array('nombre' => 'Proteína C reactiva', 'CUP' => '906914', 'valor' => '16000.00', 'descripcion' => 'nivel de esta proteína en la sangre, que puede indicar inflamación en el cuerpo', 'nombre_alternativo' => 'PCR', 'componente' => 'proteina_c_reactiva'),
            array('nombre' => 'Antiestreptolisina', 'CUP' => '906002', 'valor' => '16000.00', 'descripcion' => 'La antiestreptolisina es un anticuerpo que reconoce sustancias extrañas al organismo que son producidas por la bacteria Streptococcus pyogenes, el cual es responsable de enfermedades como la faringitis y sus complicaciones como la fiebre reumática y glomerulonefritis, siendo la antiestreptolisina O (ASLO) la más utilizada. ', 'nombre_alternativo' => 'Astos', 'componente' => 'antiestreptolisina'),
            array('nombre' => 'Glicemia Post-prandial', 'CUP' => '903843', 'valor' => '32000.00', 'descripcion' => 'Es el nivel de glucosa en la sangre después de comer. Se mide 1,5 o 2 horas después de comer y no debe superar los 160 mg/dl. Los valores vuelven a la normalidad pasadas 3 horas de la ingesta.', 'nombre_alternativo' => 'glucemia postprandial (GP)', 'componente' => 'glicemia_post_pradial'),
            array('nombre' => 'Frotis de secreción', 'CUP' => '901107', 'valor' => '16000.00', 'descripcion' => 'usado para ayudar a identificar bacterias. Se puede tomar una muestra para evaluación de los fluidos corporales que normalmente no contienen bacterias, tales como sangre, orina o líquido cefalorraquídeo', 'nombre_alternativo' => 'COLORACIÓN DE GRAM', 'componente' => 'frotis_de_secrecion'),
            array('nombre' => 'Serologia (Prueba no treponemica) VDRL', 'CUP' => '906915', 'valor' => '16000.00', 'descripcion' => 'comprueban la presencia o el nivel de anticuerpos específicos en la sangre', 'nombre_alternativo' => 'Serologia', 'componente' => 'serologia_vdrl'),
            array('nombre' => 'Eosinofilos en moco nasal', 'CUP' => '902219', 'valor' => '36000.00', 'descripcion' => '', 'nombre_alternativo' => 'Conteo y medición de células eosinofilos en moco nasal, respuesta unflamtoriaalergica', 'componente' => 'eosinofilos_en_moco_nasal'),
            array('nombre' => 'Cuadro Hematico', 'CUP' => '902207', 'valor' => '16000.00', 'descripcion' => 'Conjunto de pruebas de laboratorio médico realizadas a la sangre del paciente con el fin de obtener información sobre el número, composición y proporciones de los elementos figurados de la sangre. ', 'nombre_alternativo' => 'Cuadro hematico - Sin observacion', 'componente' => 'cuadro_hematico'),
            array('nombre' => 'ANTIGENO ESPECÍFICO DE PRÓSTATA (PSA)', 'CUP' => '906610', 'valor' => '26000.00', 'descripcion' => 'Prueba rápida para detectar semicuantitativamente niveles de Antigeno Prostatico Especifico(PSA) en sangre suero o plasma', 'nombre_alternativo' => 'PSA', 'componente' => 'antigeno_especifico_de_prostata')
        );

        foreach ($examens as $examen) {
            Examen::create([
                'nombre' => $examen['nombre'],
                'cup' => $examen['CUP'],
                'valor' => $examen['valor'],

                'plantilla' => json_encode([
                    'descripcion' => $examen['descripcion'] ?? null,
                    'componente' => $examen['componente'] ?? null,
                    'nombre' => $examen['nombre'],
                    'nombre alternativo' => $examen['nombre_alternativo'] ?? null,
                ]),
            ]);
        }
    }
}

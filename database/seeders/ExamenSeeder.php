<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Examen;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // Para generar slugs

use App\Models\Opcion;
use App\Models\Parametro;
use App\Models\ValorReferencia;

class ExamenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $examenesData = array( // Renombrado para evitar confusión con la instancia del modelo
            array(
                'nombre' => 'Hemoclasificación',
                'CUP' => '911017',
                'valor' => '10000.00',
                'descripcion' => 'permite determinar los grupos sanguíneos en el sistema ABO y el factor RH',
                'nombre_alternativo' => 'RH',
                'parametros' => array(
                    array('nombre' => 'Grupo Sanguineo', 'slug' => 'gs', 'tipo_dato' => 'select', 'posicion' => 1, 'opciones' => array('A', 'B', 'O', 'AB')),
                    array('nombre' => 'RH', 'slug' => 'rh', 'tipo_dato' => 'select', 'posicion' => 2, 'opciones' => array('Positivo', 'Negativo'))
                )
            ),
            array(
                'nombre' => 'Prueba de embarazo',
                'CUP' => '904508',
                'valor' => '16000.00',
                'sexo_aplicable' => 'F',
                'descripcion' => 'prueba de sangre cuantitativa mide la cantidad exacta de GCH en la sangre, y una prueba de sangre cualitativa de GCH le da un simple sí o no respuesta a si usted está embarazada o no',
                'nombre_alternativo' => 'Test de embarazo en sangre o en orina',
                'parametros' => array(
                    array('nombre' => 'Resultado', 'slug' => 'resultado', 'tipo_dato' => 'select', 'posicion' => 1, 'opciones' => array('Positivo', 'Negativo')),
                    array('nombre' => 'FUR', 'slug' => 'fur', 'tipo_dato' => 'date', 'posicion' => 2)
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
                'sexo_aplicable' => $examenData['sexo_aplicable'] ?? 'A',
                'descripcion' => $examenData['descripcion'] ?? null,
            ]);

            // Itera sobre los parámetros definidos para cada examen
            foreach ($examenData['parametros'] as $parametroData) {
                // Crea el registro del Parámetro en la tabla 'parametros'
                $parametro = Parametro::create([
                    'nombre' => $parametroData['nombre'],
                    'slug' => $parametroData['slug'],
                    'tipo_dato' => $parametroData['tipo_dato'],
                    'posicion' => $parametroData['posicion'],
                    'examen_id' => $examen->id,
                ]);



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



        // Función auxiliar para procesar parámetros (incluyendo sub-parámetros)
        $processParam = function($paramData, $examenInstance,$ordenPar) {
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
                    'posicion' => $ordenPar,
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



// Aquí puedes agregar más exámenes si es necesario
        $examens = array(
//   array('parametros' => '[{"parametro": "Grupo sanguíneo", "resultado": {"tipo": "select", "nombre": "gs", "items": ["A", "B", "AB", "O"]}}, {"parametro": "RH", "resultado": {"tipo": "select", "nombre": "rh", "items": ["positivo", "negativo"]}}]','nombre_examen' => 'Hemoclasificación','nombre_alternativo' => 'RH','descripcion' => 'permite determinar los grupos sanguíneos en el sistema ABO y el factor RH','CUP' => '911017','valor' => '10000.00'),
//   array('parametros' => '[{"parametro": "resultado", "resultado": {"tipo": "select", "nombre": "resultado", "items": ["positivo", "negativo"]}}, {"parametro": "FUR", "resultado": {"tipo": "date", "nombre": "fur"}}]','nombre_examen' => 'Prueba de embarazo','nombre_alternativo' => 'Test de embarazo en sangre o en orina','descripcion' => 'prueba de sangre cuantitativa mide la cantidad exacta de GCH en la sangre, y una prueba de sangre cualitativa de GCH le da un simple sí o no respuesta a si usted está embarazada o no','CUP' => '904508','valor' => '16000.00'),
//   array('parametros' => '[{"parametro": "hematocrito", "resultado": {"tipo": "number", "nombre": "hto"}, "unidades": "%", "referencia": {"adultos": {"salida": "42-52", "minimo": 42, "maximo": 52}, "menores": {"salida": "35-44", "minimo": 35, "maximo": 44}}}, {"parametro": "hemoglobina", "resultado": {"tipo": "number", "nombre": "hb"}, "unidades": "g%", "referencia": {"adultos": {"salida": "13.5-16.5", "minimo": 13.5, "maximo": 16.5}, "menores": {"salida": "11.0-13.5", "minimo": 11, "maximo": 13.5}}}, {"parametro": "recuento de leucocitos", "resultado": {"tipo": "number", "nombre": "leu"}, "unidades": "leu/mm³", "referencia": {"adultos": {"salida": "5000-10000", "minimo": 5000, "maximo": 10000}, "menores": {"salida": "7000-13000", "minimo": 7000, "maximo": 13000}}}, {"grupo": "recuento diferencial", "parametros": [{"parametro": "neutrofilos", "resultado": {"tipo": "number", "nombre": "neutrofilos"}, "unidades": "%", "referencia": {"adultos": {"salida": "52-67", "minimo": 52, "maximo": 67}, "menores": {"salida": "35-60", "minimo": 35, "maximo": 60}}}, {"parametro": "linfocitos", "resultado": {"tipo": "number", "nombre": "linfocitos"}, "unidades": "%", "referencia": {"adultos": {"salida": "27-42", "minimo": 27, "maximo": 42}, "menores": {"salida": "25-50", "minimo": 25, "maximo": 50}}}, {"parametro": "eosinofilos", "resultado": {"tipo": "number", "nombre": "eosinofilos"}, "unidades": "%", "referencia": {"adultos": {"salida": "0-3", "maximo": 3}}}, {"parametro": "monocitos", "resultado": {"tipo": "number", "nombre": "monocitos"}, "unidades": "%", "referencia": {"adultos": {"salida": "3-7", "minimo": 3, "maximo": 7}}}, {"parametro": "celulas inmaduras", "resultado": {"tipo": "number", "nombre": "inmaduras"}, "unidades": "%"}]}, {"parametro": "recuento de plaquetas", "resultado": {"tipo": "number", "nombre": "rto_plaquetas"}, "unidades": "plaq/mm³", "referencia": {"adultos": {"salida": "150000-450000", "minimo": 150000, "maximo": 450000}}}, {"parametro": "vsg", "resultado": {"tipo": "number", "nombre": "vsg"}, "unidades": "mm/h", "referencia": {"adultos": {"salida": "0-22", "minimo": 0, "maximo": 22}}}, {"parametro": "observaciones", "resultado": {"tipo": "textarea", "nombre": "observacion", "items": ["muestra contaminda, se solicita nueva muestra"]}}]','nombre_examen' => 'Cuadro Hematico','nombre_alternativo' => 'CH','descripcion' => 'Conjunto de pruebas de laboratorio médico realizadas a la sangre del paciente con el fin de obtener información sobre el número, composición y proporciones de los elementos figurados de la sangre. ','CUP' => '902207','valor' => '16000.00'),
//   array('parametros' => '[{"parametro": "Coloración de gram", "resultado": {"tipo": "datalist", "nombre": "gram_a", "items": ["cocos Gram positivos aislados escasos", "negativo para diplococos gram Negativos intra y extra celulares"]}}, {"parametro": "reacción leucocitaria", "resultado": {"tipo": "select", "nombre": "r_leuc", "items": ["escasa", "moderada", "aumentada"]}}]','nombre_examen' => 'Frotis de secreción uretral ','nombre_alternativo' => 'COLORACIÓN DE GRAM','descripcion' => 'análisis de sangre que mide la cantidad de glóbulos blancos en el cuerpo. Los glóbulos blancos, también llamados leucocitos','CUP' => '901107','valor' => '16000.00'),
//   array('parametros' => '[{"grupo": "examen fisico-quimico", "parametros": [{"parametro": "color", "resultado": {"tipo": "select", "nombre": "color", "items": ["hidrico", "amarillo", "ambar", "amarillo intenso", "rojo"]}}, {"parametro": "aspecto", "resultado": {"tipo": "select", "nombre": "aspecto", "items": ["lig turbio", "turbio", "limpido"]}}, {"parametro": "densidad", "resultado": {"tipo": "number", "nombre": "densidad"}, "unidades": "g/dL"}, {"parametro": "pH", "resultado": {"tipo": "number", "nombre": "ph"}}, {"parametro": "glucosa", "resultado": {"tipo": "text", "default": "negativo", "nombre": "glucosa"}, "unidades": "mg/dL"}, {"parametro": "cetonas", "resultado": {"tipo": "text", "default": "negativo", "nombre": "cetonas"}, "unidades": "mg/dL"}, {"parametro": "leucocito esterasa", "resultado": {"tipo": "select", "items": ["positivo", "negativo"], "nombre": "leucocito"}}, {"parametro": "proteinas", "resultado": {"tipo": "text", "default": "negativo", "nombre": "proteinas"}, "unidades": "mg/dL"}, {"parametro": "pigmentos biliares", "resultado": {"tipo": "text", "default": "negativo", "nombre": "pigmentos"}, "unidades": "mg/dL"}, {"parametro": "hemoglobina", "resultado": {"tipo": "text", "default": "negativo", "nombre": "hemoglobina"}}, {"parametro": "nitritos", "resultado": {"tipo": "select", "items": ["positivo", "negativo"], "nombre": "nitritos"}}, {"parametro": "urobilinogeno", "resultado": {"tipo": "text", "default": "normal", "nombre": "urobilinogeno"}, "unidades": "mg/dL"}]}, {"grupo": "examen microscopico", "parametros": [{"parametro": "cel epiteliales", "resultado": {"tipo": "range", "nombre": "epiteliales"}, "unidades": "x campo"}, {"parametro": "leucocitos", "resultado": {"tipo": "range", "nombre": "leucocitos"}, "unidades": "x campo"}, {"parametro": "hematies", "resultado": {"tipo": "range", "nombre": "hematies"}, "unidades": "x campo"}, {"parametro": "bacterias", "resultado": {"tipo": "range", "nombre": "bacterias"}}, {"parametro": "moco", "resultado": {"tipo": "range", "nombre": "moco"}}]}, {"parametro": "observaciones", "resultado": {"tipo": "textarea", "nombre": "observacion", "items": ["muestra contaminda, se solicita nueva muestra"]}}]','nombre_examen' => 'uroanalisis','nombre_alternativo' => 'parcial de orina','descripcion' => '','CUP' => '907106','valor' => '16000.00'),
//   array('parametros' => '[{"parametro": "glicemia", "subtitulo": "Método enzimático", "resultado": {"tipo": "number", "nombre": "glicemia"}, "unidades": "mg/dL", "referencia": {"adultos": {"salida": "70-100", "minimo": 70, "maximo": 100}}}]','nombre_examen' => 'Glicemia','nombre_alternativo' => 'glucosa en la sangre','descripcion' => 'concentración de glucosa libre en la sangre, ​​ suero o plasma sanguíneo','CUP' => '903841','valor' => '16000.00'),
//   array('parametros' => '[{"parametro": "Colesterol total", "subtitulo": "Método enzimático", "resultado": {"tipo": "number", "nombre": "col_total"}, "unidades": "mg/dL", "referencia": {"adultos": {"salida": "Hasta 200", "maximo": 200}}}]','nombre_examen' => 'Colesterol Total','nombre_alternativo' => 'colesterol en la sangre','descripcion' => 'Cantidad total de colesterol en la sangre. Incluye ambos tipos: El colesterol de lipoproteína de baja densidad y el colesterol de lipoproteína de alta densidad ','CUP' => '903818','valor' => '16000.00'),
//   array('parametros' => '[{"parametro": "Triglicéridos", "subtitulo": "Método enzimático", "resultado": {"tipo": "number", "nombre": "trigliceridos"}, "unidades": "mg/dL", "referencia": {"adultos": {"salida": "Hasta 150", "maximo": 150}}}]','nombre_examen' => 'Triglicéridos','nombre_alternativo' => 'lípidos','descripcion' => 'cantidad de triglicéridos(son un tipo de grasa) en la sangre','CUP' => '903868','valor' => '16000.00'),
//   array('parametros' => '[{"parametro": "Colesterol LDL", "subtitulo": "Método enzimático", "resultado": {"tipo": "number", "nombre": "col_ldl"}, "unidades": "mg/dL", "referencia": {"adultos": {"salida": "Hasta 100"}}}]','nombre_examen' => 'Colesterol LDL','nombre_alternativo' => '','descripcion' => 'se le llama colesterol "malo" porque un nivel alto de LDL lleva a una acumulación de colesterol en las arteria','CUP' => '903816','valor' => '16000.00'),
//   array('parametros' => '[{"parametro": "Colesterol HDL", "subtitulo": "Método enzimático", "resultado": {"tipo": "number", "nombre": "col_hdl"}, "unidades": "mg/dL", "referencia": {"adultos": {"salida": "Optimo mayor a 55", "minimo": 35}, "mujeres": {"salida": "Optimo mayor a 65", "minimo": 35}}}]','nombre_examen' => 'Colesterol HDL','nombre_alternativo' => 'lipoproteínas de alta densidad','descripcion' => 'se le llama colesterol "bueno" porque transporta el colesterol de otras partes de su cuerpo a su hígado. Su hígado luego elimina el colesterol de su cuerpo,El HDL desempeña un papel crucial en la prevención de la aterosclerosis, una enfermedad que estrecha y endurece las arterias, aumentando el riesgo de ataques cardíacos y accidentes cerebrovasculares','CUP' => '903815','valor' => '16000.00'),
//   array('parametros' => '[{"parametro": "Ácido úrico", "resultado": {"nombre": "acido_urico", "tipo": "number"}, "unidades": "mg/dL", "referencia": {"adultos": {"salida": "2.5 - 7.0", "minimo": 2.5, "maximo": 7.0}, "mujeres": {"salida": "1.5 - 6.0", "minimo": 1.5, "maximo": 6.0}}}]','nombre_examen' => 'Ácido Úrico','nombre_alternativo' => NULL,'descripcion' => 'mide la cantidad de ácido úrico en una muestra de sangre u orina','CUP' => '903801','valor' => '16000.00'),
  array('parametros' => '[{"parametro": "pH", "resultado": {"tipo": "number", "nombre": "ph"}, "grupo": null}, {"parametro": "Celulas Epiteliales", "resultado": {"tipo": "text", "nombre": "cel_epiteliales"}, "grupo": "Examen Fresco"}, {"parametro": "leucocitos", "resultado": {"tipo": "text", "nombre": "leucocitos"}, "unidades": "xC", "grupo": "Examen Fresco"}, {"parametro": "hematies", "resultado": {"tipo": "text", "nombre": "hematies"}, "unidades": "xC", "grupo": "Examen Fresco"}, {"parametro": "bacterias", "resultado": {"tipo": "text", "default": "negativo", "nombre": "bacterias"}, "grupo": "Examen Fresco"}, {"parametro": "hongos", "resultado": {"tipo": "text", "default": "negativo", "nombre": "hongos"}, "grupo": "Examen Fresco"}, {"parametro": "trichomona vaginalis", "resultado": {"tipo": "text", "default": "negativo", "nombre": "trichomona"}, "grupo": "Examen Fresco"}, {"parametro": "coloración de Gram", "resultado": {"tipo": "textarea", "nombre": "coloracion"}, "grupo": "examen microscópico"}, {"parametro": "reaccion leucocitaria", "resultado": {"tipo": "textarea", "nombre": "r_leu", "items": ["escasa", "moderada", "aumentada", "no presenta reaccción leucocitaria en la muesta anailizada"]}, "grupo": "examen microscópico"}, {"parametro": "informe", "resultado": {"nombre": "informe", "tipo": "textarea"}, "grupo": null}]','nombre_examen' => 'Frotis de flujo vaginal','nombre_alternativo' => 'Coloracion de Gram','descripcion' => '','CUP' => '901107','valor' => '16000.00', 'sexo_aplicable' => 'F'),
  array('parametros' => '[{"parametro": "color", "resultado": {"nombre": "color", "tipo": "datalist", "items": ["amarillo", "cafe"]}, "grupo": "Examen fisico quimico"}, {"parametro": "consistencia", "resultado": {"nombre": "consistencia", "tipo": "datalist", "items": ["diarreica", "blanda", "dura", "pastosa"]}, "grupo": "Examen fisico quimico"}, {"parametro": "glucotest", "resultado": {"nombre": "glucotest", "default": "negativo", "tipo": "text"}, "grupo": "Examen fisico quimico"}, {"parametro": "pH", "resultado": {"tipo": "number", "default": 7.0, "nombre": "ph"}, "grupo": "Examen fisico quimico"}, {"parametro": "moco", "resultado": {"nombre": "moco", "default": "negativo", "tipo": "text"}, "grupo": "Examen fisico quimico"}, {"parametro": "hemoglobina", "resultado": {"nombre": "hemoglobina", "default": "negativo", "tipo": "text"}, "grupo": "Examen fisico quimico"}, {"parametro": "celulosa", "resultado": {"nombre": "celulosa", "default": "-", "tipo": "text"}, "grupo": "Examen microscópico"}, {"parametro": "fibras vegetales", "resultado": {"nombre": "fibras_vegetales", "default": "-", "tipo": "text"}, "grupo": "Examen microscópico"}, {"parametro": "almidones", "resultado": {"nombre": "almidones", "default": "-", "tipo": "text"}, "grupo": "Examen microscópico"}, {"parametro": "grasas neutras", "resultado": {"nombre": "grasas_neutras", "default": "-", "tipo": "text"}, "grupo": "Examen microscópico"}, {"parametro": "ácidos grasos", "resultado": {"nombre": "acidos_grasos", "default": "-", "tipo": "text"}, "grupo": "Examen microscópico"}, {"parametro": "leucocitos", "resultado": {"nombre": "leucocitos", "tipo": "text"}, "unidades": "xC", "grupo": "Examen microscópico"}, {"parametro": "hematies", "resultado": {"nombre": "hematies", "tipo": "text"}, "unidades": "xC", "grupo": "Examen microscópico"}, {"parametro": "levaduras en gemación", "resultado": {"nombre": "lev_gemacion", "tipo": "text"}, "grupo": "Examen microscópico"}, {"parametro": "Flora bacteriana", "resultado": {"nombre": "f_bacteriana", "tipo": "select", "items": ["normal", "ligeramente aumentada", "aumentada", "escasa"]}, "grupo": "Examen microscópico"}, {"parametro": "Informe", "resultado": {"nombre": "parasitologico", "default": "No se observan estructuras parasitarias en la muestra analizada", "tipo": "textarea"}, "grupo": "Examen parasitológico"}, {"parametro": "Coloracion de Gram", "resultado": {"nombre": "col-gram", "tipo": "textarea"}, "grupo": "Examen parasitológico"}]','nombre_examen' => 'Coproanalisis','nombre_alternativo' => 'Coprológico','descripcion' => 'análisis de heces o análisis de materia fecal, es un examen de laboratorio que analiza una muestra de materia fecal para detectar sangre oculta, bacterias, parásitos, inflamación, sobrecrecimiento de bacterias o levaduras (hongos), y presencia o infección con bacterias.','CUP' => '902207','valor' => '16000.00'),
  array('parametros' => '[{"parametro": "Coloracíon de Gram", "resultado": {"tipo": "textarea", "nombre": "f_bacteriana", "default": "Flora bacteriana mixta normal de orofaringe"}}, {"parametro": "reacción leucocitaria", "resultado": {"tipo": "textarea", "nombre": "r_leucocitaria", "defalut": "Reacción leucocitaria Escasa"}}]','nombre_examen' => 'Frotis de garganta','nombre_alternativo' => NULL,'descripcion' => 'prueba de laboratorio que identifica microorganismos que pueden causar una infección en la garganta','CUP' => '901305','valor' => '12000.00'),
  array('parametros' => '[{"parametro": "KoH piel  y uñas", "resultado": {"tipo": "select", "nombre": "koh", "items": ["negativo", "positivo"]}}, {"parametro": "observaciones", "resultado": {"tipo": "textarea", "nombre": "observaciones", "default": "No se observan estructuras micoticas"}}]','nombre_examen' => 'Koh Piel y Uñas','nombre_alternativo' => 'hidróxido de potasio','descripcion' => 'examen cutáneo simple que se utiliza para identificar infecciones fúngicas en las uñas, la piel y el cabello. La prueba consiste en tratar muestras de una zona infectada con KOH, que disuelve las células de la piel y deja las células del hongo visibles bajo un microscopio','CUP' => '901305','valor' => '12000.00'),
  array('parametros' => '[{"parametro": "factor reumatoideo", "resultado": {"nombre": "ratest", "subtitulo": "aglutinación latex", "tipo": "text"}, "unidades": "UI/mL", "referencia": {"adultos": {"salida": "Hasta 8", "minimo": 0, "maximo": 8}}}]','nombre_examen' => 'factor reumatoideo','nombre_alternativo' => 'RA TEST','descripcion' => 'anticuerpo que puede indicar trastornos autoinmunitarios o infecciones','CUP' => '906911','valor' => '16000.00'),
  array('parametros' => '[{"parametro": "PCR", "resultado": {"nombre": "pcr", "subtitulo": "aglutinación latex", "tipo": "text"}, "unidades": "UI/mL", "referencia": {"adultos": {"salida": "Hasta 6", "minimo": 0, "maximo": 6}}}]','nombre_examen' => 'Proteína C reactiva','nombre_alternativo' => 'PCR','descripcion' => 'nivel de esta proteína en la sangre, que puede indicar inflamación en el cuerpo','CUP' => '906914','valor' => '16000.00'),
  array('parametros' => '[{"parametro": "Antiestreptolisina", "subtitulo": "aglutinación latex", "resultado": {"tipo": "text", "nombre": "astos"}, "unidades": "UI/mL", "referencia": {"adultos": {"salida": "Hasta 200", "minimo": 0, "maximo": 200}}}]','nombre_examen' => 'Antiestreptolisina','nombre_alternativo' => 'Astos','descripcion' => 'La antiestreptolisina es un anticuerpo que reconoce sustancias extrañas al organismo que son producidas por la bacteria Streptococcus pyogenes, el cual es responsable de enfermedades como la faringitis y sus complicaciones como la fiebre reumática y glomerulonefritis, siendo la antiestreptolisina O (ASLO) la más utilizada. ','CUP' => '906002','valor' => '16000.00'),
  array('parametros' => '[{"parametro": "glicemia basal", "subtitulo": "Método enzimático", "resultado": {"tipo": "number", "nombre": "glicemia-pre"}, "unidades": "mg/dL", "referencia": {"adultos": {"salida": "70-100", "minimo": 70, "maximo": 100}}}, {"parametro": "glicemia post-prandial", "subtitulo": "Método enzimático", "resultado": {"tipo": "number", "nombre": "glicemia-post"}, "unidades": "mg/dL", "referencia": {"adultos": {"salida": "70-100", "minimo": 70, "maximo": 100}}}, {"parametro": "observaciones", "resultado": {"tipo": "textarea", "nombre": "observacion", "items": ["Se le suministro 75g de glucosa anhidra"]}}]','nombre_examen' => 'Glicemia Post-prandial','nombre_alternativo' => 'glucemia postprandial (GP)','descripcion' => 'Es el nivel de glucosa en la sangre después de comer. Se mide 1,5 o 2 horas después de comer y no debe superar los 160 mg/dl. Los valores vuelven a la normalidad pasadas 3 horas de la ingesta.','CUP' => '903843','valor' => '32000.00'),
  array('parametros' => '[{"parametro": "Coloración de gram", "resultado": {"tipo": "datalist", "nombre": "gram_a", "items": ["cocos Gram positivos aislados escasos", "negativo para diplococos gram Negativos intra y extra celulares"]}}, {"parametro": "reacción leucocitaria", "resultado": {"tipo": "select", "nombre": "r_leuc", "items": ["escasa", "moderada", "aumentada"]}}]','nombre_examen' => 'Frotis de secreción','nombre_alternativo' => 'COLORACIÓN DE GRAM','descripcion' => 'usado para ayudar a identificar bacterias. Se puede tomar una muestra para evaluación de los fluidos corporales que normalmente no contienen bacterias, tales como sangre, orina o líquido cefalorraquídeo','CUP' => '901107','valor' => '16000.00'),
  array('parametros' => '[{"parametro": "Serología", "resultado": {"tipo": "text", "nombre": "resultado", "default": "NO REACTIVA"}}]','nombre_examen' => 'Serologia (Prueba no treponemica) VDRL','nombre_alternativo' => 'Serologia','descripcion' => 'comprueban la presencia o el nivel de anticuerpos específicos en la sangre','CUP' => '906915','valor' => '16000.00'),
  array('parametros' => '[{"parametro": "Fosa nasal derecha", "resultado": {"nombre": "poli_de", "tipo": "number"}, "unidades": "%", "grupo": "Polimorfonucleares"}, {"parametro": "Fosa nasal izquierda", "resultado": {"nombre": "poli_iz", "tipo": "number"}, "unidades": "%", "grupo": "Polimorfonucleares"}, {"parametro": "Fosa nasal derecha", "resultado": {"nombre": "mono_de", "tipo": "number"}, "unidades": "%", "grupo": "Mononucleares"}, {"parametro": "Fosa nasal izquierda", "resultado": {"nombre": "mono_iz", "tipo": "number"}, "unidades": "%", "grupo": "Mononucleares"}, {"parametro": "Fosa nasal derecha", "resultado": {"nombre": "eos_de", "tipo": "number"}, "unidades": "%", "grupo": "Eosinofilos"}, {"parametro": "Fosa nasal izquierda", "resultado": {"nombre": "eos_iz", "tipo": "number"}, "unidades": "%", "grupo": "Eosinofilos"}, {"parametro": "observaciones", "resultado": {"nombre": "observaciones", "tipo": "textarea"}, "grupo": null}]','nombre_examen' => 'Eosinofilos en moco nasal','nombre_alternativo' => 'Conteo y medición de células eosinofilos en moco nasal, respuesta unflamtoriaalergica','descripcion' => '','CUP' => '902219','valor' => '36000.00'),
  array('parametros' => '[{"parametro": "Antigeno específico de próstata", "subtitulo": "Prueba rápida semicuantitativa para (PSA) \\n Negativo menor a 3 ng/ml", "resultado": {"tipo": "select", "nombre": "psa", "items": ["positivo", "negativo"]}}, {"parametro": "observaciones", "resultado": {"tipo": "textarea", "nombre": "observacion", "items": ["Se sugiere realizar prueba cuantitativa"]}}]','nombre_examen' => 'Antigeno específico de próstata','nombre_alternativo' => 'PSA','descripcion' => 'Prueba rápida para detectar semicuantitativamente niveles de Antigeno Prostatico Especifico(PSA) en sangre suero o plasma','CUP' => '906610','valor' => '26000.00','sexo_aplicable' => 'M')
);

// Función para reestructurar los parámetros
foreach ($examens as &$examen) {
    // Decodificar la cadena JSON de parámetros
    $parametros_json = $examen['parametros'];
    $parametros = json_decode($parametros_json, true);

    $new_parametros = [];
    $nuevoExamen = Examen::firstOrCreate(
        ['nombre' => $examen['nombre_examen']],
        [
            'nombre' => $examen['nombre_examen'],
            'nombre_alternativo' => $examen['nombre_alternativo'] ??'A',
            'descripcion' => $examen['descripcion'],
            'sexo_aplicable' => $examen['sexo_aplicable'] ?? 'A',
            'cup' => $examen['CUP'],
            'valor' => $examen['valor']
        ]
    );
    $orden = 0;
    foreach ($parametros as $param) {
        // Si el parámetro es un grupo con sub-parámetros
        if (isset($param['grupo']) && isset($param['parametros']) && is_array($param['parametros'])) {
            $group_name = $param['grupo'];
            foreach ($param['parametros'] as $sub_param) {
                // Agregar el nombre del grupo al sub-parámetro
                $sub_param['grupo'] = $group_name;
                $new_parametros[] = $sub_param;
            }
        } else {
            // Si no es un grupo, añadirlo directamente y asegurarse de que 'grupo' sea null si no existe
            if (!isset($param['grupo'])) {
                $param['grupo'] = null;
            }
            $new_parametros[] = $param;
        }

            $processParam($param,$nuevoExamen,$orden);
            $orden++;


    }


        }




    }
}

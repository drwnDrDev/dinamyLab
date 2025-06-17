<?php

namespace App\Services;

use App\Support\StringHelper;
use Illuminate\Support\Facades\Log;
use App\Models\Persona;
use App\Models\Parametro;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Models\ValorReferencia;
use Carbon\Carbon;


class EscogerReferencia
{
    /**
     * Filtra los valores de referencia para eliminar aquellos que son nulos o vacíos.
     *
     * @param array $datos Datos a filtrar.
     * @return array Datos filtrados.
     */
    public static function datosDemograficos(Persona $paciente, Carbon $fecha): array
    {
        $sexo = $paciente->sexo === 'M' ? 'hombres' : 'mujeres';

        $edad = $paciente->fecha_nacimiento ? $paciente->fecha_nacimiento->diffInYears($fecha) : null;
        if ($edad === null || $edad < 0) {
            Log::warning('No se puede calcular la edad: fecha de nacimiento no definida', [
                'paciente_id' => $paciente->id
            ]);
            return [];
        }
        $etario = $edad < 5 ? 'menores' : 'adultos';

        return [
            'sexo' => $sexo,
            'etario' => $etario,
        ];
    }


    public static function estableceReferencia(array $datosDemograficos,Parametro $parametro): ?ValorReferencia
    {
        if(!$parametro->valoresReferencia || empty($parametro->valoresReferencia)){
            return null;
        }
        if(count($parametro->valoresReferencia) === 1){
             return $parametro->valoresReferencia->first();
        }

        $sexo = $datosDemograficos['sexo'];
        $etario = $datosDemograficos['etario'];
        // Si $parametro->valoresReferencia es una colección, usamos los métodos de colección de Laravel
        $referencias = $parametro->valoresReferencia->where('demografia', $etario);

        if ($referencias->isNotEmpty()) {
            return $referencias->first();
        }
        $referencias = $parametro->valoresReferencia->where('demografia', $sexo);

        if ($referencias->isNotEmpty()) {
            return $referencias->first();
        }

        return $parametro->valoresReferencia->first();;


    }
    public static function recorrerParamtrosExamen(Procedimiento $procedimiento): array
    {

        $datosDemograficos = self::datosDemograficos($procedimiento->orden->paciente,Carbon::parse($procedimiento->fecha) );
        $parametrosE = $procedimiento->examen
                                ->parametros()
                                ->withPivot('orden')
                                ->orderBy('pivot_orden')
                                ->get();


        if(empty($datosDemograficos)){
            return [];
        }
        $parametros = [];
        foreach ($parametrosE as $parametro) {
            $referencia = self::estableceReferencia($datosDemograficos, $parametro);

            $parametros[] = [
                'id'=>$parametro->id,
                'nombre' => $parametro->nombre,
                'grupo' => $parametro->grupo,
                'tipo_dato' => $parametro->tipo_dato,
                'default'=>$parametro->default,
                'metodo' => $parametro->metodo,
                'unidades' => $parametro->unidades,
                'referencia' => $referencia?$referencia['salida']:null,
                'opciones' => $parametro->opciones ? $parametro->opciones->map(function($opcion) {
                    return $opcion->valor;
                })->all() : [],
            ];
        }
        return $parametros;
    }

    public static function guardaResuldado(array $formData, Procedimiento $pro ){

        $dDemograficos = self::datosDemograficos($pro->orden->paciente,Carbon::parse($pro->fecha) );

        foreach ( $formData as $parametro => $resultado ){
            $parametro = Parametro::find($parametro);
            $ref = self::estableceReferencia($dDemograficos,$parametro);
            $isNormal = true;
            if($ref !== null){
                $isNormal = $resultado<$ref['max'] && $resultado>$ref['min'];
            }
            Resultado::create([
                'parametro_id'=>$parametro->id,
                'resultado'=>$resultado,
                'procedimiento_id'=>$pro->id,
                'is_normal'=>$isNormal
            ]);

        }


    }

}


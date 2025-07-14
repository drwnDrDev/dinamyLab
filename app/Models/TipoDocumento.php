<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{

    protected $fillable = [
        'nombre',
        'cod_rips',
        'cod_dian',
        'es_nacional',
        'edad_minima',
        'edad_maxima',
        'unidad_edad',
        'regex_validacion',
        'requiere_acudiente',
    ];

    /**
     * Obtiene el ID del tipo de documento por su código RIPS.
     *
     * @param string $codigo
     * @return int
     */

public static function idPorCodigoRips(string $codigo): int
        {
            return static::where('cod_rips', $codigo)->value('id');
        }
    

}

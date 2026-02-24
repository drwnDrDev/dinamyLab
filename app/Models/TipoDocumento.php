<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    protected $fillable = [
        'nombre',
        'cod_rips',
        'cod_dian',
        'es_nacional',
        'edad_minima',
        'edad_maxima',
        'es_paciente',
        'es_pagador',
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

        public static function idPorCodigoDian(string $codigo): int
        {
            return static::where('cod_dian', $codigo)->value('id');
        }

    /**
     * Obtiene el regex de validación por el código RIPS.
     * @param string $codigo
     * @return string
     * @throws \InvalidArgumentException
     * */


    public static function regexPorCodigoRips(string $codigo): string
        {
            $regex = static::where('cod_rips', $codigo)->value('regex_validacion');
            if ($regex === null) {
                throw new \InvalidArgumentException("El código RIPS '{$codigo}' no es válido.");
            }

            $regex = 'regex:/' . $regex . '/';
            return $regex;
        }





}

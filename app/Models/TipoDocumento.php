<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoDocumento extends Model
{

public static function idPorCodigoRips(string $codigo): int
        {
            return static::where('cod_rips', $codigo)->value('id');
        }

}

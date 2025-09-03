<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $fillable = [
        'departamento',
        'codigo_departamento',
        'municipio',
        'codigo_municipio',
        'nivel'
    ];

    static public function incrementarNivel($codigo_municipio)
    {
        try {
            $municipio = self::find($codigo_municipio);
            if ($municipio && $municipio->nivel < 65535) {
                $municipio->nivel++;
                $municipio->save();
            }
        } catch (\Exception $e) {
            // Manejar la excepción, por ejemplo, registrar el error
            \Log::error('Error incrementando nivel del municipio: ' . $e->getMessage());
        }
    }

}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{

    protected $fillable = [
        'nombre',
        'codigo_iso',
        'nivel',
    ];

    static public function incremetarNivelPais(string $codigo_iso): void
    {
        $pais = self::find($codigo_iso);
        if ($pais) {
            $pais->nivel = $pais->nivel + 1;
            if($pais->nivel<255) {
            $pais->save();
            } else {
                Log::warning('Nivel máximo alcanzado para el país: ' . $pais->nombre);
            }

        }
    }

    protected $table = 'paises';
    protected $primaryKey = 'codigo_iso';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

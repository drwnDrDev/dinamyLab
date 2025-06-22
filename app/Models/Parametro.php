<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{


    protected $fillable = [
        'nombre',
        'grupo',
        'tipo_dato',
        'slug',
        'por_defecto',
        'unidades',
        'metodo',

    ];

    public function examenes()
    {
        return $this->belongsToMany(Examen::class, 'examen_parametro','examen_id', 'parametro_id')
            ->withPivot('posicion')
            ->withTimestamps();
    }

        /**
     * Get the valor_referencias for the Parametro.
     */
    public function valoresReferencia()
    {
        return $this->hasMany(ValorReferencia::class);
    }

        public function opciones()
    {
        return $this->hasMany(Opcion::class);
    }
}

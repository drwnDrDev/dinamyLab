<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{


    protected $fillable = [
        'nombre',
        'grupo',
        'posicion',
        'tipo_dato',
        'slug',
        'por_defecto',
        'unidades',
        'metodo',
        'examen_id'
    ];

    public function examen()
    {
        return $this->belongsTo(Examen::class,'examenes');

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

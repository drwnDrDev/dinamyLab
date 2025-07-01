<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValorReferencia extends Model
{

    protected $fillable = [
        'parametro_id',
        'demografia',
        'salida',
        'min',
        'max',
        'optimo'
    ];

     /**
     * Get the parametro that owns the Opcion.
     */
    public function parametro()
    {
        return $this->belongsTo(Parametro::class);
    }

}

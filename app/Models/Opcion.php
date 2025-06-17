<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opcion extends Model
{
       protected $fillable = [
        'parametro_id',
        'valor'
    ];

        /**
     * Get the parametro that owns the Opcion.
     */
    public function parametro()
    {
        return $this->belongsTo(Parametro::class);
    }
}

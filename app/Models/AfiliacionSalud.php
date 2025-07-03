<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AfiliacionSalud extends Model
{

    protected $fillable = [
        'tipo_afiliacion',
        'eps',
        'persona_id'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    protected $table = 'afiliaciones_salud';

}

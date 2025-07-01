<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AfiliacionSalud extends Model
{

    protected $fillable = [
        'tipo_afiliacion',
        'numero_afiliacion',
        'fecha_inicio',
        'fecha_fin',
        'entidad_salud_id',
        'empleado_id'
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    protected $table = 'afiliaciones_salud';

}

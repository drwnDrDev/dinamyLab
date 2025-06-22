<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{

    protected $fillable = [
        'nombre',
        'cup',
        'valor',
        'desripcion',
        'nombre_alternativo'

    ];


    public function ordenes()
    {
        return $this->belongsToMany(Orden::class, 'orden_examen','examen_id', 'orden_medica_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }


    public function parametros()
    {
        return $this->hasMany(Parametro::class);
    }


    protected $table = 'examenes';

}

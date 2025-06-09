<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Examen extends Model
{

    protected $fillable = [
        'nombre',
        'cup',
        'valor',
        'plantilla'
    ];

    public function slug(){
        return json_decode($this->plantilla, true)['componente'] ?? null;
    }
   
    public function ordenes()
    {
        return $this->belongsToMany(Orden::class, 'orden_examen','examen_id', 'orden_medica_id')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    protected $table = 'examenes';

}

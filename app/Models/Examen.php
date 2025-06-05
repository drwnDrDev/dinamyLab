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

    protected $table = 'examenes';

}

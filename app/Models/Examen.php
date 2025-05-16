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

    
    protected $table = 'examenes';

}

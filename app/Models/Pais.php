<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pais extends Model
{

    protected $fillable = [
        'nombre',
        'codigo_iso',
        
        'nivel',
    ];

    protected $table = 'paises';
}

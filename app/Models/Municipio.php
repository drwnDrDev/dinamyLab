<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Municipio extends Model
{
    protected $fillable = [
        'departamento',
        'codigo_departamento',
        'municipio',
        'codigo_municipio',
        'nivel'
    ];

}

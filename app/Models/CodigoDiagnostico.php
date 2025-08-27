<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoDiagnostico extends Model
{
    protected $table = 'codigo_diagnosticos';
    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'grupo',
        'sub_grupo',
        'nivel',
        'activo'
    ];
}

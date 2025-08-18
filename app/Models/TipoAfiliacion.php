<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoAfiliacion extends Model
{
    protected $fillable = [
        'codigo',
        'descripcion',
        'activo',
    ];

    protected $table = 'tipos_afiliaciones';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

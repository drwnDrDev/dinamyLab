<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServicioHabilitado extends Model
{
    protected $table = 'servicios_habilitados';
    public $timestamps = false;
    protected $fillable = [
        'codigo',
        'nombre',
        'grupo',
        'codigo_grupo',
        'activo',
        'nivel',
    ];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'integer';


    
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ViaIngreso extends Model
{
    protected $table = 'vias_ingreso';
    protected $primaryKey = 'codigo';
    public $incrementing = false; // Porque la clave primaria no es un entero autoincremental
    protected $keyType = 'string'; // Porque la clave primaria es una cadena
    public $timestamps = false; // Si no tienes columnas created_at y updated_at

    protected $fillable = [
        'codigo',
        'nombre',
        'activo',
        'nivel'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\IncrementaNivel;

class MetodoPago extends Model
{
    use IncrementaNivel;
    protected $fillable = [
        'codigo',
        'nombre'
    ];

    protected $table = 'metodos_pagos';


    protected static function booted()
    {
        self::incrementarNivel('codigo', 6);
        self::resetearNiveles(4);
    }
}

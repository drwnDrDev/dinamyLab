<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\IncrementaNivel;

class Finalidad extends Model
{
    use IncrementaNivel;
    protected $fillable = [
        'codigo',
        'nombre'
    ];

    protected $table = 'finalidades';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected static function booted()
    {
        self::incrementarNivel('codigo', 16);
        self::resetearNiveles(10);
    }
}

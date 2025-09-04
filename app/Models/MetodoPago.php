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

    protected $table = 'metodos_pago';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected static function booted()
    {
        self::incrementarNivel('codigo', 6);
        self::resetearNiveles(4);
    }
}

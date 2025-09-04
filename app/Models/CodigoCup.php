<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\IncrementaNivel;

use Illuminate\Database\Eloquent\SoftDeletes;

class CodigoCup extends Model
{
    use IncrementaNivel;
    use SoftDeletes;

    protected $table = 'codigo_cups';
    protected $fillable = ['codigo', 'nombre', 'descripcion', 'estado'];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    protected static function booted()
    {
        self::incrementarNivel('codigo', 30);
        self::resetearNiveles();
    }



}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\IncrementaNivel;

class ServicioHabilitado extends Model
{
    use IncrementaNivel;
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

    protected static function booted()
    {
        self::incrementarNivel('codigo', 100);
        self::resetearNiveles(8);
    }

}

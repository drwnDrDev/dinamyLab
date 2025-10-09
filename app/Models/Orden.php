<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Orden extends Model
{
    use SoftDeletes;
   // ...
    protected $fillable = [
        'sede_id',
        'numero',
        'paciente_id', // Asegúrate de incluirlo aquí
        'observaciones',
        'terminada',
        'abono',
        'user_id',
        'total',
    ];

    public function casts()
    {
        return [
            'numero' => 'integer',
            'abono' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }
    public function paciente()
    {
        return $this->belongsTo(Persona::class, 'paciente_id');
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
    public function procedimientos()
    {
        return $this->hasMany(Procedimiento::class);
    }
    public function examenes()
    {
        return $this->belongsToMany(Examen::class, 'orden_examen', 'orden_medica_id', 'examen_id')
            ->withPivot('cantidad');

    }
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    protected $table = 'ordenes_medicas';
}

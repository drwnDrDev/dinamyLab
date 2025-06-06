<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{


    protected $fillable = [
        'numero',
        'paciente_id',
        'acompaniante_id',
        'descripcion',
        'abono',
        'estado',
        'total',
    ];
    protected $casts = [
        'numero' => 'integer',
        'abono' => 'decimal:2',
    ];
    public function paciente()
    {
        return $this->belongsTo(Persona::class, 'paciente_id');
    }
    public function acompaniante()
    {
        return $this->belongsTo(Persona::class, 'acompaniante_id');
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
        return $this->belongsToMany(Examen::class, 'orden_examen')
            ->withPivot('cantidad')
            ->withTimestamps();
    }

    protected $table = 'ordenes_medicas';
}

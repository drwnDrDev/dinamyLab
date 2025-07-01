<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'facturas';



    protected $fillable = [
        'tipo',
        'numero',
        'cufe',
        'fecha_emision',
        'fecha_vencimiento',
        'subtotal',
        'total_ajustes',
        'sede_id',
        'convenio_id',
        'empleado_id',
        'resolucion_id',
        'qr',
        'estado',
        'tipo_pago',
        'total',
        'observaciones'
    ];

    protected function casts(): array
    {
        return [
            'fecha_emision' => 'datetime',
            'fecha_vencimiento' => 'date',
     
            'total' => 'decimal:2',
            'subtotal' => 'decimal:2',
            'total_ajustes' => 'decimal:2'
        ];
    }

}

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
        'numero',
        'sede_id',
        'convenio_id',
        'empleado_id',
        'resolucion_id',
        'fecha_emision',
        'fecha_vencimiento',
        'total',
        'subtotal',
        'total_ajustes',
        'observaciones',
        'tipo',
        'cufe',
        'pagador_type',
        'pagador_id',
        'qr',
        'estado',
        'tipo_pago'
    ];

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
    public function convenio()
    {
        return $this->belongsTo(Convenio::class);
    }
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
    public function resolucion()
    {
        return $this->belongsTo(Resolucion::class);
    }
    public function mediosPago()
    {
        return $this->hasMany(FacturaMedioPago::class);
    }
    public function pagador()
    {
        return $this->morphTo();
    }
    


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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    


    protected $fillable = [
        'numero',
        'fecha',
        'pagador_type',
        'pagador_id',
        'subtotal',
        'total_ajustes',
        'impuestos',
        'total',
        'observaciones'
    ];
    protected $casts = [
        'impuestos' => 'array',
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'total_ajustes' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    public function pagador()
    {
        return $this->morphTo();
    }
}

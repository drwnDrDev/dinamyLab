<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{

    protected $fillable = [
        'impuesto',
        'factura_id',
        'sede_id',
        'descripcion',
        'codigo',
        'tasa',
        'base',
        'monto'
    ];

    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    protected function casts(): array
    {
        return [
            'tasa' => 'decimal:2',
            'base' => 'decimal:2',
            'monto' => 'decimal:2',
        ];
    }
}

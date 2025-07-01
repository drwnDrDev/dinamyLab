<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
   protected $fillable = [
        'razon_social',
        'nit',
        'contacto_id',
    ];

    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }
    // Relación isomórfica para la columna 'pagador' en la tabla 'factura'
    public function facturasComoPagador()
    {
        return $this->morphMany(Factura::class, 'pagador');
    }
}

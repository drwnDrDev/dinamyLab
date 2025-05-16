<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tributo extends Model
{

    protected $guarded = [];
        // No uses $fillable, y controla el acceso a los datos solo desde el sistema (por ejemplo, usando métodos específicos en el modelo o servicios).
    public function impuesto()
    {
        return $this->belongsTo(Impuesto::class);
    }
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
    public function getValorAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }
    public function getBaseAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }
    public function getPorcentajeAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }
}

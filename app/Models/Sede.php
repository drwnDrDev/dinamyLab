<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'res_facturacion',
        'incio_facturacion',
        'fin_facturacion',
        'empresa_id',
        'contacto_id'
    ];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

}

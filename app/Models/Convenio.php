<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Convenio extends Model
{
   protected $fillable = [
    'tipo_documento_id',
    'numero_documento',
    'razon_social',
    'descuento'
    ];
    /**
     * Relación con el modelo Contacto.
     */

    public function telefonos(){
        return $this->morphMany(Telefono::class, 'telefonoable');
    }
    public function emails(){
        return $this->morphMany(CorreoElectronico::class, 'emailable');
    }
    public function direccion(){
        return $this->morphOne(Direccion::class, 'direccionable');
    }
    public function redeSociales(){
        return $this->morphMany(RedSocial::class, 'redable');
    }
    public function facturas()
    {
        return $this->hasMany(Factura::class, 'convenio_id');
    }

}

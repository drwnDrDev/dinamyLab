<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
        /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\PersonaFactory> */
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'razon_social',
        'nit',
        'contacto_id',
    ];

    /**
     * Relación con el modelo Contacto.
     */
    public function telefonos()
    {
        return $this->morphMany(Telefono::class, 'telefonoable');
    }
    public function emails()
    {
        return $this->morphMany(CorreoElectronico::class, 'emailable');
    }
    public function direccion()
    {
        return $this->morphOne(Direccion::class, 'direccionable');
    }
    public function redesSociales()
    {
        return $this->morphMany(RedSocial::class, 'redable');
    }
    public function sedes()
    {
        return $this->hasMany(Sede::class);
    }
    public function empleados()
    {
        return $this->hasMany(Empleado::class);
    }


}

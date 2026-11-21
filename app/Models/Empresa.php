<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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



    /**
     * Razón social en dos líneas para encabezados impresos:
     * "LABORATORIO CLINICO CLAUDIA ..." => ['Laboratorio clínico', 'Dra. Claudia ...'].
     * El título (Dra., Dr.) sale de config('instancias.cliente.titulo_profesional').
     * Si la razón social no empieza por "Laboratorio clínico" se devuelve [null, razón social].
     */
    public function nombreEnLineas(): array
    {
        $razonSocial = (string) $this->razon_social;
        $prefijo = 'LABORATORIO CLINICO ';

        if (! Str::startsWith(Str::upper(Str::ascii($razonSocial)), $prefijo)) {
            return [null, $razonSocial];
        }

        $titular = Str::title(Str::lower(trim(mb_substr($razonSocial, mb_strlen($prefijo)))));
        $titulo = trim((string) config('instancias.cliente.titulo_profesional'));

        return ['Laboratorio clínico', trim($titulo.' '.$titular)];
    }
}

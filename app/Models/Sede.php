<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'codigo_prestador',
        'logo',
        'empresa_id',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    public function empleados()
    {
        return $this->belongsToMany(Empleado::class, 'empleado_sede');
    }
    public function resoluciones()
    {
        return $this->morphMany(Resolucion::class, 'uso');
    }
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
    public function telefonos(){
        if($this->morphMany(Telefono::class, 'telefonoable')->exists()){
            return $this->morphMany(Telefono::class, 'telefonoable');
        }
        return $this->empresa->telefonos();

    }
    public function emails(){
        if($this->morphMany(CorreoElectronico::class, 'emailable')->exists()){
            return $this->morphMany(CorreoElectronico::class, 'emailable');
        }
        return $this->empresa->emails();

    }
    public function direccion(){
        if($this->morphOne(Direccion::class, 'direccionable')->exists()){
            return $this->morphOne(Direccion::class, 'direccionable');
        }
        return $this->empresa->direccion();

    }


    protected static function booted()
    {
        static::deleting(function ($sede) {
            if ($sede->contacto) {
                $sede->contacto->delete();
            }
        });
    }
}

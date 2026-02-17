<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model

{
        /** @use \Illuminate\Database\Eloquent\Factories\HasFactory<\Database\Factories\PersonaFactory> */
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'cargo',
        'firma',
        'tipo_documento_id',
        'numero_documento',
        'fecha_nacimiento',
        'user_id',
        'empresa_id'
    ];



    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function nombre(){
        $this->user->name;
    }

    public function sedes()
    {
        return $this->belongsToMany(Sede::class, 'empleado_sede');
    }
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
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
    public function afiliacionesSalud()
    {
        return $this->hasMany(AfiliacionSalud::class);
    }

    public function is_admin()
    {
        return $this->user->hasRole('admin');
    }
}

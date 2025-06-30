<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model

{
    protected $fillable = [
        'cargo',
        'firma',
        'tipo_documento',
        'numero_documento',
        'fecha_ingreso',
        'fecha_retiro',
        'fecha_nacimiento',
        'user_id',
        'empresa_id',
        'persona_id',
        'contacto_id',
    ];

    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
}

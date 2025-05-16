<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model

{
    protected $fillable = [
        'codigo',
        'cargo',
        'persona_id',
        'user_id',
        'sede_id',
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

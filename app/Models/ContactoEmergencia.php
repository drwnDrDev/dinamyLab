<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactoEmergencia extends Model
{
    protected $fillable = [
        'paciente_id',
        'acompanante_id',
        'parentesco'
    ];

    public function paciente()
    {
        return $this->belongsTo(Persona::class, 'paciente_id');
    }
    public function acompanante()
    {
        return $this->belongsTo(Persona::class, 'acompanante_id');
    }
    

    protected $table = 'contacto_emergencias';
}

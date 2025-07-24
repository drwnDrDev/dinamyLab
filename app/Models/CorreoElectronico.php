<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorreoElectronico extends Model
{

    protected $fillable = [
        'email',
        'emailable_id',
        'emailable_type'
    ];

    /**
     * Relación polimórfica con el modelo que posee el correo electrónico.
     */
    public function emailable()
    {
        return $this->morphMany();
    }

    protected $table = 'correos_electronicos';
}

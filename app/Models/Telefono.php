<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Telefono extends Model
{
    protected $fillable = [
        'numero',
        'telefonoable_id',
        'telefonoable_type'
    ];

    /**
     * Relación polimórfica con el modelo que posee el teléfono.
     */
    public function telefonoable()
    {
        return $this->morphTo();
    }

    protected $table = 'telefonos';
}

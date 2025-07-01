<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $fillable = [
        'direccion',
        'municipio_id',
        'direccionable_id',
        'direccionable_type'
    ];

    /**
     * Relación polimórfica con el modelo que posee la dirección.
     */
    public function direccionable()
    {
        return $this->morphTo();
    }

    protected $table = 'direcciones';
}

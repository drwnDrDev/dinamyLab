<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $fillable = [
        'direccion',
        'municipio_id',
        'pais_id',
        'codigo_postal',
        'zona',
        'direccionable_id',
        'direccionable_type'
    ];

    public function municipio()
    {
        return $this->belongsTo(Municipio::class, 'municipio_id');
    }
    public function pais_residencia()
    {
        return $this->belongsTo(Pais::class, 'pais_id', 'codigo_iso');
    }

    /**
     * Relación polimórfica con el modelo que posee la dirección.
     */
    public function direccionable()
    {
        return $this->morphTo();
    }

    protected $table = 'direcciones';
}

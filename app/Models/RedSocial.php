<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedSocial extends Model
{
    protected $fillable = [

        'redable_type',
        'redable_id',
        'nombre',
        'perfil',
        'url'
    ];
    /**
     * Relación polimórfica con el modelo que posee la red social.
     */
    public function redable()
    {
        return $this->morphTo();
    }
    protected $table = 'redes_sociales';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'descripcion',
        'categoria',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'servicios';
}

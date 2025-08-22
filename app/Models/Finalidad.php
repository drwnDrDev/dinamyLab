<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Finalidad extends Model
{
    protected $fillable = [
        'codigo',
        'nombre'
    ];

    protected $table = 'finalidades';
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
}

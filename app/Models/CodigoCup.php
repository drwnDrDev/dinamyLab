<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodigoCup extends Model
{
    protected $table = 'codigo_cups';
    protected $fillable = ['codigo', 'nombre', 'descripcion', 'estado'];
    protected $primaryKey = 'codigo';
    public $incrementing = false;
    protected $keyType = 'string';

    


}

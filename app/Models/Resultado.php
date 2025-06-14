<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resultado extends Model
{

        protected $fillable = [
        'procedimiento_id',
        'parametro_id',
        'es_normal',
        'resultado'
    ];




}

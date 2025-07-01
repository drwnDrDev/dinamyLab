<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Resultado extends Model
{

    protected $fillable = [
        'resultado',
        'posicion',
        'parametro_id',
        'procedimiento_id'
    ];

    public function parametro(){
        return $this->belongsTo(Parametro::class);
    }


}

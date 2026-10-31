<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultadoCambio extends Model
{
    protected $fillable = [
        'procedimiento_id',
        'resultado_id',
        'parametro_id',
        'usuario_id',
        'parametro_nombre',
        'valor_anterior',
        'valor_nuevo',
        'observacion',
        'usuario_nombre',
    ];

    public function procedimiento()
    {
        return $this->belongsTo(Procedimiento::class);
    }
}

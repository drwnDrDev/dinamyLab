<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Procedimiento extends Model
{

    protected $fillable = [
        'orden_id',
        'empleado_id',
        'resultados',
        'observacion',
        'factura_id',
        'fecha'
    ];


    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }
    public function isPrestador()
    {
        return $this->empleado->cargo == 'prestador';
    }
    public function examen()
    {
        return $this->belongsTo(Examen::class);
    }
    protected $casts = [
       
        'fecha' => 'date',
    ];
}

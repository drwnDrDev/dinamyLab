<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;

class Procedimiento extends Model
{


    protected $fillable = [
        'orden_id',
        'empleado_id',
        'examen_id',
        'factura_id',
        'fecha',
        'sede_id',
        'contacto_emergencia_id',
        'estado'
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
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
    public function contactoEmergencia()
    {
        return $this->belongsTo(ContactoEmergencia::class);
    }

    public function resultado(){
        return $this->hasMany(Resultado::class);
    }
    public function factura()
    {
        return $this->belongsTo(Factura::class);
    }
    public function scopePendientes($query)
    {
        return $query->whereNull('resultados');
    }



}

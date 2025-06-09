<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\AsCollection;
use Illuminate\Database\Eloquent\Casts\AsDate;

class Procedimiento extends Model
{

    protected $fillable = [
        'orden_id',
        'examen_id',
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

    public function resultado(){
        return json_decode($this->resultados);
    }
    protected function casts(): array
    {
        return [
        'resultados' => AsCollection::class
        ];
    }
}

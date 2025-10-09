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
        'diagnostico_principal',
        'codigo_servicio',
        'diagnostico_relacionado',
        'codigo_finalidad',
        'codigo_modalidad',
        'codigo_via_ingreso',
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
    public function finalidad()
    {
        return $this->belongsTo(Finalidad::class, 'codigo_finalidad', 'codigo');
    }
    public function modalidad()
    {
        return $this->belongsTo(Modalidad::class, 'codigo_modalidad', 'codigo');
    }
    public function viaIngreso()
    {
        return $this->belongsTo(ViaIngreso::class, 'codigo_via_ingreso', 'codigo');
    }
    public function diagnosticoPrincipal()
    {
        return $this->belongsTo(CodigoDiagnostico::class, 'diagnostico_principal', 'codigo');
    }
    public function diagnosticoRelacionado()
    {
        return $this->belongsTo(CodigoDiagnostico::class, 'diagnostico_relacionado', 'codigo');
    }
    public function servicioHabilitado()
    {
        return $this->belongsTo(ServicioHabilitado::class, 'codigo_servicio', 'codigo');
    }

    public function scopePendientes($query)
    {
        return $query->whereNull('resultados');
    }




}

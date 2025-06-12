<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\NombreParser;

class Persona extends Model
{


    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'sexo',
        'nacional',
        'telefono',
        'contacto_id'
    ];
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'nacional' => 'boolean',
        'sexo' => 'string',
    ];
    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'paciente_id');
    }
    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Orden::class, 'paciente_id');
    }



    public function nombreCompleto(): string
    {
        return NombreParser::formatear([
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
        ])['nombre_completo'];
    }

    public function nombres() : string
    {
        return NombreParser::formatear([
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
        ])['nombres'];
    }

    public function getInicialesAttribute(): string
    {
        return NombreParser::formatear([
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
        ])['iniciales'];
    }

    public function apellidos(): string
    {
        return NombreParser::formatear([
            'primer_nombre' => $this->primer_nombre,
            'segundo_nombre' => $this->segundo_nombre,
            'primer_apellido' => $this->primer_apellido,
            'segundo_apellido' => $this->segundo_apellido,
        ])['apellidos'];
    }

    public function edad()
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->diffInYears(now()) : null;
    }



    // Relación isomórfica para la columna 'pagador' en la tabla 'factura'
    public function facturasComoPagador()
    {
        return $this->morphMany(Factura::class, 'pagador');
    }
}

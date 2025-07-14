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
        'tipo_documento_id',
        'numero_documento',
        'fecha_nacimiento',
        'sexo',
        'nacional',
        'telefono',

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

    public function afiliacionSalud()
    {
        return $this->hasOne(AfiliacionSalud::class);
    }

    public function direccion()
    {
        return $this->morphOne(Direccion::class, 'direccionable');
    }
    public function telefonos()
    {
        return $this->morphMany(Telefono::class, 'telefonoable');
    }
    public function email()
    {
        return $this->morphOne(CorreoElectronico::class, 'emailable');
    }
    public function redesSociales()
    {
        return $this->morphMany(RedSocial::class, 'redable');
    }
    public function contactoEmergencia()
    {
        return $this->hasOne(ContactoEmergencia::class, 'paciente_id');
    }
    public function procedencia()
    {
         return $this->morphOne(PaisProcedencia::class, 'procedencia');
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

    public function edad($fecha=null): ?string
    {
        // Si no se proporciona una fecha, se usa la fecha actual
        $fecha = $fecha ?: now();
        if (!$this->fecha_nacimiento) {
            return null;
        }

        $diff = $this->fecha_nacimiento->diff($fecha);

        if ($diff->y > 0) {
            return $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
        } elseif ($diff->m > 0) {
            return $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
        } else {
            return $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
        }
    }
    public function tipo_documento()
    {
        return $this->belongsTo(TipoDocumento::class);
    }

    public function scopeNacional($query)
    {
        return $query->where('nacional', true);
    }
}

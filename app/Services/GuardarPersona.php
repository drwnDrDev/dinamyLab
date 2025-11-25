<?php
namespace App\Services;

use App\Models\Pais;
use App\Models\Persona;
use App\Services\NombreParser;
use Carbon\Carbon;
use App\Services\GuardarContacto;

class GuardarPersona
{

    /**
     * Guarda una nueva persona en la base de datos.
     *
     * @param array $datos Datos de la persona a guardar.
     * @return Persona
     */
    public static function guardar(array $datos): Persona
    {

        // Dividir nombres y apellidos en primer y segundo nombre
        $parsed = NombreParser::parsearPersona(
            $datos['nombres'],
            $datos['apellidos']
        );

        // Crear la persona
       $persona =  Persona::create([
            'primer_nombre' => $parsed['primer_nombre'],
            'segundo_nombre' => $parsed['segundo_nombre'],
            'primer_apellido' => $parsed['primer_apellido'],
            'segundo_apellido' => $parsed['segundo_apellido'],
            'tipo_documento_id' =>  \App\Models\TipoDocumento::idPorCodigoRips($datos['tipo_documento']),
            'numero_documento' => $datos['numero_documento'],
            'fecha_nacimiento' => Carbon::parse($datos['fecha_nacimiento']),
            'sexo' => $datos['sexo'],
            'pais_origen' => $datos['pais_nacimiento'] ?? 170,
        ]);


        // Guardar contacto si se proporcionó
        GuardarContacto::guardarContacto($datos, $persona);
        Pais::incrementarNivelPais($persona->pais_origen);

        return $persona;
    }
}

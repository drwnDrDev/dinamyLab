<?php
namespace App\Services;

use App\Models\Persona;

use App\Http\Requests\StorePersonaRequest;
use App\Models\Municipio;
use App\Services\NombreParser;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GuardarPersona
{
    public static function guardarContacto(StorePersonaRequest $datos, Persona $persona): ?int
    {
        if (
            !$datos->has('telefono') &&
            !$datos->has('direccion') &&
            !$datos->has('correo') &&
            !$datos->has('red_social_nombre')
        ) {
            Log::warning('No se proporcionaron datos de contacto');
            return null;
        }

        if (
            !$datos->has('municipio') ||
            $datos->input('municipio') === '' ||
            $datos->input('municipio') === null
        ) {
            $sede = session('sede');
            log('Sede actual: ' . json_encode($sede));
            if ($sede && $sede->municipio_id) {
                $datos->merge(['municipio' => $sede->municipio_id]);
            } else {
                Log::warning('No se proporcionó municipio, usando Bogotá por defecto');
                $datos->merge(['municipio' => 11001]); // Bogotá
            }

        }else {
            $municipio=Municipio::findOrFail($datos->input('municipio'));
            $municipio->nivel = $municipio->nivel + 1;
            $municipio->save();
        }
        $persona->direccion()->create([
            'direccion' => $datos->input('direccion', null),
            'municipio_id' => $datos->input('municipio', 11001), // Default to Bogotá
        ]);

        if (
            $datos->has('eps') &&
            $datos->input('eps') !== '' &&
            $datos->input('eps') !== null
        ) {
            $persona->afiliacionSalud()->create([
                'eps' => $datos->input('eps', 'sisben'),
                'tipo_afiliacion' => $datos->input('tipo_afiliacion', 'Subsidiado'),
            ]);
        }

        if (
            $datos->has('pais') &&
            $datos->input('pais') !== '' &&
            $datos->input('pais') !== null
        ) {
            $persona->procedencia()->create([
                'pais_codigo_iso' => $datos->input('pais', 'COL'), // Default to Colombia
            ]);
        }

        if (
            $datos->has('telefono') &&
            $datos->input('telefono') !== '' &&
            $datos->input('telefono') !== null
        ) {
            $persona->telefonos()->create([
                'numero' => $datos->input('telefono'),
            ]);
        }

        if (
            $datos->has('correo') &&
            $datos->input('correo') !== '' &&
            $datos->input('correo') !== null
        ) {
            $persona->email()->create([
                'email' => $datos->input('correo'),
            ]);
        }


        return null;
    }


    /**
     * Guarda una nueva persona en la base de datos.
     *
     * @param array $datos Datos de la persona a guardar.
     * @return Persona
     */
    public static function guardar(StorePersonaRequest $request): Persona
    {
        $request->validated();

        // Dividir nombres y apellidos en primer y segundo nombre
        $parsed = NombreParser::parsearPersona(
            $request->input('nombres'),
            $request->input('apellidos')
        );

        // Asignar booleano a nacional
        $nacional = $request->input('pais') === 'COL' || $request->input('pais') === null;

        // Crear la persona
       $persona =  Persona::create([
            'primer_nombre' => $parsed['primer_nombre'],
            'segundo_nombre' => $parsed['segundo_nombre'],
            'primer_apellido' => $parsed['primer_apellido'],
            'segundo_apellido' => $parsed['segundo_apellido'],
            'tipo_documento' => $request->input('tipo_documento'),
            'numero_documento' => $request->input('numero_documento'),
            'fecha_nacimiento' => Carbon::parse($request->input('fecha_nacimiento')),
            'sexo' => $request->input('sexo'),
            'nacional' => $nacional,
        ]);

        // Guardar contacto si se proporcionó
        self::guardarContacto($request, $persona);

        return $persona;
    }
}

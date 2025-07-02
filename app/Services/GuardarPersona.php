<?php
namespace App\Services;

use App\Models\Persona;
use App\Http\Requests\StorePersonaRequest;
use App\Services\NombreParser;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class GuardarPersona
{


    public static function guardarContacto(StorePersonaRequest $datos, Persona $persona): ?int
    {
        if (!$datos->has('telefono') && !$datos->has('direccion') && !$datos->has('correo') && !$datos->has('red_social_nombre')) {
            Log::warning('No se proporcionaron datos de contacto');
            return null;
        }
        // Si no se proporciona un municipio, se asume Bogotá (11007)

        $persona->direccion()->create([
            'direccion' => $datos->input('direccion',null),
            'municipio_id' => $datos->input('municipio_id', 11007), // Default to Bogotá
        ]);
        if ($datos->has('eps')) {
            $persona->afiliacionSalud()->create([
                'eps_id' => $datos->input('eps'),
            ]);
        }
        if ($datos->has('pais')) {
            $persona->procedencia()->create([
                'pais' => $datos->input('pais'),
            ]);
        }
        if ($datos->has('telefono')) {
            $persona->telefonos()->create([
                'numero' => $datos->input('telefono'),
            ]);
        }
        if($datos->input('correo')){
        $persona->emails()->create([
            'email' => $datos->input('correo'),
        ]);
        }

            // $persona->redesSociales()->create([
            //     'nombre' => $datos->input('red_social_nombre'),
            //     'url' => $datos->input('red_social_url'),
            //     'perfil' => $datos->input('red_social_perfil'),
            // ]);


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

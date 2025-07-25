<?php
namespace App\Services;

use App\Models\Municipio;
use App\Models\RedSocial;
use Exception;
use Illuminate\Database\Eloquent\Model;

class GuardarContacto
{
    public static function guardarContacto(array $datos, Model $modelo): bool
    {

        $modeloClass = get_class($modelo);

        if(!$datos || empty($datos)) {
            throw new Exception("Error Processing Request", 1);
            ('No se proporcionaron datos de contacto');
            return false;
        }
        if($datos['telefono']) {
            $modelo->telefonos()->create(['numero' => $datos['telefono']]);
        }
        if($datos['direccion']) {
            if (!isset($datos['municipio']) || empty($datos['municipio']) || Municipio::find($datos['municipio']) === null) {
                $datos['municipio'] = ElegirEmpresa::defaultDireccion()->municipio_id ?? 11007; // Bogotá por defecto
            }
            $modelo->direccion()->create([
                'direccion' => $datos['direccion'],
                'municipio_id' =>  $datos['municipio']
            ]);
        }else {
            $datos['municipio'] = ElegirEmpresa::defaultDireccion()->municipio_id ?? 11007; // Bogotá por defecto
            $modelo->direccion()->create([
                'direccion' => null,
                'municipio_id' =>  $datos['municipio']
            ]);
        }

        if($datos['correo']) {
            if($modeloClass === 'App\Models\Persona') {
                $modelo->email()->create(['correo' => $datos['correo']]);
            } else {
                $modelo->emails()->create(['correo' => $datos['correo']]);
            }

        }
        if($datos['redes_sociales']) {
            foreach ($datos['redes_sociales'] as $redSocial) {
                RedSocial::create(['nombre' => $redSocial,
                    'url' => $redSocial['url'],
                    'perfil' => $redSocial['perfil'],
                    'redable_type' => $modeloClass,
                    'redable_id' => $modelo->id
                    ]);
            }
        }

        return true;
    }
}

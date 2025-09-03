<?php
namespace App\Services;

use App\Models\Municipio;
use App\Models\RedSocial;
use App\Models\Pais;
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
        if($datos['telefono'] ) {
            $modelo->telefonos()->firstOrCreate(['numero' => $datos['telefono']]);
        }
        if($datos['municipio'] || $datos['direccion']) {
            $modelo->direccion()->firstOrCreate([
                'direccion' => $datos['direccion'],
                'municipio_id' =>  $datos['municipio']
            ],
            [
                'direccion' => $datos['direccion'] ?? null,
                'municipio_id' => $datos['municipio']?? '11001',
                'pais_id' => $datos['pais_residencia'] ?? 170,
                'codigo_postal' => $datos['codigo_postal'] ?? null,
                'zona' => $datos['zona'] ?? '02',
            ]);
            Municipio::incrementarNivel($datos['municipio']);
        }
        if(isset($datos['pais_residencia'])) {
            $modelo->direccion()->updateOrCreate([
                'pais_id' => $datos['pais_residencia']
            ]);
            Pais::incrementarNivelPais($datos['pais_residencia']);
        }

        if($datos['correo']) {
            if($modeloClass === 'App\Models\Persona') {
                $modelo->email()->updateOrCreate(['email' => $datos['correo']]);
            } else {
                $modelo->emails()->firstOrCreate(['email' => $datos['correo']]);
            }


        }
        if(isset($datos['eps'])) {
            $modelo->afiliacionSalud()->updateOrCreate(['eps' => $datos['eps']], [
                'tipo_afiliacion' => $datos['tipo_afiliacion'] ?? 12
            ]);
        }


        return true;
    }
}

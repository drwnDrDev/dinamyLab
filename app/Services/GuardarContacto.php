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
                $datos['municipio'] = ElegirEmpresa::defaultMunicipio();
            }
            $modelo->direccion()->create([
                'direccion' => $datos['direccion'],
                'municipio_id' =>  $datos['municipio']
            ]);
        }else {
            $datos['municipio'] = ElegirEmpresa::defaultMunicipio();
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
        if($datos['redes']) {
            foreach ($datos['redes'] as $redSocial => $url) {
                if(empty($url)) {
                    continue;
                }
                RedSocial::create(['nombre' => $redSocial,
                    'url' => $url,
                    'perfil' => 'perfil'.$datos['razon_social'].'_'.$redSocial,
                    'redable_type' => $modeloClass,
                    'redable_id' => $modelo->id
                    ]);

            }
        }

        return true;
    }
}

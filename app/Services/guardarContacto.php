<?php
namespace App\Services;
use App\Models\Contacto;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class GuardarContacto
{

    public static function filtrar($datos): array
    {
        // Filtrar solo los datos cuyo valor sea diferente de null
        return array_filter(
            $datos,
            function ($value) {
                return !is_null($value);
            }
        );



    }

    /**
     * Guarda un nuevo contacto en la base de datos.
     *
     * @param array $datos Datos del contacto a guardar.
     * @return Contacto El contacto guardado.
     */
    public static function guardar(array $datos): Contacto
    {
        // Filtrar solo los datos cuyo valor sea diferente de null
        $datos = self::filtrar($datos);

        if($datos===[]){
            Log::warning('contacto sin datos', ['user' => Auth::id()]);
            return Contactato::find(1); // Retorna un contacto por defecto si no hay datos           
        }

        $info_adicional = array_diff(
            $datos,
            ['telefono', 'municipio'] // Excluir estos campos del info_adicional
        );

        // Crear el contacto
        $contacto = Contacto::create([
            'telefono' => $datos['telefono'] ?? null,
            'municipio_id' => $datos['municipio'] ?? 11007, // Valor por defecto para Bogotá
            'info_adicional' => $info_adicional,
            'usuario_id' => Auth::id(),
        ]);
        return $contacto;
    }
}

<?php
namespace App\Services;

use App\Models\Convenio;

use App\Models\Municipio;
use App\Models\Pais;
use App\Services\NombreParser;

use Carbon\Carbon;
use App\Services\GuardarPersona;
use Illuminate\Support\Facades\Log;

class GuardarConvenio
{
    public static function guardarConvenio(array $datos): ?int
    {

        if (
            !$datos['razon_social'] ||
            !$datos['numero_documento'] ||
            !$datos['tipo_documento_id']
        ) {
            Log::warning('Faltan datos obligatorios para el convenio');
            return null;
        }

        $convenio = new Convenio();
        $convenio->fill([
            'tipo_documento_id' => $datos['tipo_documento_id'],
            'numero_documento' => $datos['numero_documento'],
            'razon_social' => $datos['razon_social'],
            'descuento' => $datos['descuento'] ?? 0,
        ]);
        {
            Log::warning('Faltan datos obligatorios para el convenio');
            return null;
        }

        if ($convenio->save()) {
            Log::info('Convenio guardado exitosamente');
            return $convenio->id;
        } else {
            Log::error('Error al guardar el convenio');
            return null;
        }
    }
}

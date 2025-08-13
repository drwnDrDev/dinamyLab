<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ResultadoController extends Controller
{

public function json_rips(Request $request)
{
    $procedimientos = Procedimiento::with(['persona', 'examen'])
        ->whereBetween('fecha_procedimiento', ['2025-07-01', '2025-07-31'])
        ->where('prestador_id', 1)
        ->get();

    $procedimientosPorPersona = $procedimientos->groupBy('persona_id');

    $usuarios = [];
    foreach ($procedimientosPorPersona as $usuario) {
        $usuarios[] = [
            "tipoDocumentoIdentificacion" =>  $usuario->first()->persona->tID,
            "numDocumentoIdentificacion" =>  $usuario->first()->persona->numero_doc,
            "tipoUsuario" => "04",
            "fechaNacimiento" => $usuario->first()->persona->fecha_nacimiento,
            "codSexo" => $usuario->first()->persona->sexo,
            "codPaisResidencia" => "170",
            "codMunicipioResidencia" => "11007",
            "codZonaTerritorialResidencia" => "07",
            "incapacidad" => "NO",
            "codPaisOrigen" => "170",
            "consecutivo" => 1,
            "servicios" => [
                "procedimientos" => $usuario->map(function($procedimiento) {
                    return [
                        "codPrestador" => "110010822701",
                        "fechaInicioAtencion" => $procedimiento->fecha_procedimiento . " 00:00",
                        "idMIPRES" => "",
                        "numAutorizacion" => $procedimiento->factura,
                        "codProcedimiento" => $procedimiento->examen->CUP,
                        "viaIngresoServicioSalud" => "03",
                        "modalidadGrupoServicioTecSal" => "01",
                        "grupoServicios" => "03",
                        "codServicio" => 328,
                        "finalidadTecnologiaSalud" => "15",
                        "tipoDocumentoIdentificacion" => "CC",
                        "numDocumentoIdentificacion" => "51934571",
                        "codDiagnosticoPrincipal" => "Z017",
                        "codDiagnosticoRelacionado" => null,
                        "codComplicacion" => null,
                        "vrServicio" => 0,
                        "conceptoRecaudo" => "05",
                        "valorPagoModerador" => 0,
                        "numFEVPagoModerador" => "",
                        "consecutivo" => 1
                    ];
                })->values()->toArray()
            ]
        ];
    }

    return response()->json([
        'usuarios' => $usuarios
    ]);
}
}

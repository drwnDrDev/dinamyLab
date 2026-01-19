<?php

namespace App\Http\Controllers\Api;

use App\Estado;
use Illuminate\Http\Request;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Http\Controllers\Controller;
use App\Services\EscogerReferencia;
use Symfony\Component\Console\Input\Input;

class ResultadosController extends Controller
{

public function index()
{
    $resultados = Resultado::with(['procedimiento', 'procedimiento.persona', 'procedimiento.examen'])->get();

    if ($resultados->isEmpty()) {
        return response()->json(['message' => 'No hay resultados disponibles'], 204);
    }

    return response()->json([
        "message" => "Resultados obtenidos",
         "data"=>  $resultados
    ], 200);
}

public function show($id)
{
    $resultado = Resultado::with(['procedimiento', 'procedimiento.persona', 'procedimiento.examen'])
                         ->find($id);

    if (!$resultado) {
        return response()->json(['message' => 'Resultado no encontrado'], 404);
    }

    return response()->json([
        "message" => "Resultado encontrado",
         "data"=>  $resultado
    ], 200);
}

    public function store(Request $request, Procedimiento $procedimiento)
    {
        // Verificar si es una solicitud JSON (desde el formulario de lotes)
        if ($request->isJson()) {
            $datos = $request->json()->all();

            if (isset($datos['resultados']) && is_array($datos['resultados'])) {
                // Procesar resultados en formato JSON
                EscogerReferencia::guardaResultado($datos['resultados'], $procedimiento);
                $procedimiento->estado = Estado::TERMINADO;
                $procedimiento->fecha = now();
                $procedimiento->empleado_id = auth()->user()->id ?? 1;
                $procedimiento->save();

                return response()->json([
                    'message' => 'Resultados guardados correctamente',
                    'procedimiento_id' => $procedimiento->id,
                    'estado' => $procedimiento->estado
                ], 200);
            }
        }

        // Procesar de forma tradicional (desde formulario HTML)
        EscogerReferencia::guardaResultado($request->except(['_token', 'submit']), $procedimiento);
        $procedimiento->estado = Estado::TERMINADO;
        $procedimiento->fecha = $request->input('fecha_procedimiento', now());
        $procedimiento->empleado_id = $request->input('empleado_id', auth()->user()->id ?? 1);
        $procedimiento->save();

        return redirect()->route('resultados.show', $procedimiento)->with('success', 'Resultados guardados correctamente.');
    }

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

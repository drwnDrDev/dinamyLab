<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Empleado;
use App\Models\Orden;
use App\Models\Persona;
use App\Models\Procedimiento;
use Illuminate\Http\Request;

class ProcedimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $sede = session('sede');
        if (!$sede) {
            return redirect()->back()->withErrors(['sede' => 'No se ha seleccionado una sede.'])->withInput();
        }
        $procedimientos = Procedimiento::with(['orden.paciente', 'examen'])

            ->where('sede_id', $sede->id)
            ->where('estado','en proceso')
            ->orderBy('updated_at')
            ->get();


        return view('procedimientos.index', compact('procedimientos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $ordenes =Orden::with(['paciente'])
            ->where('estado', '!=', 'CANCELADA')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('procedimientos.resultados', compact('ordenes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Procedimiento $procedimiento)
    {
        return view('procedimientos.show', compact('procedimiento'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Procedimiento $procedimiento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Procedimiento $procedimiento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Procedimiento $procedimiento)
    {
        //
    }

    public function resultado()
    {
        $ordenes = Orden::with(['paciente'])
            ->where('estado', '!=', 'CANCELADA')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('procedimientos.resultados', compact('ordenes'));
    }
    /**
     * Display a listing of the examenes.
     */

    public function reportes(Request $request){

        $startDate = $request->input('fecha_inicio', now()->startOfMonth()->toDateString());
        $endDate = $request->input('fecha_fin', now()->endOfMonth()->toDateString());
        $procedimientos = Procedimiento::with(['examen'])
            ->where('fecha', '>=', $startDate)
            ->where('fecha', '<=', $endDate)
            ->selectRaw('examen_id, COUNT(*) as total_procedimientos')
            ->groupBy('examen_id')
            ->orderByDesc('total_procedimientos')
            ->get();

        $sedes = \App\Models\Sede::orderBy('nombre')->get();
        return view('procedimientos.rips', compact('procedimientos', 'sedes','startDate', 'endDate'));
    }

    public function examenes()
    {
        $examenes = Examen::with(['procedimientos', 'ordenes'])
            ->orderBy('nombre')
            ->get();
        return view('procedimientos.examenes', compact('examenes'));
    }
    public function observaciones(Request $request, Procedimiento $procedimiento)
    {
        $request->validate([
            'observacion' => 'required|string|max:255',
            'estado' => 'required|in:pendiente de muestra,anulado',
        ]);
         $observacion = $request->input('observacion','SIN OBSERVACIÓN'); ;
         $estado = $request->INPUT('estado', 'pendiente de muestra');


        $procedimiento->estado = $estado;
        $procedimiento->save();


        return response()->json([
            'message' => 'Observación actualizada correctamente',
            'estado' => $procedimiento->estado,
            'observacion' => $observacion,
        ]);
    }

public function json_rips(Request $request)
{


    $procedimientos = Procedimiento::whereBetween('fecha', [$request->input('fecha_inicio',now()->startOfMonth()), $request->input('fecha_fin',now()->endOfMonth())])
        ->where('sede_id', $request->input('sede_id',$sedeActual->id))
        ->where('estado','terminado')
        ->get();


        $procedimientos = $procedimientos->groupBy('orden.paciente_id')->map(function ($usuarios) use (&$i) {
        $currentUsuario = $usuarios->first()->orden->paciente;
        $i++;

        return [
            "tipoDocumentoIdentificacion" => $currentUsuario->tipo_documento->cod_rips,
            "numDocumentoIdentificacion" => $currentUsuario->numero_documento,
            "tipoUsuario" => "04",
            "fechaNacimiento" => $currentUsuario->fecha_nacimiento->format('Y-m-d'),
            "codSexo" => $currentUsuario->sexo,
            "codPaisResidencia" =>$currentUsuario->cod_pais_residencia ?? "170",
            "codMunicipioResidencia" => $currentUsuario->cod_municipio_residencia ?? "11001",
            "codZonaTerritorialResidencia" => $currentUsuario->cod_zona_territorial_residencia ?? "01",
            "incapacidad" => $currentUsuario->incapacidad ?? "NO",
            "codPaisOrigen" => $currentUsuario->cod_pais_origen ?? "170",
            "consecutivo" => $i,
            "servicios" => [
            "procedimientos" => $usuarios->map(function($procedimiento, $index) {
                return [
                        "codPrestador" => $procedimiento->sede->codigo_prestador,
                        "fechaInicioAtencion" => $procedimiento->fecha->format('Y-m-d H:i'),
                        "idMIPRES" => "",
                        "numAutorizacion" => null,
                        "codProcedimiento" => $procedimiento->examen->cup,
                        "viaIngresoServicioSalud" => $procedimiento->codigo_via_ingreso,
                        "modalidadGrupoServicioTecSal" => $procedimiento->codigo_modalidad,
                        "grupoServicios" => $procedimiento->servicioHabilitado->codigo_grupo ?? "03",
                        "codServicio" => $procedimiento->codigo_servicio,
                        "finalidadTecnologiaSalud" => $procedimiento->codigo_finalidad,
                        "tipoDocumentoIdentificacion" => "CC",
                        "numDocumentoIdentificacion" => '51934571',//valor por defecto para pruebas
                        "codDiagnosticoPrincipal" => $procedimiento->diagnosticoPrincipal->codigo,
                        "codDiagnosticoRelacionado" => $procedimiento->diagnosticoRelacionado->codigo ?? null,
                        "codComplicacion" => null,
                        "vrServicio" => 0,
                        "conceptoRecaudo" => "05",
                        "valorPagoModerador" => 0,
                        "numFEVPagoModerador" => "",
                        "consecutivo" => $index + 1
                ];
            })

     ]];
    })->values();




//descargar el archivo JSON
if (!empty($procedimientos)) {
    $json = json_encode(array(
           "numDocumentoIdObligado"=> "51934571",
            "numFactura"=> null,
            "tipoNota"=> "RS",
            "numNota"=> date('Ym'),
        "usuarios" => $procedimientos
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fileName = 'rips_'.date('YmdHis').'.json';

    return response($json, 200)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
}
}



public function usuarios()
{
$personas = Persona::with(['procedimientos.examen'])
    ->whereHas('procedimientos', function ($query) {
        $query->whereBetween('fecha_procedimiento', ['2025-07-01', '2025-07-31'])
              ->where('prestador_id', 1);
    })
    ->get();

$usuarios = $personas->map(function($persona) {
    return [
        "tipoDocumentoIdentificacion" => $persona->tID,
        "numDocumentoIdentificacion" => $persona->numero_doc,
        "tipoUsuario" => "12",
        "fechaNacimiento" => $persona->fecha_nacimiento,
        "codSexo" => $persona->sexo,
        "codPaisResidencia" => "170",
        "codMunicipioResidencia" => "11007",
        "codZonaTerritorialResidencia" => "01",
        "incapacidad" => "NO",
        "codPaisOrigen" => "170",
        "consecutivo" => 1,
        "servicios" => [
            "procedimientos" => $persona->procedimientos->map(function($procedimiento) {
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
                    "tipoDocumentoIdentificacion" => $persona->tID,
                    "numDocumentoIdentificacion" => $persona->numero_doc,
                    "codDiagnosticoPrincipal" => "Z017",
                    "codDiagnosticoRelacionado" => null,
                    "codComplicacion" => null,
                    "vrServicio" => 0,
                    "conceptoRecaudo" => "05",
                    "valorPagoModerador" => 0,
                    "numFEVPagoModerador" => "",
                    "consecutivo" => 1
                ];
            })
        ]
    ];
});

return response()->json([
    'usuarios' => $usuarios
]);

}
}

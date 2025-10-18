<?php

namespace App\Http\Controllers;

use App\Models\Examen;
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

    public function reportes(){
        $procedimientos = Procedimiento::with(['examen'])
            ->where('fecha', '>=', now()->startOfMonth())
            ->where('fecha', '<=', now()->endOfMonth())
            ->selectRaw('examen_id, COUNT(*) as total_procedimientos')
            ->groupBy('examen_id')
            ->orderByDesc('total_procedimientos')
            ->get();
        return view('procedimientos.rips', compact('procedimientos'));
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


    $procedimientos = Procedimiento::with(['orden.paciente', 'examen'])
        ->whereBetween('fecha', [$request->input('fecha_inicio',now()->startOfMonth()), $request->input('fecha_fin',now()->endOfMonth())])
        ->where('sede_id', $request->input('sede_id',1))
        ->get();

   $procedimientoData= $procedimientos->groupBy('orden.paciente.numero_documento')->map(function($items, $key) {
        return [
            'numero_documento' => $key,
            'procedimientos' => $items->map(function($procedimiento) {
                return [
                    'fecha_procedimiento' => $procedimiento->fecha,
                    'factura' => $procedimiento->factura_id,
                    'CUP' => $procedimiento->examen->CUP,
                ];
            })->toArray(),
        ];
    })->values()->toArray();

    dd($procedimientoData);


    $usuarios = $procedimientos->map(function($usuario) {
        return array(
            "tipoDocumentoIdentificacion" => $usuario->tipo_documento,
            "numDocumentoIdentificacion" => $usuario->numero_documento,
            "tipoUsuario" => "04",
            "fechaNacimiento" => $usuario->fecha_nacimiento,
            "codSexo" => $usuario->sexo,
            "codPaisResidencia" => "170",
            "codMunicipioResidencia" => "11001",
            "codZonaTerritorialResidencia" => "01",
            "incapacidad" => "NO",
            "codPaisOrigen" => "170",
            "consecutivo" => 1,
            "servicios" => array(
                "procedimientos" => array_map(function($procedimiento) {
                    return array(
                        "codPrestador" => "110010822701",
                        "fechaInicioAtencion" => $procedimiento['fecha_procedimiento'] . " 00:00",
                        "idMIPRES" => "",
                        "numAutorizacion" => $procedimiento['factura'],
                        "codProcedimiento" => $procedimiento['CUP'],
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
                    );
                }, $usuario->procedimientos->toArray())
            )
        );

    });
//descargar el archivo JSON
if (!empty($usuarios)) {
    $json = json_encode(array(
           "numDocumentoIdObligado"=> "51934571",
            "numFactura"=> null,
            "tipoNota"=> "RS",
            "numNota"=> "6532",
        "usuarios" => $usuarios
    ), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    $fileName = 'rips.json';

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

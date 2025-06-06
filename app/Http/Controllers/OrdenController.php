<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Models\Orden;
use App\Models\Examen;
use App\TipoDocumento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Procedimiento;
class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ordenes = Orden::orderBy('created_at', 'desc')->get();
        return view('ordenes.index', compact('ordenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $tipos_documento = collect(TipoDocumento::cases())
            ->mapWithKeys(fn($tipo) => [$tipo->value => $tipo->nombre()]);
        $examenes = Examen::all();

        $orden_numero = Orden::max('numero') + 1;
        return view('ordenes.create', compact('tipos_documento', 'examenes', 'orden_numero'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $request->validate([
            'paciente_id' => 'required|exists:personas,id',
            'acompaniante_id' => 'nullable|exists:personas,id',
            'numero_orden' => 'required|string|max:20|unique:ordenes_medicas,numero',
        ]);


        $examenes = array_filter(
            $request->input('examenes', []),
            function ($cantidad) {
            return !is_null($cantidad) && $cantidad != 0;
            }
        );
        

        if (empty($examenes)) {
            return redirect()->back()->withErrors(['examenes' => 'Debe seleccionar al menos un examen.']);
        }

        $total = Examen::whereIn('id', array_keys($examenes))
            ->get()
            ->sum(function ($examen) use ($examenes) {
                return $examen->valor * $examenes[$examen->id];
            });


        $abono = $request->input('pago')==="on"? $total : $request->input('abono', 0);

        $orden = Orden::create([
            'numero' => $request->input('numero_orden'),
            'paciente_id' => $request->input('paciente_id'),
            'acompaniante_id' => $request->input('acompaniante_id'),
            'descripcion' => $request->input('observaciones'),
            'abono' => $abono,
            'total' => $total,
        ]);



        $orden_examen = array_map(function ($examen) use ($orden , $examenes) {
            return [
                'orden_id' => $orden->id,
                'examen_id' => $examen,
                'cantidad' => $examenes[$examen],

            ];
        }, array_keys($examenes));
        // Asignar los procedimientos a la orden

        $procedimientos = [];
        foreach ($orden_examen as $examen) {
            for ($i = 0; $i < $examen['cantidad']; $i++) {
            $procedimientos[] = [
                'orden_id' => $examen['orden_id'],
                'empleado_id' => Auth::user()->id,
                'examen_id' => $examen['examen_id'],
                'fecha' => now(),
                'estado' => Estado::PROCESO,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            }
        }

        Procedimiento::insert($procedimientos);

        return redirect()->route('ordenes')->with('success', 'Orden médica creada correctamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Orden $orden)
    {
        $orden->load(['paciente', 'acompaniante']);

        $procedimientos = $orden->procedimientos;
        return view('ordenes.show', compact('orden', 'procedimientos'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Orden $orden)
    {

        return view('ordenes.edit', compact('orden'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Orden $orden)
    {
        //
    }

    public function resultados(Orden $orden,Examen $examen)
    {
        $orden->load(['paciente', 'acompaniante', 'examenes']);
        return view('procedimientos.resultados', compact('orden', 'examen'));
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Orden $orden)
    {
        if (Auth::user()->can('eliminar_orden_medica')) {
            $orden->delete();
            return redirect()->route('ordenes')->with('success', 'Orden médica eliminada correctamente');
        }
        return redirect()->route('ordenes')->with('error', 'No tienes permiso para eliminar esta orden médica');
    }
}

<?php


namespace App\Http\Controllers;

use App\Estado;

use Illuminate\Http\Request;
use App\Models\Procedimiento;
use App\Services\EscogerReferencia;
use App\Models\Persona;
use App\Models\Orden;

class ResultadosController extends Controller
{

    public function index()
    {

        $resultados = Procedimiento::with(['orden.paciente', 'resultado'])
            ->whereHas('resultado')
            ->where('estado', Estado::TERMINADO)
            ->where('updated_at', '>=', now()->subDays(2))
            ->orderBy('updated_at', 'desc')
            ->get();

        if ($resultados->isEmpty()) {
            return redirect()->back()->with('info', 'No hay resultados disponibles.');
        }
        return view('resultados.index', compact('resultados'));
    }
    public function show(Procedimiento $procedimiento)
    {


        // Carga el procedimiento con sus relaciones necesarias
        $procedimiento->load(['orden.paciente', 'resultado', 'sede']);

        // Verifica si el procedimiento ya tiene resultados
        if ($procedimiento->resultado->isEmpty()) {
            return redirect()->route('resultados.create', $procedimiento)
                ->with('warning', 'No hay parámetros para este examen. Por favor, crea los resultados.');
        }
        $parametros = EscogerReferencia::obtenerResultados($procedimiento);

        return view('resultados.show', compact('procedimiento', 'parametros'));
    }
    public function create(Procedimiento $procedimiento)
    {


        $parametros = EscogerReferencia::recorrerParametrosExamen($procedimiento->load(['orden.paciente', 'examen.parametros']));
        if (empty($parametros)) {
            return redirect()->route('resultados.show', $procedimiento)
                ->with('warning', 'No hay parámetros para este examen. Por favor, crea los resultados.');
        }
        if ($procedimiento->estado === Estado::TERMINADO->value || $procedimiento->estado === Estado::ENTREGADO->value) {
            return redirect()->route('resultados.show', $procedimiento->id)
                ->with('info', 'Este procedimiento ya ha sido completado.');
        }
        if ($procedimiento->estado === Estado::ANULADO->value) {
            return redirect()->route('resultados.show', $procedimiento)
                ->with('error', 'Este procedimiento ha sido anulado y no puede ser editado.');
        }

        if ($procedimiento->estado === Estado::PENDIENTE->value || $procedimiento->estado === Estado::MUESTRA->value) {
            $procedimiento->estado = Estado::PROCESO->value; // Cambia el estado a 'en proceso'
            $procedimiento->save();
        }

        return view('resultados.create', compact('parametros', 'procedimiento'));
    }
    public function store(Request $request, Procedimiento $procedimiento)
    {

    $procedimiento->fecha = $request->input('fecha_procedimiento', now()); // Actualiza la fecha del procedimiento con la fecha enviada o la fecha actual
    $procedimiento->save();

    // Validar unicidad de (procedimiento_id, parametro_id) antes de guardar
        $input = collect($request->except(['_token', 'submit', 'resultados']));

        // Si viene la estructura 'resultados' (para procesamiento por lotes), usar esa
        if ($request->has('resultados')) {
            $input = collect($request->input('resultados'));
        }

        $duplicates = [];

        foreach ($input->keys() as $paramId) {
            if (is_numeric($paramId) && $procedimiento->resultado()->where('parametro_id', $paramId)->exists()) {
                $duplicates[] = $paramId;
            }
        }

        if (!empty($duplicates)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ya existen resultados para los parámetros: ' . implode(', ', $duplicates)
                ], 422);
            }
            return redirect()->back()
                ->with('warning', 'Ya existen resultados para los parámetros: ' . implode(', ', $duplicates));
        }

        EscogerReferencia::guardaResultado($input->toArray(), $procedimiento);

        $procedimiento->estado = Estado::TERMINADO; // Cambia el estado del procedimiento a 'terminado'
        $procedimiento->fecha = now(); // Actualiza la fecha del procedimiento
        $procedimiento->empleado_id = auth()->user()->empleado->id; // Asigna el empleado que guarda los resultados
        $procedimiento->save();

        // Si es una petición JSON (AJAX), devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Resultados guardados correctamente.',
                'procedimiento_id' => $procedimiento->id
            ], 200);
        }

        return redirect()->route('resultados.show', $procedimiento)->with('success', 'Resultados guardados correctamente.');
    }
    public function edit(Procedimiento $procedimiento)
    {

        // Carga el procedimiento con sus relaciones necesarias
        $procedimiento->load(['orden.paciente', 'resultado', 'sede', 'examen.parametros.opciones']);

        // Verifica si el procedimiento ya tiene resultados
        if ($procedimiento->resultado->isEmpty()) {
            return redirect()->route('resultados.create', $procedimiento)
                ->with('warning', 'No hay parámetros para este examen. Por favor, crea los resultados.');
        }

        // Combina parámetros del examen con resultados en una sola colección
        $parametros = collect(EscogerReferencia::obtenerResultados($procedimiento));


        return view('resultados.edit', compact('procedimiento', 'parametros'));
    }
    public function update(Request $request, Procedimiento $procedimiento)
    {
        // Validar unicidad de (procedimiento_id, parametro_id) antes de guardar
        $input = collect($request->except(['_token', 'submit']));
        $duplicates = [];
        foreach ($input->keys() as $paramId) {
            if (is_numeric($paramId) && !$procedimiento->resultado()->where('parametro_id', $paramId)->exists()) {
                $duplicates[] = $paramId;
            }
        }
        if (!empty($duplicates)) {
            return redirect()->back()
                ->with('warning', 'No existen resultados previos para los parámetros: ' . implode(', ', $duplicates));
        }
        EscogerReferencia::actualizaResultado($request->except(['_token', 'submit']), $procedimiento);
        return redirect()->route('resultados.show', $procedimiento)->with('success', 'Resultados actualizados correctamente.');
    }

    public function historia(Persona $persona)
    {
        $ordenes = Orden::where('paciente_id', $persona->id)
                       ->orderBy('updated_at', 'desc')
                       ->get();
        return view(
            'resultados.historia',
            compact('persona', 'ordenes')
        );
    }

    public function historia_show(Request $request, Persona $persona)
    {

        $sede = request()->session()->get('sede');
        $procedimientos = Procedimiento::find(array_keys($request->all()))->map(
            function ($procedimiento) {
                return [
                    'procedimiento' => $procedimiento,
                    'resultado' => EscogerReferencia::obtenerResultados($procedimiento)
                ];
            }
        );

        return view(
            'resultados.historia_show', compact('persona', 'procedimientos', 'sede')
        );
    }
}

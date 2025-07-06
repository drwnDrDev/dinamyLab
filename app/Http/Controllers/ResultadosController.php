<?php

namespace App\Http\Controllers;

use App\Estado;
use App\Models\Examen;
use Illuminate\Http\Request;
use App\Models\Procedimiento;
use App\Services\EscogerReferencia;

class ResultadosController extends Controller
{

    public function index()
    {

        $resultados= Procedimiento::with(['orden.paciente', 'resultado'])
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
        $procedimiento->load(['orden.paciente', 'resultado','sede']);

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

        // Verifica si el procedimiento ya tiene resultados
        $parametros = EscogerReferencia::recorrerParametrosExamen($procedimiento->load(['orden.paciente', 'examen.parametros']));

       return view('resultados.create', compact('parametros','procedimiento'));
    }
    public function store(Request $request, Procedimiento $procedimiento)
    {

        EscogerReferencia::guardaResultado($request->except(['_token','submit']),$procedimiento);

        $procedimiento->estado = Estado::TERMINADO; // Cambia el estado del procedimiento a 'terminado'
        $procedimiento->save();
        return redirect()->route('resultados.show', $procedimiento)->with('success', 'Resultados guardados correctamente.');
    }
}

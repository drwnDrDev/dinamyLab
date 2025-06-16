<?php

namespace App\Http\Controllers;

use App\Estado;
use Illuminate\Http\Request;
use App\Models\Procedimiento;
use App\Models\Orden;
use App\Models\Parametro;
use App\Services\EscogerReferencia;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ResultadosController extends Controller
{

    public function index()
    {

        return view('resultados.index');
    }
    public function show(Procedimiento $procedimiento)
    {

        return view('resultados.show', compact('procedimiento'));
    }
    public function create(Procedimiento $procedimiento)
    {
        return view('resultados.create', ['procedimiento' => $procedimiento]);
    }
    public function store(Request $request, Procedimiento $procedimiento)
    {
        
        return  EscogerReferencia::recorrerParamtrosExamen($procedimiento->load(['orden.paciente', 'examen.parametros']));
        $procedimiento->resultados = [
            "data"=>$request->except('_token', 'submit'),

            "meta" => [
                "created_by" => Auth::user()->id,
                "procedimiento_id" => $procedimiento->id,
                "created_at" => now(),
            ]

        ];

        $procedimiento->estado = Estado::TERMINADO; // Cambia el estado del procedimiento a 'terminado'
        $procedimiento->save();


        return redirect()->route('resultados.show', $procedimiento)->with('success', 'Resultados guardados correctamente.');
    }
}

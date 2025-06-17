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

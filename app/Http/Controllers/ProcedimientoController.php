<?php

namespace App\Http\Controllers;

use App\Models\Examen;
use App\Models\Orden;
use App\Models\Procedimiento;
use Illuminate\Http\Request;

class ProcedimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $procedimientos= Procedimiento::orderBy('updated_at', 'desc')
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
        return view('procedimientos.rips');
    }

    public function examenes()
    {
        $examenes = Examen::with(['procedimientos', 'ordenes'])
            ->orderBy('nombre')
            ->get();
        return view('procedimientos.examenes', compact('examenes'));
    }
}

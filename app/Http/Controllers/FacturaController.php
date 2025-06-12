<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use App\Models\Examen;
use App\Models\Factura;
use App\TipoDocumento;
use Illuminate\Http\Request;

class FacturaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $facturas = Factura::with(['paciente', 'orden'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('facturas.index', compact('facturas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $convenios = Convenio::orderBy('razon_social')->get();
        $tipos_documento = collect(TipoDocumento::cases())
            ->mapWithKeys(fn($tipo) => [$tipo->value => $tipo->nombre()]);

        $ordenes = \App\Models\Orden::with(['paciente'])
            ->where('estado', '!=', 'CANCELADA')
            ->orderBy('created_at', 'desc')
            ->get();
        return view('facturas.create', compact('ordenes','tipos_documento','convenios'));
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
    public function show(Factura $factura)
    {
        $factura->load(['paciente', 'orden.examenes']);
        return view('facturas.show', compact('factura'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Factura $factura)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Factura $factura)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Factura $factura)
    {
        //
    }
}

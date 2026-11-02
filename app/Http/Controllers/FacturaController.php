<?php

namespace App\Http\Controllers;

use App\Models\Convenio;
use App\Models\Examen;
use App\Models\Factura;
use App\Models\Procedimiento;
use App\Models\Persona;

use App\Estado;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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
    public function create(Persona $persona)
    {

        $convenios = Convenio::orderBy('razon_social')->get();
        $ordenes = \App\Models\Orden::with(['paciente', 'procedimientos' => function($query) {
            $query->whereIn('estado', [Estado::TERMINADO, Estado::ENTREGADO])
                  ->whereNull('factura_id')
                  ->with('examen');
            }])
            ->where('paciente_id', $persona->id)
            ->get();

        return view('facturas.create', compact('ordenes','convenios', 'persona'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validacion = $request->validate([
            'paciente_id' => 'required|exists:personas,id',
            'pagador_type' => 'required|in:persona,convenio',
            'numero_factura' => 'required|unique:facturas,numero',
            'procedimientos' => 'required|array',
            'subtotal' => 'required|numeric',
            'total' => 'required|numeric',
        ]);


        $facturaData = [
            'paciente_id' => $validacion['paciente_id'],
            'pagador_type' => $validacion['pagador_type'],
            'pagador_id' => $validacion['pagador_type'] === 'persona' ? $validacion['paciente_id'] : null,
            'numero' => $validacion['numero_factura'],
            'sede_id' => session('sede')->id,
            'subtotal' => $validacion['subtotal'],
            'total' => $validacion['total'],
            'fecha_emision' => Carbon::now(),
            'fecha_vencimiento' => Carbon::now()->addDays(30),
        ];

        dd($facturaData);
        $factura = Factura::create($facturaData);
        return redirect()->route('facturas.show', $factura);
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


}

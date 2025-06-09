<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Orden;
use App\Estado;

class OrdenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(string $id)
    {
        
     
        $orden = Orden::findOrFail($id);
        return response()->json($orden);
     
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function add(Request $request, string $id)
    {
        $orden = Orden::findOrFail($id);
        $request->validate([
            'examen_id' => 'required|exists:examenes,id',
            'cantidad' => 'required|integer|min:1',
        ]);
        $examen_id = $request->input('examen_id');
        $cantidad = $request->input('cantidad');
        $orden->examenes()->attach($examen_id, [
            'cantidad' => $cantidad,
     
        ]);
        // Crear los procedimientos asociados a la orden
        $procedimientos = [];
        for ($i = 0; $i < $cantidad; $i++) {
            $procedimientos[] = [
                'orden_id' => $orden->id,
          
                'examen_id' => $examen_id,
                'fecha' => now(),
                'estado' => Estado::PROCESO,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        $orden->procedimientos()->createMany($procedimientos);
         return response()->json(['message' => 'Procedimiento agregado a la orden correctamente.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

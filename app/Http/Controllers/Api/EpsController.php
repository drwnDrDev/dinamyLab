<?php

namespace App\Http\Controllers\Api;

use App\Models\Eps;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class EpsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
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
    public function show($id)
    {
        $eps = Eps::find($id);

        if (!$eps) {
            return response()->json([
                'message' => 'EPS no encontrada',
                'data' => null
            ], 404);
        }

        return response()->json([
            'message' => 'EPS encontrada',
            'data' => $eps

        ]);
    }




    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Eps $eps)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Eps $eps)
    {
        //
    }
}

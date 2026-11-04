<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Procedimiento;
use App\Services\ControlSede;
use Illuminate\Http\Request;

class ProcedimientosController extends Controller
{
    public function destroy(Request $request, Procedimiento $procedimiento)
    {
        ControlSede::autorizarProcedimiento($request, $procedimiento);
        $procedimiento->delete();

        return response()->json(['message' => 'Procedimiento eliminado correctamente.'], 200);
    }
}

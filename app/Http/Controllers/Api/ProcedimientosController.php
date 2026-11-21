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
        abort_if($procedimiento->bloqueadoPorRips(), 409, 'No se puede eliminar un procedimiento exportado a RIPS.');
        $procedimiento->delete();

        return response()->json(['message' => 'Procedimiento eliminado correctamente.'], 200);
    }
}

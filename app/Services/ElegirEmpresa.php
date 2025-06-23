<?php
namespace App\Services;
use App\Models\Parametro;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Resultado;
use App\Models\ValorReferencia;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ElegirEmpresa
{

    public static function elegirEmpresa( $procedimiento)
    {
        $procedimiento = Procedimiento::findOrFail($procedimiento);
        if ($procedimiento->empleado) {
            return $procedimiento->empleado->sede->empresa;
        }
        if (session('empresa')) {
            return Empresa::find(session('empresa'));
        }
        if (session('sede')) {
            return Sede::find(session('sede'))->empresa;
        }
        if (session('empleado')) {
            return Empleado::find(session('empleado'))->sede->empresa;
        }
        if (Auth::user()->empleado) {
            return Auth::user()->empleado->sede->empresa;
        }
        return Empresa::first();
    }
}
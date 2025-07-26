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

    public static function elegirSede()
    {
        $sede = session('sede');
        if ($sede) {
            return $sede;
        }
        if (Auth::user()->empleado) {
            return Auth::user()->empleado->sede;
        }
        if (session('empresa')) {
            return session('empresa')->sedes->first();
        }
        if (session('empleado')) {
            return session('empleado')->sede;
        }

        return null;
    }

    public static function defaultMu()
    {
        $empresa = self::elegirEmpresa();
        if ($empresa) {
            return $empresa->municipio->id;
        }
        // Usar el municipio con el nivel más alto como default
        $municipio = \App\Models\Municipio::orderByDesc('nivel')->first();
        return $municipio ? $municipio->id : null;
    }
}

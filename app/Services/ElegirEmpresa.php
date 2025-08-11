<?php

namespace App\Services;

use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Municipio;
use App\Models\Procedimiento;
use App\Models\Sede;
use Illuminate\Support\Facades\Auth;


class ElegirEmpresa
{

    public static function elegirEmpresa():?Empresa
    {

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

    public static function elegirSede():?Sede
    {
        $sede = session('sede');
        if ($sede) {
            return $sede;
        }
        if (session('empresa')) {
            return session('empresa')->sedes->first();
        }
        if (session('empleado')) {
            return session('empleado')->sede;
        }

        return null;
    }

    public static function defaultMunicipio():int
    {
        $empresa = self::elegirEmpresa();
        if ($empresa) {
            return $empresa->municipio->id;
        }
        // Usar el municipio con el nivel más alto como default
        $municipio = Municipio::orderByDesc('nivel')->first();
        return $municipio ? $municipio->id : null;
    }
}

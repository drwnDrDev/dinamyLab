<?php

namespace App\Http\Controllers;

use App\Services\ControlSede;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $sedeId = $request->user()->hasRole('admin')
            ? null
            : (int) ControlSede::autorizarSedeActiva($request)->id;
        $query = $request->input('search');

        // Validación
        if (
            !$query ||
            empty($query) ||
            !preg_match('/^[a-zA-Z0-9\s\-\_:ñÑ]+$/u', $query)
        ) {
            return view('search.busqueda_avanzada', compact('query'))
            ->with('error', 'No debes dejar el campo de búsqueda vacío o ingresar caracteres especiales.');
        }

        if (is_numeric($query)) {
            if (strlen($query) < 6) {
                $result = $this->buscarOrden($query, $sedeId)
                    ?? $this->buscarFactura($query, $sedeId)
                    ?? $this->buscarPersonaPorDocumento($query);
                return $result ?: view('search.busqueda_avanzada', compact('query'))
            ->with('error', 'No results found for the search query');
            } elseif (strlen($query) == 6) {
                $result = $this->buscarPersonaPorDocumento($query)
                    ?? $this->buscarExamenPorCup($query)
                    ?? $this->buscarFactura($query, $sedeId)
                    ?? $this->buscarOrden($query, $sedeId);
                return $result ?: view('search.busqueda_avanzada', compact('query'))
            ->with('error', 'No results found for the search query');
            } else {
                $result = $this->buscarPersonaPorDocumento($query)
                    ?? $this->buscarFactura($query, $sedeId)
                    ?? $this->buscarOrden($query, $sedeId);
                return $result ?: view('search.busqueda_avanzada', compact('query'))
            ->with('error', 'No results found for the search query');
            }
        }

       $result =  $this->buscarPersonaPorNombre($query)
            ?? $this->buscarExamenPorNombre($query);
        if ($result) {
            return $result;
        }


        return view('search.busqueda_avanzada', compact('query'))
            ->with('error', 'No se encontraron resultados para la búsqueda.');
    }

    private function buscarOrden($query, ?int $sedeId)
    {
        $orden = \App\Models\Orden::where('numero', $query)
            ->when($sedeId !== null, fn ($orden) => $orden->where('sede_id', $sedeId))
            ->first();
        if ($orden) {
            return redirect()->route('ordenes.show', $orden->id);
        }
        return null;
    }

    private function buscarFactura($query, ?int $sedeId)
    {
        $factura = \App\Models\Factura::query()
            ->when($sedeId !== null, fn ($facturas) => $facturas->where('sede_id', $sedeId))
            ->find($query);
        if ($factura) {
            return redirect()->route('facturas.show', $factura->id);
        }
        return null;
    }

    private function buscarExamenPorCup($query)
    {
        if (\App\Models\Examen::where('cup', $query)->exists()) {
            return redirect()->route('examenes', ['busqueda' => $query]);
        }
        return null;
    }

    private function buscarPersonaPorDocumento($query)
    {
        $persona = \App\Models\Persona::where('numero_documento', $query)->first();
        if ($persona) {
            return redirect()->route('personas.show', $persona->id);
        }
        return null;
    }

    private function buscarPersonaPorNombre($query)
    {
        $hayCoincidencias = \App\Models\Persona::where(function ($personas) use ($query) {
            $personas->where('primer_nombre', 'like', "%{$query}%")
                ->orWhere('segundo_nombre', 'like', "%{$query}%")
                ->orWhere('primer_apellido', 'like', "%{$query}%")
                ->orWhere('segundo_apellido', 'like', "%{$query}%");
        })->exists();

        if ($hayCoincidencias) {
            return redirect()->route('personas', ['busqueda' => $query]);
        }

        return null;
    }

    private function buscarExamenPorNombre($query)
    {
        $hayCoincidencias = \App\Models\Examen::where(function ($examenes) use ($query) {
            $examenes->where('nombre', 'like', "%{$query}%")
                ->orWhere('nombre_alternativo', 'like', "%{$query}%")
                ->orWhere('descripcion', 'like', "%{$query}%");
        })->exists();

        if ($hayCoincidencias) {
            return redirect()->route('examenes', ['busqueda' => $query]);
        }

        return null;
    }
}

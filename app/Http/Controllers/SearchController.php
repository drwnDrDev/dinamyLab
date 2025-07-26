<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
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
                $result = $this->buscarOrden($query)
                    ?? $this->buscarFactura($query)
                    ?? $this->buscarPersonaPorDocumento($query);
                return $result ?: view('search.busqueda_avanzada', compact('query'))
            ->with('error', 'No results found for the search query');
            } elseif (strlen($query) == 6) {
                $result = $this->buscarPersonaPorDocumento($query)
                    ?? $this->buscarExamenPorCup($query)
                    ?? $this->buscarFactura($query)
                    ?? $this->buscarOrden($query);
                return $result ?: view('search.busqueda_avanzada', compact('query'))
            ->with('error', 'No results found for the search query');
            } else {
                $result = $this->buscarPersonaPorDocumento($query)
                    ?? $this->buscarFactura($query)
                    ?? $this->buscarOrden($query);
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
            ->with('error', __('No results found for the search query'));
    }

    private function buscarOrden($query)
    {
        $orden = \App\Models\Orden::where('numero', $query)->first();
        if ($orden) {
            return redirect()->route('ordenes.show', $orden->id);
        }
        return null;
    }

    private function buscarFactura($query)
    {
        $factura = \App\Models\Factura::find($query);
        if ($factura) {
            return redirect()->route('facturas.show', $factura->id);
        }
        return null;
    }

    private function buscarExamenPorCup($query)
    {
        $examenes = \App\Models\Examen::where('cup', $query)->get();
        if ($examenes && $examenes->count() > 0) {
            return view('examenes.index', compact('examenes'));
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
        $personas = \App\Models\Persona::where('primer_nombre', 'like', '%' . $query . '%')->get();
        if ($personas && $personas->count() > 0) {
            return view('personas.index', compact('personas'));
        }
        $personas = \App\Models\Persona::where('primer_apellido', 'like', '%' . $query . '%')->get();
        if ($personas && $personas->count() > 0) {
            return view('personas.index', compact('personas'));
        }
        $personas = \App\Models\Persona::where('segundo_nombre', 'like', '%' . $query . '%')->get();
        if ($personas && $personas->count() > 0) {
            return view('personas.index', compact('personas'));
        }
        $personas = \App\Models\Persona::where('segundo_apellido', 'like', '%' . $query . '%')->get();
        if ($personas && $personas->count() > 0) {
            return view('personas.index', compact('personas'));
        }

        return null;
    }

    private function buscarExamenPorNombre($query)
    {
        $examenes = \App\Models\Examen::where('nombre', 'like', '%' . $query . '%')->get();
        if ($examenes && $examenes->count() > 0) {
            return view('examenes.index', compact('examenes'));
        }
        $examenes = \App\Models\Examen::where('nombre_alternativo', 'like', '%' . $query . '%')->get();
        if ($examenes && $examenes->count() > 0) {
            return view('examenes.index', compact('examenes'));
        }
        $examenes = \App\Models\Examen::where('descripcion', 'like', '%' . $query . '%')->get();
        if ($examenes && $examenes->count() > 0) {
            return view('examenes.index', compact('examenes'));
        }

        return null;
    }
}

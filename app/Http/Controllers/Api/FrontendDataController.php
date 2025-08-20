<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\TipoDocumento;
use App\Models\Pais;
use App\Models\Municipio;
use App\Models\Eps;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache; // Si usas caché con el Job


class FrontendDataController extends Controller
{
    /**
     * Proporciona los datos necesarios para el frontend (localStorage).
     */
    public function getStaticData(): JsonResponse
    {


        try {

            $cachedData = Cache::get('frontend_static_data');
            if ($cachedData) {
                return response()->json($cachedData);
            }

            $tiposDocumentoPaciente = TipoDocumento::where('es_paciente', true)->orderBy('nivel','desc')->get();
            $tiposDocumentoPagador = TipoDocumento::where('es_pagador', true)->orderBy('nivel','desc')->get();
            $paises = Pais::select('nombre', 'codigo_iso','nivel')->orderBy('nivel', 'desc')->get();
            $municipios = Municipio::select('municipio', 'id','departamento')
                                    ->orderBy('nivel', 'desc')
                                    ->get()
                                    ->map(function($municipio) {
                        return [
                            'codigo' => $municipio->id,
                            'municipio' => mb_strtolower($municipio->municipio, 'UTF-8'),
                            'departamento' => mb_strtolower($municipio->departamento, 'UTF-8'),
                            'nivel' => $municipio->nivel,
                        ];
             });

            $eps = Eps::select('nombre', 'id')
            ->where('habilitada', true) // Solo EPS verificadas
            ->orderBy('nombre')->get();

            $data = [
                'documentos_paciente' => $tiposDocumentoPaciente,
                'documentos_pagador' => $tiposDocumentoPagador,
                'paises' => $paises,
                'municipios' => $municipios,
                'eps' => $eps
            ];

        Cache::put('frontend_static_data', $data, now()->addHours(24)); // Cache por 24 horas

           return response()->json($data);

        } catch (\Exception $e) {

            return response()->json([
                'error' => 'Error al procesar la solicitud de datos.',
                'message' => $e->getMessage() // Solo en desarrollo, no exponer errores detallados en producción
            ], 500);
        }
    }
}

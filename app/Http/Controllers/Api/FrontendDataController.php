<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CausaExterna;
use App\Models\Eps;
use App\Models\Finalidad;
use App\Models\ModalidadAtencion;
use App\Models\Municipio;
use App\Models\Pais;
use App\Models\ServicioHabilitado;
use App\Models\Sexo;
use App\Models\TipoAfiliacion;
use App\Models\TipoDocumento;
use App\Models\ViaIngreso;
use App\Support\CatalogsCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FrontendDataController extends Controller
{
    /**
     * Proporciona los datos necesarios para el frontend (localStorage).
     */
    public function getStaticData(Request $request): JsonResponse|Response
    {
        $payload = CatalogsCache::remember(fn () => $this->buildPayload());
        $etag = md5(json_encode($payload));

        if ($request->header('If-None-Match') === '"' . $etag . '"') {
            return response()
                ->noContent(304)
                ->setEtag($etag)
                ->setCache(['private' => true, 'max_age' => 0, 'must_revalidate' => true])
                ->header('Vary', 'Cookie');
        }

        return response()
            ->json($payload)
            ->setEtag($etag)
            ->setCache(['private' => true, 'max_age' => 0, 'must_revalidate' => true])
            ->header('Vary', 'Cookie');
    }

    private function buildPayload(): array
    {
        $servicosHabilitados = ServicioHabilitado::where('activo', true)
                                ->orderBy('nivel','desc')->get();
        if($servicosHabilitados->isEmpty()) {
            $servicosHabilitados = ServicioHabilitado::orderBy('nivel','desc')
                                ->limit(12)->get();
        }
        $viaIngreso = ViaIngreso::where('activo', true)
                    ->orderBy('nivel','desc')->get();
        $tipoAfiliacion = TipoAfiliacion::where('activo', true)
                                ->orderBy('nivel','desc')->get();
        $causaExterna = CausaExterna::where('activo', true)
                                ->orderBy('nivel','desc')->get();
        $modalidadAtencion = ModalidadAtencion::where('activo', true)
                                ->orderBy('nivel','desc')->get();
        $finaalidad = Finalidad::where('activo', true)
                                ->orderBy('nivel','desc')->get();
        $tiposDocumentoPaciente = TipoDocumento::where('es_paciente', true)
                                ->orderBy('nivel','desc')->get();
        $tiposDocumentoPagador = TipoDocumento::where('es_pagador', true)
                                ->orderBy('nivel','desc')->get();
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
        ->orderBy('nivel','desc')->get();

        $sexos = Sexo::orderBy('nombre')->get(['codigo', 'nombre', 'cod_sexo']);

        return [
            'documentos_paciente' => $tiposDocumentoPaciente,
            'documentos_pagador' => $tiposDocumentoPagador,
            'paises' => $paises,
            'municipios' => $municipios,
            'eps' => $eps,
            'servicios_habilitados' => $servicosHabilitados,
            'via_ingreso' => $viaIngreso,
            'modalidad_atencion' => $modalidadAtencion,
            'tipo_afiliacion' => $tipoAfiliacion,
            'causa_externa' => $causaExterna,
            'finalidad_consulta' => $finaalidad,
            'sexos' => $sexos,
        ];
    }
}

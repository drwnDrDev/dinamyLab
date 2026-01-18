<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PreRegistroCita;
use App\Models\Persona;
use App\Services\ParseadorListaPersonas;
use App\Services\GuardarPersona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PreRegistroCitaController extends Controller
{
    /**
     * PRE-REGISTRO PÚBLICO (Sin autenticación)
     * Permite a usuarios finales pre-registrar citas
     * POST /api/citas/pre-registrar
     */
    public function preRegistrar(Request $request)
    {
        $request->validate([
            'nombres_completos' => 'required|string|max:255',
            'numero_documento' => 'nullable|string|max:50',
            'tipo_documento' => 'nullable|string|max:5',
            'telefono_contacto' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'fecha_deseada' => 'nullable|date',
            'hora_deseada' => 'nullable|date_format:H:i',
            'motivo' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        try {
            // Parsear nombres automáticamente
            $datosParseados = ParseadorListaPersonas::parsear(
                $request->nombres_completos . ',' . ($request->numero_documento ?? ''),
                $request->tipo_documento ?? 'CC'
            );

            $preRegistro = PreRegistroCita::create([
                'nombres_completos' => $request->nombres_completos,
                'numero_documento' => $request->numero_documento,
                'tipo_documento' => $request->tipo_documento ?? 'CC',
                'telefono_contacto' => $request->telefono_contacto,
                'email' => $request->email,
                'fecha_deseada' => $request->fecha_deseada,
                'hora_deseada' => $request->hora_deseada,
                'motivo' => $request->motivo,
                'observaciones' => $request->observaciones,
                'codigo_confirmacion' => PreRegistroCita::generarCodigoConfirmacion(),
                'datos_parseados' => $datosParseados[0] ?? null,
                'estado' => 'pendiente',
            ]);

            return response()->json([
                'message' => 'Pre-registro creado exitosamente',
                'data' => $preRegistro,
                'codigo' => $preRegistro->codigo_confirmacion,
                'instrucciones' => 'Guarda este código para consultar tu cita: ' . $preRegistro->codigo_confirmacion
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear pre-registro',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * PRE-REGISTRAR MÚLTIPLES (Desde lista)
     * POST /api/citas/pre-registrar-lista
     */
    public function preRegistrarLista(Request $request)
    {
        $request->validate([
            'contenido' => 'required|string',
            'tipo_documento' => 'nullable|string|max:5',
            'fecha_deseada' => 'nullable|date',
            'motivo' => 'nullable|string',
        ]);

        try {
            $personasParseadas = ParseadorListaPersonas::parsear(
                $request->contenido,
                $request->tipo_documento ?? 'CC'
            );

            $preRegistros = [];
            foreach ($personasParseadas as $persona) {
                $preRegistro = PreRegistroCita::create([
                    'nombres_completos' => $persona['nombres_completos'],
                    'numero_documento' => $persona['numero_documento'],
                    'tipo_documento' => $persona['tipo_documento'],
                    'fecha_deseada' => $request->fecha_deseada,
                    'motivo' => $request->motivo,
                    'codigo_confirmacion' => PreRegistroCita::generarCodigoConfirmacion(),
                    'datos_parseados' => $persona,
                    'estado' => 'pendiente',
                ]);

                $preRegistros[] = $preRegistro;
            }

            return response()->json([
                'message' => count($preRegistros) . ' pre-registros creados',
                'data' => $preRegistros,
                'codigos' => array_column($preRegistros, 'codigo_confirmacion'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear pre-registros',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * CONSULTAR PRE-REGISTRO (Público)
     * GET /api/citas/consultar/{codigo}
     */
    public function consultar($codigo)
    {
        $preRegistro = PreRegistroCita::where('codigo_confirmacion', $codigo)
            ->orWhere('numero_documento', $codigo)
            ->first();

        if (!$preRegistro) {
            return response()->json([
                'message' => 'Pre-registro no encontrado',
            ], 404);
        }

        return response()->json([
            'message' => 'Pre-registro encontrado',
            'data' => $preRegistro,
        ]);
    }

    /**
     * LISTAR PRE-REGISTROS PENDIENTES (Recepción)
     * Requiere autenticación
     * GET /api/recepcion/pre-registros/pendientes
     */
    public function listarPendientes(Request $request)
    {
        $query = PreRegistroCita::pendientes()
            ->with(['persona', 'confirmadoPor'])
            ->orderBy('fecha_deseada', 'asc')
            ->orderBy('created_at', 'asc');

        // Filtros
        if ($request->has('fecha')) {
            $query->paraFecha($request->fecha);
        }

        if ($request->has('buscar')) {
            $busqueda = $request->buscar;
            $query->where(function($q) use ($busqueda) {
                $q->where('nombres_completos', 'like', "%{$busqueda}%")
                  ->orWhere('numero_documento', 'like', "%{$busqueda}%")
                  ->orWhere('codigo_confirmacion', 'like', "%{$busqueda}%");
            });
        }

        $preRegistros = $query->paginate(20);

        return response()->json($preRegistros);
    }

    /**
     * CONFIRMAR Y COMPLETAR REGISTRO (Recepción)
     * PUT /api/recepcion/pre-registros/{id}/confirmar
     */
    public function confirmarYCompletar(Request $request, $id)
    {
        $preRegistro = PreRegistroCita::findOrFail($id);

        if ($preRegistro->estado === 'confirmado') {
            return response()->json([
                'message' => 'Este pre-registro ya fue confirmado',
            ], 400);
        }

        $request->validate([
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'numero_documento' => 'required|string',
            'tipo_documento' => 'required|string',
            'fecha_nacimiento' => 'required|date',
            'sexo' => 'required|string',
            'telefono' => 'nullable|string',
            'municipio' => 'required|exists:municipios,id',
            'eps' => 'nullable|string',
            'tipo_afiliacion' => 'nullable|string',
            // otros campos necesarios...
        ]);

        try {
            // Crear o actualizar persona formal
            $persona = GuardarPersona::guardar($request->all());

            if (!$persona) {
                throw new \Exception('Error al guardar persona');
            }

            // Actualizar pre-registro
            $preRegistro->persona_id = $persona->id;
            $preRegistro->estado = 'confirmado';
            $preRegistro->fecha_confirmacion = now();
            $preRegistro->confirmado_por = Auth::id();
            $preRegistro->save();

            return response()->json([
                'message' => 'Pre-registro confirmado y persona registrada',
                'data' => [
                    'pre_registro' => $preRegistro->fresh(['persona']),
                    'persona' => $persona,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al confirmar pre-registro',
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * CANCELAR PRE-REGISTRO
     * PUT /api/recepcion/pre-registros/{id}/cancelar
     */
    public function cancelar($id)
    {
        $preRegistro = PreRegistroCita::findOrFail($id);

        $preRegistro->estado = 'cancelado';
        $preRegistro->save();

        return response()->json([
            'message' => 'Pre-registro cancelado',
            'data' => $preRegistro,
        ]);
    }

    /**
     * BUSCAR EN RECEPCIÓN
     * GET /api/recepcion/pre-registros/buscar
     */
    public function buscarEnRecepcion(Request $request)
    {
        $request->validate([
            'busqueda' => 'required|string',
        ]);

        $preRegistros = PreRegistroCita::buscarPorDocumentoOCodigo($request->busqueda);

        return response()->json([
            'message' => count($preRegistros) . ' resultados encontrados',
            'data' => $preRegistros,
        ]);
    }
}

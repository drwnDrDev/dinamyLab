<?php

namespace App\Http\Controllers;

use App\Models\PreRegistroCita;
use App\Models\ModalidadAtencion;
use App\Models\Sede;
use Illuminate\Http\Request;

class PreRegistroCitaController extends Controller
{
    /**
     * Mostrar formulario de registro de cita anónimo (público)
     */
    public function create()
    {
        $sedes = Sede::where('activa', true)->get(['id', 'nombre', 'ciudad']);
        $modalidades = ModalidadAtencion::where('activo', true)->get(['id', 'nombre']);

        return view('citas.registro-anonimo', [
            'sedes' => $sedes,
            'modalidades' => $modalidades,
        ]);
    }

    /**
     * Guardar registro de cita anónima
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombres_completos' => 'required|string|max:255',
            'tipo_documento' => 'required|string|max:50',
            'numero_documento' => 'required|string|max:50',
            'telefono_contacto' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'fecha_deseada' => 'required|date|after_or_equal:today',
            'hora_deseada' => 'required|date_format:H:i',
            'sede_id' => 'nullable|exists:sedes,id',
            'modalidad_id' => 'nullable|exists:modalidades_atencion,id',
            'motivo' => 'nullable|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
        ]);

        // Generar código de confirmación
        $codigo_confirmacion = PreRegistroCita::generarCodigoConfirmacion();

        // Crear pre-registro
        $preRegistro = PreRegistroCita::create([
            'nombres_completos' => $validated['nombres_completos'],
            'tipo_documento' => $validated['tipo_documento'],
            'numero_documento' => $validated['numero_documento'],
            'telefono_contacto' => $validated['telefono_contacto'],
            'email' => $validated['email'],
            'fecha_deseada' => $validated['fecha_deseada'],
            'hora_deseada' => $validated['hora_deseada'],
            'motivo' => $validated['motivo'] ?? null,
            'observaciones' => $validated['observaciones'] ?? null,
            'codigo_confirmacion' => $codigo_confirmacion,
            'estado' => 'pendiente',
            'datos_parseados' => [
                'sede_id' => $validated['sede_id'] ?? null,
                'modalidad_id' => $validated['modalidad_id'] ?? null,
            ],
        ]);

        // TODO: Aquí enviar email de confirmación con el código

        // Si es una petición AJAX, devolver JSON
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Cita registrada correctamente',
                'redirect' => route('citas.confirmacion', ['codigo' => $codigo_confirmacion])
            ]);
        }

        return redirect()->route('citas.confirmacion', ['codigo' => $codigo_confirmacion])
            ->with('success', 'Cita registrada correctamente. Verifica tu email para confirmarla.');
    }

    /**
     * Mostrar página de confirmación anónima
     */
    public function confirmacion($codigo)
    {
        $preRegistro = PreRegistroCita::where('codigo_confirmacion', $codigo)
            ->with('persona', 'orden')
            ->firstOrFail();

        return view('citas.confirmacion', [
            'preRegistro' => $preRegistro,
        ]);
    }

    /**
     * Confirmar cita anónima
     */
    public function confirmar(Request $request, $codigo)
    {
        $preRegistro = PreRegistroCita::where('codigo_confirmacion', $codigo)->firstOrFail();

        $preRegistro->update([
            'estado' => 'confirmada',
            'fecha_confirmacion' => now(),
        ]);

        return redirect()->route('citas.exito')
            ->with('success', 'Cita confirmada correctamente');
    }

    /**
     * Mostrar página de éxito
     */
    public function exito()
    {
        return view('citas.exito');
    }

    /**
     * Listar pre-registros (solo autenticados)
     */
    public function index()
    {
        $this->authorize('view', PreRegistroCita::class);

        $preRegistros = PreRegistroCita::with('persona', 'orden')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('citas.listado', [
            'preRegistros' => $preRegistros,
        ]);
    }

    /**
     * Mostrar detalles de un pre-registro
     */
    public function show(PreRegistroCita $preRegistro)
    {
        $this->authorize('view', $preRegistro);

        $preRegistro->load('persona', 'orden');

        return view('citas.detalle', [
            'preRegistro' => $preRegistro,
        ]);
    }

    /**
     * Actualizar estado de pre-registro
     */
    public function updateEstado(Request $request, PreRegistroCita $preRegistro)
    {
        $this->authorize('update', $preRegistro);

        $validated = $request->validate([
            'estado' => 'required|in:pendiente,confirmada,procesada,cancelada',
        ]);

        $preRegistro->update($validated);

        return back()->with('success', 'Estado actualizado correctamente');
    }

    /**
     * Cancelar pre-registro
     */
    public function cancelar(PreRegistroCita $preRegistro)
    {
        $this->authorize('delete', $preRegistro);

        $preRegistro->update(['estado' => 'cancelada']);

        return back()->with('success', 'Pre-registro cancelado');
    }

    /**
     * Filtrar pre-registros por estado
     */
    public function filtrar(Request $request)
    {
        $this->authorize('view', PreRegistroCita::class);

        $query = PreRegistroCita::query();

        if ($request->has('estado') && $request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->whereDate('fecha_deseada', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->whereDate('fecha_deseada', '<=', $request->fecha_hasta);
        }

        $preRegistros = $query->with('persona', 'orden')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('citas.listado', [
            'preRegistros' => $preRegistros,
            'filtros' => $request->only('estado', 'fecha_desde', 'fecha_hasta'),
        ]);
    }
}

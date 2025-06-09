<?php

namespace App\Http\Controllers;

use App\Models\Orden; // Asumo que tu modelo Orden está en App\Models
use App\Models\Examen; // Asumo que tu modelo Examen está en App\Models
use App\Models\Procedimiento; // Asumo que tu modelo Procedimiento está en App\Models
use App\Models\Persona; // Asumo que tu modelo Persona (para paciente/acompañante) está en App\Models
use App\Estado;
use App\Policies\ExaminationPolicy; // Importa tu Policy
use App\Policies\ProcedurePolicy; // Importa tu Policy si también tienes procedimientos con restricción de género

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException; // Para manejar errores específicos de validación

class OrdenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|exists:personas,id',
            'acompaniante_id' => 'nullable|exists:personas,id',
            'numero_orden' => 'required|string|max:20|unique:ordenes_medicas,numero',
        ]);

        // Carga la instancia del paciente para la autorización
        $paciente = Persona::find($request->input('paciente_id'));

        // Filtra y valida que haya exámenes seleccionados
        $examenesSolicitados = array_filter(
            $request->input('examenes', []),
            function ($cantidad) {
                return !is_null($cantidad) && $cantidad != 0;
            }
        );

        if (empty($examenesSolicitados)) {
            return redirect()->back()->withErrors(['examenes' => 'Debe seleccionar al menos un examen.']);
        }

        // --- VALIDACIÓN DE GÉNERO CON POLICIES ---
        // Carga los detalles completos de los exámenes seleccionados
        $examenesData = Examen::whereIn('id', array_keys($examenesSolicitados))->get()->keyBy('id');

        foreach ($examenesSolicitados as $examenId => $cantidad) {
            $examen = $examenesData->get($examenId);

            // Si el examen no se encontró en la DB (aunque 'exists' debería prevenir esto, es buena práctica)
            if (!$examen) {
                return redirect()->back()->withErrors(['examenes' => "El examen con ID {$examenId} no existe."]);
            }

            // Realiza la autorización para cada examen y paciente
            // Auth::user() es el doctor autenticado en este contexto
            // Passamos el Patient y el Examination
            try {
                Auth::user()->can(ExaminationPolicy::class . ':performOnPatient', [$paciente, $examen]);
                // Si la autorización falla, lanzará una AuthorizationException
            } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
                // Captura la excepción y devuelve un error con el mensaje de la Policy
                return redirect()->back()->withErrors([
                    'examenes' => 'No se puede solicitar el examen "' . $examen->nombre . '" para el paciente debido a restricciones de género: ' . $e->getMessage()
                ])->withInput(); // withInput() para mantener los datos del formulario
            }
        }
        // --- FIN VALIDACIÓN DE GÉNERO CON POLICIES ---

        // Calcular el total
        $total = $examenesData->sum(function ($examen) use ($examenesSolicitados) {
            return $examen->valor * $examenesSolicitados[$examen->id];
        });

        $abono = $request->input('pago') === "on" ? $total : $request->input('abono', 0);

        // Crear la orden médica
        $orden = Orden::create([
            'numero' => $request->input('numero_orden'),
            'paciente_id' => $request->input('paciente_id'),
            'acompaniante_id' => $request->input('acompaniante_id'),
            'descripcion' => $request->input('observaciones'),
            'abono' => $abono,
            'total' => $total,
        ]);

        // Preparar datos para la tabla pivote o relación de exámenes con la orden
        // En tu código actual, esta parte no se usa para guardar una relación directa
        // pero es una buena práctica si tienes una tabla intermedia orden_examen
        $orden_examen = array_map(function ($examenId) use ($orden, $examenesSolicitados) {
            return [
                'orden_id' => $orden->id,
                'examen_id' => $examenId,
                'cantidad' => $examenesSolicitados[$examenId],
            ];
        }, array_keys($examenesSolicitados));

        // Asignar los procedimientos a la orden
        $procedimientos = [];
        foreach ($orden_examen as $examenItem) { // Cambiado el nombre de la variable para evitar conflicto
            for ($i = 0; $i < $examenItem['cantidad']; $i++) {
                $procedimientos[] = [
                    'orden_id' => $orden->id, // Usa $orden->id aquí, no $examen['orden_id']
                    'empleado_id' => Auth::user()->id,
                    'examen_id' => $examenItem['examen_id'],
                    'fecha' => now(),
                    'estado' => Estado::PROCESO,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        Procedimiento::insert($procedimientos);

        return redirect()->route('ordenes')->with('success', 'Orden médica creada correctamente');
    }
}



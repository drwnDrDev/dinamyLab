// Supongamos que tienes una Orden con sus relaciones cargadas
$orden = OrdenMedica::with('procedimientos.resultado')->find($ordenId);

$resultado = $orden->procedimientos->map(function ($proc) {
    return [
        'examen' => $proc->examen->nombre ?? 'Desconocido',
        'fecha_orden' => $proc->orden->created_at,
        'fecha_procedimiento' => $proc->fecha,
        'fecha_resultado' => optional($proc->resultado)->fecha_resultado,
        'dias_orden_a_procedimiento' => $proc->created_at->diffInDays($proc->orden->created_at),
        'dias_procedimiento_a_resultado' => optional($proc->resultado)?->fecha_resultado
            ? $proc->fecha->diffInDays($proc->resultado->fecha_resultado)
            : null,
        'dias_orden_a_resultado' => optional($proc->resultado)?->fecha_resultado
            ? $proc->orden->created_at->diffInDays($proc->resultado->fecha_resultado)
            : null,
    ];
});

return $resultado;

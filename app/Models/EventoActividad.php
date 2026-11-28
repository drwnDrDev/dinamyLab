<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventoActividad extends Model
{
    protected $table = 'eventos_actividad';

    public $timestamps = false;

    protected $guarded = ['id'];

    public const EVENTOS = [
        'sesion.iniciada' => 'Inicio de sesión',
        'sesion.cerrada' => 'Cierre de sesión',
        'usuario.registrado' => 'Registro de usuario',
        'paciente.creado' => 'Creación de paciente',
        'paciente.actualizado' => 'Actualización de paciente',
        'paciente.historia_consultada' => 'Acceso a ficha / historia clínica del paciente',
        'rips.reporte_consultado' => 'Consulta del reporte RIPS',
        'rips.descargados' => 'Generación y descarga de RIPS',
        'orden.creada' => 'Creación de orden',
        'resultado.creado' => 'Creación de resultado',
        'resultado.actualizado' => 'Actualización de resultados',
        'resultados.descargados' => 'Descarga de resultados (PDF)',
        'resultados.impresion_solicitada' => 'Solicitud de impresión de resultados',
        'resultados.lote_procesado' => 'Procesamiento por lotes',
        'procedimiento.anulacion_solicitada' => 'Solicitud de anulación de examen',
        'procedimiento.anulacion_aprobada' => 'Aprobación de anulación de examen',
        'procedimiento.anulacion_rechazada' => 'Rechazo de anulación de examen',
    ];

    protected function casts(): array
    {
        return ['detalles' => 'array'];
    }
}

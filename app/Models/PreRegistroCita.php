<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PreRegistroCita extends Model
{
    use SoftDeletes;

    protected $table = 'pre_registros_citas';

    protected $fillable = [
        'nombres_completos',
        'numero_documento',
        'tipo_documento',
        'telefono_contacto',
        'email',
        'fecha_deseada',
        'hora_deseada',
        'motivo',
        'observaciones',
        'estado',
        'persona_id',
        'orden_id',
        'codigo_confirmacion',
        'fecha_confirmacion',
        'confirmado_por',
        'datos_parseados',
    ];

    protected $casts = [
        'fecha_deseada' => 'date',
        'hora_deseada' => 'datetime',
        'fecha_confirmacion' => 'datetime',
        'datos_parseados' => 'array',
    ];

    /**
     * Generar código de confirmación único
     */
    public static function generarCodigoConfirmacion(): string
    {
        do {
            $codigo = strtoupper(Str::random(8));
        } while (self::where('codigo_confirmacion', $codigo)->exists());

        return $codigo;
    }

    /**
     * Relación con Persona (cuando se completa el registro)
     */
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    /**
     * Relación con Orden (si se crea orden)
     */
    public function orden()
    {
        return $this->belongsTo(Orden::class, 'orden_id', 'id');
    }

    /**
     * Usuario que confirmó el pre-registro
     */
    public function confirmadoPor()
    {
        return $this->belongsTo(\App\Models\User::class, 'confirmado_por');
    }

    /**
     * Scopes
     */
    public function scopePendientes($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeConfirmados($query)
    {
        return $query->where('estado', 'confirmado');
    }

    public function scopeParaFecha($query, $fecha)
    {
        return $query->whereDate('fecha_deseada', $fecha);
    }

    /**
     * Buscar por documento o código
     */
    public static function buscarPorDocumentoOCodigo(string $busqueda)
    {
        return self::where('numero_documento', $busqueda)
            ->orWhere('codigo_confirmacion', $busqueda)
            ->get();
    }

    /**
     * Confirmar y crear persona formal
     */
    public function confirmar(array $datosCompletos, $usuarioId)
    {
        // Aquí se crearía o actualizaría la persona formal
        // y se marcaría el pre-registro como confirmado

        $this->estado = 'confirmado';
        $this->fecha_confirmacion = now();
        $this->confirmado_por = $usuarioId;
        $this->save();

        return $this;
    }
}

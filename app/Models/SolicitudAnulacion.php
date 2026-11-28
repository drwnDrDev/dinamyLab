<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SolicitudAnulacion extends Model
{
    protected $table = 'solicitudes_anulacion';

    protected $fillable = [
        'procedimiento_id', 'sede_id', 'solicitante_id', 'motivo', 'estado',
        'resuelta_por_id', 'respuesta', 'resuelta_at',
    ];

    protected function casts(): array
    {
        return ['resuelta_at' => 'datetime'];
    }

    public function procedimiento(): BelongsTo
    {
        return $this->belongsTo(Procedimiento::class)->withTrashed();
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function resueltaPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resuelta_por_id');
    }

    public function sede(): BelongsTo
    {
        return $this->belongsTo(Sede::class);
    }
}

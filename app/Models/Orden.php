<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Estado;

class Orden extends Model
{
    use HasFactory, SoftDeletes;
   // ...
    protected $fillable = [
        'sede_id',
        'numero',
        'paciente_id',
        'observaciones',
        'terminada',
        'abono',
        'user_id',
        'total',
    ];

    public function casts()
    {
        return [
            'abono' => 'decimal:2',
            'total' => 'decimal:2',
            'terminada' => 'datetime',
        ];
    }
    public function paciente()
    {
        return $this->belongsTo(Persona::class, 'paciente_id');
    }

    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
    public function procedimientos()
    {
        return $this->hasMany(Procedimiento::class);
    }

    public function sincronizarEstado(): void
    {
        $estadosFinales = [
            Estado::TERMINADO->value,
            Estado::ENTREGADO->value,
        ];

        $pendientes = $this->procedimientos()->where(function ($query) use ($estadosFinales) {
            $query->whereNotIn('estado', $estadosFinales)->orWhereNull('estado');
        });
        $remisionesPendientes = $this->remisiones()->where(function ($query) use ($estadosFinales) {
            $query->whereNotIn('estado', $estadosFinales)->orWhereNull('estado');
        });
        $completada = ($this->procedimientos()->exists() || $this->remisiones()->exists())
            && ! $pendientes->exists() && ! $remisionesPendientes->exists();

        if ($completada && $this->terminada === null) {
            $this->forceFill(['terminada' => now()])->saveQuietly();
        } elseif (! $completada && $this->terminada !== null) {
            $this->forceFill(['terminada' => null])->saveQuietly();
        }
    }
    /**
     * Indica si finalizar esta remisión completaría la orden, es decir, si todo lo demás
     * (procedimientos y otras remisiones) ya está finalizado. Misma regla que sincronizarEstado().
     */
    public function remisionEsLoUltimoPendiente(OrdenRemision $remision): bool
    {
        $finales = [Estado::TERMINADO->value, Estado::ENTREGADO->value];
        $esFinal = fn ($estado) => in_array($estado instanceof Estado ? $estado->value : $estado, $finales, true);

        if ($esFinal($remision->estado)) {
            return false;
        }

        return $this->procedimientos->every(fn ($procedimiento) => $esFinal($procedimiento->estado))
            && $this->remisiones
                ->reject(fn ($otra) => $otra->is($remision))
                ->every(fn ($otra) => $esFinal($otra->estado));
    }

    public function remisiones()
    {
        return $this->hasMany(OrdenRemision::class);
    }
    public function examenes()
    {
        return $this->belongsToMany(Examen::class, 'orden_examen', 'orden_medica_id', 'examen_id')
            ->withPivot('cantidad');

    }
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function observaciones()
    {
        return $this->morphMany(Observacion::class, 'observable');
    }

    protected $table = 'ordenes_medicas';
}

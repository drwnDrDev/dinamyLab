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
            Estado::ANULADO->value,
        ];

        $pendientes = $this->procedimientos()->where(function ($query) use ($estadosFinales) {
            $query->whereNotIn('estado', $estadosFinales)->orWhereNull('estado');
        });
        $completada = $this->procedimientos()->exists() && ! $pendientes->exists();

        if ($completada && $this->terminada === null) {
            $this->forceFill(['terminada' => now()])->saveQuietly();
        } elseif (! $completada && $this->terminada !== null) {
            $this->forceFill(['terminada' => null])->saveQuietly();
        }
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenRemision extends Model
{
    protected $table = 'orden_remisiones';

    protected $fillable = [
        'orden_id',
        'estado',
        'cup',
        'nombre',
        'valor',
        'destino_sede_id',
        'destino',
    ];

    protected $attributes = ['estado' => 'pendiente de muestra'];

    protected static function booted(): void
    {
        static::saved(function (self $remision) {
            Orden::find($remision->orden_id)?->sincronizarEstado();
            if ($remision->wasChanged('orden_id')) {
                Orden::find($remision->getOriginal('orden_id'))?->sincronizarEstado();
            }
        });
        static::deleted(fn (self $remision) => Orden::find($remision->orden_id)?->sincronizarEstado());
    }

    protected function casts(): array
    {
        return [
            'estado' => \App\Estado::class,
            'valor' => 'decimal:2',
        ];
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function destinoSede()
    {
        return $this->belongsTo(Sede::class, 'destino_sede_id');
    }
}

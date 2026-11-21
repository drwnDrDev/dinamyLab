<?php

namespace App\Models;

use App\Estado;
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
        'valor_remision_id',
        'ips_externa_id',
        'valor_remitido',
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
            'estado' => Estado::class,
            'valor' => 'decimal:2',
            'valor_remitido' => 'decimal:2',
        ];
    }

    public function orden()
    {
        return $this->belongsTo(Orden::class);
    }

    public function ipsExterna()
    {
        return $this->belongsTo(IpsExterna::class);
    }

    public function valorRemision()
    {
        return $this->belongsTo(ValorRemision::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenRemision extends Model
{
    protected $table = 'orden_remisiones';

    protected $fillable = [
        'cup',
        'nombre',
        'valor',
        'destino_sede_id',
        'destino',
    ];

    protected function casts(): array
    {
        return [
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\IncrementaNivel;

class Eps extends Model
{
    use IncrementaNivel;
    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'nivel',
        'verificada',
    ];

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('nombre', 'like', '%' . $search . '%')
                ->orWhere('codigo', 'like', '%' . $search . '%');
        });
    }
    protected static function booted()
    {
        self::incrementarNivel('codigo', 6);
        self::resetearNiveles(4);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Eps extends Model
{
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empresa extends Model
{
    protected $fillable = [
        'razon_social',
        'nit',
        'contacto_id',
    ];

    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('razon_social', 'like', '%' . $search . '%')
                ->orWhere('nit', 'like', '%' . $search . '%');
        });
    }
}

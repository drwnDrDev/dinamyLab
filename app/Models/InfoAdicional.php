<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoAdicional extends Model
{
    protected $fillable = [
        'contacto_id',
        'tipo',
        'valor',
        'liga',
        'descripcion',
    ];

    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('tipo', 'like', '%' . $search . '%')
                ->orWhere('valor', 'like', '%' . $search . '%');
        });
    }
}

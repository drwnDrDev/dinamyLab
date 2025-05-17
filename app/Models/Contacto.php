<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $fillable = [
        'municipio_id',
        'pais_id',
        'telefono',
        'info_adicional',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }
    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }
    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('telefono', 'like', '%' . $search . '%')
                ->orWhere('redes', 'like', '%' . $search . '%');
        });

    }
    protected $casts = [
        'info_adicional' => 'array',
    ];
    
}

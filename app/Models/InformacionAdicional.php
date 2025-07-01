<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InformacionAdicional extends Model
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
        return $this->hasOne(Contacto::class);
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('tipo', 'like', '%' . $search . '%')
                ->orWhere('valor', 'like', '%' . $search . '%');
        });
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    protected $table = 'informacion_adicional';
    
}

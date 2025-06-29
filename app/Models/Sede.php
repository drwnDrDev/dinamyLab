<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nombre',
        'res_facturacion',
        'incio_facturacion',
        'fin_facturacion',
        'empresa_id',
        'contacto_id'
    ];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }
    // App\Models\Sede.php

    protected static function booted()
    {
        static::deleting(function ($sede) {
            if ($sede->contacto) {
                $sede->contacto->delete();
            }
        });
    }
    public function resoluciones()
    {
        return $this->morphMany(Resolucion::class, 'uso');
    }

    public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('nombre', 'like', '%' . $search . '%')
                ->orWhere('codigo_prestador', 'like', '%' . $search . '%');
        });
    }
}

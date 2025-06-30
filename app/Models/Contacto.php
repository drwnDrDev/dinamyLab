<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contacto extends Model
{
    protected $fillable = [
        'municipio_id',
        'telefono',
    ];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function informacionAdicional()
    {
        return $this->hasMany(InfoAdicional::class);
    }

    public function infoAdicional($tipo = null)
    {
        return $this->hasMany(InfoAdicional::class)
            ->when($tipo, function ($query) use ($tipo) {
                $query->where('tipo', $tipo);
            })
            ->orderBy('created_at', 'desc');
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $fillable = [
        'precio',
        'tarifable_id',
        'tarifable_type',
        'sede_id'
    ];

    public function tarifable()
    {
        return $this->morphTo();
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class);
    }

    /**
     * Relación con convenios que tienen acceso a esta tarifa.
     */
    public function convenios()
    {
        return $this->belongsToMany(Convenio::class, 'convenio_tarifa');
    }

    /**
     * Verifica si esta tarifa está vinculada a un convenio específico.
     * 
     * @param Convenio $convenio
     * @return bool
     */
    public function estaVinculadaAConvenio(Convenio $convenio): bool
    {
        return $this->convenios()->where('convenios.id', $convenio->id)->exists();
    }
}

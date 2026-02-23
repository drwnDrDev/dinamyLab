<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tarifa extends Model
{
    protected $fillable = [
        'precio',
        'tarifable_id',
        'tarifable_type',
    ];

    public function tarifable()
    {
        return $this->morphTo();
    }

    public function sede()
    {
        return $this->belongsTo(Sede::class,'sede_tarifa','tarifa_id','sede_id');
    }

    public function examen()
    {
        return $this->morphsTo(Examen::class, 'tarifable');
    }
    public function servicio()
    {
        return $this->morphsTo(Servicio::class, 'tarifable');
    }
    public function producto()
    {
        return $this->morphsTo(Producto::class, 'tarifable');
    }

    /**
     * Relación con convenios que tienen acceso a esta tarifa.
     */
    public function convenios()
    {
        return $this->belongsToMany(Convenio::class, 'convenio_tarifa', 'tarifa_id', 'convenio_id');
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

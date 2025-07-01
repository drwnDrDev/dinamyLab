<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaisProcedencia extends Model
{


    protected $fillable = [
        'pais_codigo_iso', // Código ISO del país
        'procedencia_id', // ID de la entidad que usa esta procedencia
        'procedencia_type' // Tipo de entidad que usa esta procedencia (polimórfico)
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paises_procedencia';

    /**
     * Relación polimórfica con el modelo que posee la procedencia.
     */
    public function procedencia()
    {
        return $this->morphTo();
    }

    public function pais()
    {
        return $this->belongsTo(Pais::class, 'pais_codigo_iso', 'codigo_iso');
    }
}

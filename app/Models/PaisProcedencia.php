<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaisProcedencia extends Model
{
    protected $fillable = [
        'pais_id',
        'procedencia',
        'descripcion',
        'codigo_iso',
        'codigo_telefono',
        'nivel',
    ];

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'paises_procedencia';

    

    public function pais()
    {
        return $this->belongsTo(Pais::class);
    }
}

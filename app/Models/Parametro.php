<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Parametro extends Model
{
            //     $table->string('nombre');
            // $table->string('grupo')->nullable();
            // $table->string('slug');
            // $table->string('tipo_dato');
            // $table->string('por_defecto')->nullable();
            // $table->string('unidades')->nullable();
            // $table->string('metodo')->nullable();
            // $table->unsignedTinyInteger('orden');

    protected $fillable = [
        'nombre',
        'grupo',
        'tipo_dato',
        'slug',
        'por_defecto',
        'unidades',
        'metodo',
        'orden'
    ];

    public function examenes()
    {
        return $this->belongsToMany(Examen::class, 'examen_parametro','examen_id', 'parametro_id')
            ->withTimestamps();
    }

        /**
     * Get the valor_referencias for the Parametro.
     */
    public function valoresReferencia()
    {
        return $this->hasMany(ValorReferencia::class);
    }

        public function opciones()
    {
        return $this->hasMany(Opcion::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValorReferencia extends Model
{
            // $table->foreignId('parametro_id')->constrained()->onDelete('cascade');
            // $table->strig('demografia');
            // $table->string('salida');
            // $table->decimal('min', 10, 4)->nullable();
            // $table->decimal('max', 10, 4)->nullable();

    protected $fillable = [
        'parametro_id',
        'demografia',
        'salida',
        'min',
        'max',
        'optimo'
    ];

     /**
     * Get the parametro that owns the Opcion.
     */
    public function parametro()
    {
        return $this->belongsTo(Parametro::class);
    }

}

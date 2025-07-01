<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resolucion extends Model
{

        //     Schema::create('resoluciones', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('prefijo', 10)->nullable();
        //     $table->string('res_facturacion')->nullable();
        //     $table->bigInteger('incio_facturacion')->nullable();
        //     $table->bigInteger('fin_facturacion')->nullable();
        //     $table->date('fecha_inicio')->nullable();
        //     $table->date('fecha_fin')->nullable();
        //     $table->boolean('activa')->default(true);
        //     $table->morphs('uso'); // This will create `uso_type` and `uso_id` columns for polymorphic relations
        //     $table->timestamps();
        // });


    protected $fillable = [
        'prefijo',
        'res_facturacion',
        'incio_facturacion',
        'fin_facturacion',
        'fecha_inicio',
        'fecha_fin',
        'activa',
        'uso_type',
        'uso_id'
    ];
    protected function uso()
    {
        return $this->morphTo();
    }
    

    protected function cast()
    {
        return [
            'incio_facturacion' => 'integer',
            'fin_facturacion' => 'integer',
            'fecha_inicio' => 'date',
            'fecha_fin' => 'date',
            'activa' => 'boolean',
        ];
    }

    protected $table = 'resoluciones';
}

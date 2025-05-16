<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
            //     $table->string('nombre')->unique();
            // $table->string('res_facturacion')->nullable();
            // $table->bigInteger('incio_facturacion')->nullable();
            // $table->bigInteger('fin_facturacion')->nullable();
            // $table->foreignId('empresa_id')
            //     ->constrained('empresas')
            //     ->cascadeOnDelete()
            //     ->cascadeOnUpdate();
            // $table->foreignId('contacto_id')
            //     ->constrained('contactos')
            //     ->nullOnDelete()
            //     ->cascadeOnUpdate();
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

}

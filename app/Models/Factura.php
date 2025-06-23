<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factura extends Model
{
    
            // $table->unsignedBigInteger('numero')->unique();
            // $table->string('cufe', 64)->nullable();
            // $table->date('fecha_emision');
            // $table->date('fecha_vencimiento')->nullable();
            // $table->morphs('pagador');
            // $table->decimal('subtotal', 12, 2);
            // $table->decimal('total_ajustes', 12, 2)->default(0);
            // $table->foreignId('impuestos_id')->nullable()
            //     ->constrained('impuestos')
            //     ->nullOnDelete();
            // $table->foreignId('empresa_id')->nullable()
            //     ->constrained('empresas')
            //     ->nullOnDelete();
            // $table->decimal('total', 12, 2);
            // $table->string('observaciones')->nullable();

    protected $fillable = [
        'numero',
        'cufe',
        'fecha_emision',
        'fecha_vencimiento',
        'empresa_id',
        'impuestos_id',
        'pagador_type',
        'pagador_id',
        'paciente_id',
        'subtotal',
        'total_ajustes',
        'impuestos',
        'total',
        'observaciones'
    ];
    protected $casts = [
        'impuestos' => 'array',
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'total_ajustes' => 'decimal:2',
        'total' => 'decimal:2',
    ];
    public function pagador()
    {
        return $this->morphTo();
    }
}

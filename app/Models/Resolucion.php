<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resolucion extends Model
{
            //     $table->string('codigo', 120)->unique();
            // $table->string('prefijo', 10)->nullable();
            // $table->string('res_facturacion')->nullable();
            // $table->bigInteger('incio_facturacion')->nullable();
            // $table->bigInteger('fin_facturacion')->nullable();
            // $table->date('fecha_inicio')->nullable();
            // $table->date('fecha_fin')->nullable();
            // $table->boolean('activa')->default(true);
            // $table->foreignId('empresa_id')
            //         ->constrained('empresas')
            //         ->cascadeOnDelete()
            //         ->cascadeOnUpdate();

    protected $fillable = [
        'codigo',
        'prefijo',
        'res_facturacion',
        'incio_facturacion',
        'fin_facturacion',
        'fecha_inicio',
        'fecha_fin',
        'activa',
        'empresa_id'
    ];
    protected $casts = [
        'incio_facturacion' => 'integer',
        'fin_facturacion' => 'integer',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activa' => 'boolean'
    ];
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
    public function getFullCodeAttribute()
    {
        return $this->prefijo ? "{$this->prefijo}-{$this->codigo}" : $this->codigo;
    }
    public function getFullRangeAttribute()
    {
        return $this->incio_facturacion && $this->fin_facturacion
            ? "{$this->incio_facturacion} - {$this->fin_facturacion}"
            : null;
    }
    protected $table = 'resoluciones';
}

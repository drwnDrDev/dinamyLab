<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Orden extends Model
{
    //
            // $table->unsignedMediumInteger('numero');
            // $table->foreignId('paciente_id')->constrained('personas')->onDelete('cascade');
            // $table->foreignId('acomanhante_id')->constrained('personas')->onDelete('cascade');
            // $table->string('descripcion')->nullable();
            // $table->decimal('abono', 10, 2)->nullable();
            // $table->string('estado')->default('pendiente');

    protected $fillable = [
        'numero',
        'paciente_id',
        'acompaniante_id',
        'descripcion',
        'abono',
        'estado'
    ];
    protected $casts = [
        'numero' => 'integer',
        'abono' => 'decimal:2',
    ];
    public function paciente()
    {
        return $this->belongsTo(Persona::class, 'paciente_id');
    }
    public function acompaniante()
    {
        return $this->belongsTo(Persona::class, 'acompaniante_id');
    }
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }
    public function procedimientos()
    {
        return $this->hasMany(Procedimiento::class);
    }

    protected $table = 'ordenes_medicas';
}

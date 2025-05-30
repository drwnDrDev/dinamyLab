<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    //  $table->string('priemr_nombre');
    //         $table->string('segundo_nombre')->nullable();
    //         $table->string('primer_apellido');
    //         $table->string('segundo_apellido')->nullable();
    //         $table->enum('tipo_documento',['CC','TI','CE','RC','PA','AS','MS','CD','PE','PT'])->default('CC');
    //         $table->string('numero_documento')->unique();
    //         $table->date('fecha_nacimiento')->nullable();
    //         $table->enum('sexo',['F','M'])->nullable();
    //         $table->boolean('nacional')->default(true);
    //         $table->string('telefono')->nullable();
    //         $table->foreignId('contacto_id')->nullable()
    //         ->constrained('contactos')
    //         ->nullOnDelete()
    //         ->cascadeOnUpdate();

    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'sexo',
        'nacional',
        'telefono',
        'contacto_id'
    ];
    protected $casts = [
        'fecha_nacimiento' => 'date',
        'nacional' => 'boolean',
        'sexo' => 'string',
    ];
    public function ordenes()
    {
        return $this->hasMany(Orden::class, 'paciente_id');
    }
    public function contacto()
    {
        return $this->belongsTo(Contacto::class);
    }

    public function paciente()
    {
        return $this->belongsTo(Orden::class, 'paciente_id');
    }

    public function acompaniante()
    {
        return $this->belongsTo(Orden::class, 'acompaniante_id');
    }
    public function nombres()
    {
        return $this->primer_nombre . ' ' . $this->segundo_nombre;
    }
    public function apellidos()
    {
        return $this->primer_apellido . ' ' . $this->segundo_apellido;
    }
    public function nombreCompleto()
    {
        return $this->nombres() . ' ' . $this->apellidos();
    }
    public function edad()
    {
        return $this->fecha_nacimiento ? $this->fecha_nacimiento->diffInYears(now()) : null;
    }

    

    // Relación isomórfica para la columna 'pagador' en la tabla 'factura'
    public function facturasComoPagador()
    {
        return $this->morphMany(Factura::class, 'pagador');
    }
}

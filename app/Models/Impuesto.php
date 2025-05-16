<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Impuesto extends Model
{
        //    $table->string('nombre')->unique();
        //     $table->string('descripcion')->nullable();
        //     $table->string('codigo')->unique();
        //     $table->decimal('tasa', 5, 2);
    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo',
        'tasa'
    ];
    protected $casts = [
        'tasa' => 'decimal:2',
    ];
}

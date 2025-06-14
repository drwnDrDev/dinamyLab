<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
//Nobre	Grupo	order	Parametro	Tipo_dato	UniMedida


        Schema::create('parametros', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('grupo')->nullable();
            $table->string('slug');
            $table->string('tipo_dato');
            $table->string('default')->nullable();
            $table->string('unidades')->nullable();
            $table->string('metodo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parametros');
    }
};

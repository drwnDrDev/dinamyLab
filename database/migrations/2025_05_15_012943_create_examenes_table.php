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
        Schema::create('examenes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('cup',10);
            $table->foreign('cup')
                ->references('codigo')
                ->on('codigo_cups');
            $table->string('sexo_aplicable',1)->default('A'); // M, F, A
            $table->string('nombre_alternativo')->nullable();
            $table->decimal('valor', 10, 2);
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('nivel')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('examenes');
    }
};

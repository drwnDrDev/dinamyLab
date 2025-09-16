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
        Schema::create('orden_examen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('orden_medica_id')
                    ->constrained('ordenes_medicas')
                    ->onDelete('cascade');
            $table->foreignId('examen_id')
                    ->constrained('examenes')
                    ->onDelete('cascade');
            $table->string('diagnostico_principal',10);
            $table->string('diagnostico_relacionado',10)->nullable();
            $table->foreign('diagnostico_principal')
                    ->references('codigo')
                    ->on('codigo_diagnosticos')
                    ->onDelete('cascade');
            $table->foreign('diagnostico_relacionado')
                    ->references('codigo')
                    ->on('codigo_diagnosticos')
                    ->onDelete('cascade');
            $table->string('codigo_finalidad');
            $table->foreign('codigo_finalidad')
                ->references('codigo')
                ->on('finalidades')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('cantidad')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orden_examen');
    }
};

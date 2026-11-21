<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('procedimientos', function (Blueprint $table) {
            $table->timestamp('rips_procesado_at')->nullable();
            $table->timestamp('resultados_pdf_descargado_at')->nullable();
        });

        Schema::table('ordenes_medicas', function (Blueprint $table) {
            $table->foreignId('orden_origen_id')
                ->nullable()
                ->unique()
                ->constrained('ordenes_medicas')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('ordenes_medicas', function (Blueprint $table) {
            $table->dropForeign(['orden_origen_id']);
            $table->dropUnique(['orden_origen_id']);
            $table->dropColumn('orden_origen_id');
        });

        Schema::table('procedimientos', function (Blueprint $table) {
            $table->dropColumn(['rips_procesado_at', 'resultados_pdf_descargado_at']);
        });
    }
};

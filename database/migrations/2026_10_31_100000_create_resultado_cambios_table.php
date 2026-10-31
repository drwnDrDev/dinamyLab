<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resultado_cambios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('procedimiento_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('resultado_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parametro_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('usuario_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('parametro_nombre');
            $table->text('valor_anterior')->nullable();
            $table->text('valor_nuevo')->nullable();
            $table->text('observacion');
            $table->string('usuario_nombre');
            $table->timestamps();

            $table->index(['procedimiento_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resultado_cambios');
    }
};

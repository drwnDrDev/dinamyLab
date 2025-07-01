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
        Schema::create('empleados', function (Blueprint $table) {
            $table->id();
            $table->string('firma')->nullable();
            $table->string('cargo',120)->default(\App\Cargo::PRESTADOR);
            $table->string('tipo_documento', 2)->default('CC');
            $table->string('numero_documento')->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->foreignId('empresa_id')
                ->constrained('empresas')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empleados');
    }
};

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
        Schema::create('valor_referencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parametro_id')->constrained()->onDelete('cascade');
            $table->string('demografia');
            $table->string('salida');
            $table->decimal('min', 10, 4)->nullable();
            $table->decimal('max', 10, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valor_referencias');
    }
};

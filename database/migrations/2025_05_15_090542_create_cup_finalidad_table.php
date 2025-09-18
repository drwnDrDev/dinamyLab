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
        Schema::create('cup_finalidad', function (Blueprint $table) {
            $table->id();
            $table->string('cup', 10);
            $table->foreign('cup')
                ->references('codigo')
                ->on('codigo_cups')
                ->onDelete('cascade');
            $table->string('finalidad');
            $table->foreign('finalidad')
                ->references('codigo')
                ->on('finalidades')
                ->onDelete('cascade');
            $table->unsignedSmallInteger('nivel')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cup_finalidad');
    }
};

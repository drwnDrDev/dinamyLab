<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Inicio;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contactos', function (Blueprint $table) {
            $table->id();
            $table->string('telefono', 20)->nullable();
            $table->unsignedBigInteger('municipio_id')->nullable()->default(Inicio::getMunicipioId());
            $table->foreign('municipio_id')
            ->references('id')
            ->on('municipios')
            ->onUpdate('cascade')
            ->onDelete('set null');
            $table->timestamps();
        });

    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contactos');
    }
};

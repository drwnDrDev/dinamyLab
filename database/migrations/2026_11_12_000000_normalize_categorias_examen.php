<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });

        $categorias = DB::table('categoria_examen')->orderBy('id')->get();

        foreach ($categorias as $categoria) {
            DB::table('categorias')->insert([
                'id' => $categoria->id,
                'nombre' => $categoria->nombre,
                'descripcion' => $categoria->descripcion,
                'created_at' => $categoria->created_at,
                'updated_at' => $categoria->updated_at,
            ]);
        }

        Schema::create('categoria_examen_pivot', function (Blueprint $table) {
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('examen_id')->constrained('examenes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->primary(['categoria_id', 'examen_id']);
        });

        foreach ($categorias as $categoria) {
            if (DB::table('examenes')->where('id', $categoria->examen_id)->exists()) {
                DB::table('categoria_examen_pivot')->insert([
                    'categoria_id' => $categoria->id,
                    'examen_id' => $categoria->examen_id,
                    'created_at' => $categoria->created_at,
                    'updated_at' => $categoria->updated_at,
                ]);
            }
        }

        Schema::drop('categoria_examen');
        Schema::rename('categoria_examen_pivot', 'categoria_examen');
    }

    /**
     * Reversión con pérdida de información: el esquema legacy solo admite un
     * examen por categoría, así que se conserva el primero y se descartan las
     * demás asociaciones muchos-a-muchos.
     */
    public function down(): void
    {
        Schema::create('categoria_examen_legacy', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->foreignId('examen_id')->constrained('examenes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        foreach (DB::table('categorias')->orderBy('id')->get() as $categoria) {
            $examenId = DB::table('categoria_examen')
                ->where('categoria_id', $categoria->id)
                ->orderBy('created_at')
                ->orderBy('examen_id')
                ->value('examen_id');

            if ($examenId) {
                DB::table('categoria_examen_legacy')->insert([
                    'id' => $categoria->id,
                    'nombre' => $categoria->nombre,
                    'descripcion' => $categoria->descripcion,
                    'examen_id' => $examenId,
                    'created_at' => $categoria->created_at,
                    'updated_at' => $categoria->updated_at,
                ]);
            }
        }

        Schema::drop('categoria_examen');
        Schema::rename('categoria_examen_legacy', 'categoria_examen');
        Schema::drop('categorias');
    }
};

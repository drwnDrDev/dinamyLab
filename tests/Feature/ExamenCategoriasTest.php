<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Examen;
use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ExamenCategoriasTest extends TestCase
{
    use RefreshDatabase;

    public function test_las_categorias_usan_un_pivote_muchos_a_muchos(): void
    {
        $this->assertTrue(Schema::hasTable('categorias'));
        $this->assertFalse(Schema::hasColumn('categorias', 'examen_id'));
        $this->assertTrue(Schema::hasTable('categoria_examen'));
        $this->assertTrue(Schema::hasColumn('categoria_examen', 'categoria_id'));
        $this->assertTrue(Schema::hasColumn('categoria_examen', 'examen_id'));
    }

    public function test_un_examen_puede_pertenecer_a_varias_categorias(): void
    {
        $examen = Examen::factory()->create();
        $otro = Examen::factory()->create();
        $lipidico = Categoria::create(['nombre' => 'Perfil lipídico']);
        $quimica = Categoria::create(['nombre' => 'Química sanguínea']);

        $lipidico->examenes()->attach([$examen->id, $otro->id]);
        $quimica->examenes()->attach($examen->id);

        $this->assertCount(2, $examen->fresh()->categorias);
        $this->assertCount(2, $lipidico->fresh()->examenes);
        $this->assertCount(1, $quimica->fresh()->examenes);
    }

    public function test_una_categoria_puede_estar_vacia(): void
    {
        $categoria = Categoria::create(['nombre' => 'Sin exámenes']);

        $this->assertCount(0, $categoria->fresh()->examenes);
    }

    public function test_no_duplica_el_mismo_par(): void
    {
        $examen = Examen::factory()->create();
        $categoria = Categoria::create(['nombre' => 'Repetida PK compuesta']);

        $categoria->examenes()->attach($examen->id);

        try {
            $categoria->examenes()->attach($examen->id);
            $this->fail('La clave primaria compuesta debería rechazar el par duplicado.');
        } catch (QueryException) {
            $this->assertCount(1, $categoria->fresh()->examenes);
        }
    }

    public function test_la_migracion_preserva_las_filas_legacy_de_categoria_examen(): void
    {
        $examen = Examen::factory()->create();
        $otroExamen = Examen::factory()->create();

        // Reconstruye el esquema anterior a la migración normalizadora.
        Schema::drop('categoria_examen');
        Schema::drop('categorias');

        Schema::create('categoria_examen', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->foreignId('examen_id')->constrained('examenes')->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });

        $createdAt = '2026-01-02 03:04:05';
        $updatedAt = '2026-02-03 04:05:06';

        DB::table('categoria_examen')->insert([
            [
                'id' => 91,
                'nombre' => 'Legacy EX02 alfa',
                'descripcion' => 'Categoría legacy alfa',
                'examen_id' => $examen->id,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ],
            [
                'id' => 92,
                'nombre' => 'Legacy EX02 beta',
                'descripcion' => null,
                'examen_id' => $otroExamen->id,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
            ],
        ]);

        $migracion = require database_path('migrations/2026_11_12_000000_normalize_categorias_examen.php');
        $migracion->up();

        $this->assertDatabaseHas('categorias', [
            'id' => 91,
            'nombre' => 'Legacy EX02 alfa',
            'descripcion' => 'Categoría legacy alfa',
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ]);
        $this->assertDatabaseHas('categorias', [
            'id' => 92,
            'nombre' => 'Legacy EX02 beta',
            'descripcion' => null,
            'created_at' => $createdAt,
            'updated_at' => $updatedAt,
        ]);

        $this->assertDatabaseHas('categoria_examen', [
            'categoria_id' => 91,
            'examen_id' => $examen->id,
        ]);
        $this->assertDatabaseHas('categoria_examen', [
            'categoria_id' => 92,
            'examen_id' => $otroExamen->id,
        ]);
        $this->assertFalse(Schema::hasColumn('categorias', 'examen_id'));
    }
}

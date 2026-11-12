<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Examen;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $categoria = Categoria::create(['nombre' => 'Repetida']);

        $categoria->examenes()->syncWithoutDetaching([$examen->id]);
        $categoria->examenes()->syncWithoutDetaching([$examen->id]);

        $this->assertCount(1, $categoria->fresh()->examenes);
    }
}

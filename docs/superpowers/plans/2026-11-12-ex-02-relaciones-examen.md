# EX-02 y categorías de exámenes Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Corregir las relaciones de `Examen` y convertir las categorías en una relación muchos-a-muchos con exámenes, preservando las filas actuales de `categoria_examen`.

**Architecture:** `Examen::procedimientos()` pasa a `hasMany`. La tabla actual `categoria_examen` (que en realidad contiene categorías con un `examen_id`) se migra a `categorias` y a un pivote `categoria_examen`; los modelos exponen `Examen::categorias()` y `Categoria::examenes()` como `belongsToMany`.

**Tech Stack:** Laravel 12/13, Eloquent, PHPUnit/Pest, SQLite en tests y MySQL en producción.

**Spec:** `docs/superpowers/specs/2026-11-12-ex-02-relaciones-examen-design.md`

## Global Constraints

- No perder las filas existentes de `categoria_examen` durante la migración.
- La migración debe funcionar en SQLite (tests) y MySQL (producción) sin `dropForeign` ni `dropColumn`.
- `categorias.nombre` sigue siendo único.
- No se toca el flujo de creación de órdenes ni el frontend en este plan.
- Tests con `RefreshDatabase` y SQLite en memoria.
- Commits pequeños y descriptivos.

## Review Focus

- Una categoría sin exámenes debe devolver una colección vacía, no un error.
- Un examen que pertenece a dos categorías debe resolver ambas y no duplicar filas.
- Adjuntar dos veces el mismo par categoría-examen no debe duplicar la relación.
- Un procedimiento con soft delete no debe aparecer en `$examen->procedimientos`.

---

### Task 1: Relación `Examen::procedimientos()`

**Files:**
- Modify: `app/Models/Examen.php:35-38`
- Test: `tests/Feature/ExamenProcedimientosTest.php`

**Interfaces:**
- Consumes: nada.
- Produces: `Examen::procedimientos(): HasMany` contra `App\Models\Procedimiento` con FK `examen_id`.

- [ ] **Step 1: Write the failing test**

```php
<?php

namespace Tests\Feature;

use App\Models\Examen;
use App\Models\Orden;
use App\Models\Persona;
use App\Models\Procedimiento;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\PaisSeeder;
use Database\Seeders\TestDataSeeder;
use Database\Seeders\TipoDocumentoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamenProcedimientosTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_examen_tiene_muchos_procedimientos(): void
    {
        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TestDataSeeder::class);

        $user = User::factory()->create();
        $sede = Sede::factory()->create();
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create();

        $orden = Orden::create([
            'sede_id' => $sede->id,
            'numero' => 1001,
            'paciente_id' => $paciente->id,
            'total' => 0,
            'user_id' => $user->id,
        ]);

        $procedimiento = Procedimiento::create([
            'orden_id' => $orden->id,
            'examen_id' => $examen->id,
            'sede_id' => $sede->id,
            'fecha' => now(),
            'diagnostico_principal' => 'Z017',
            'codigo_finalidad' => '14',
            'codigo_modalidad' => '01',
            'codigo_via_ingreso' => '01',
        ]);

        $this->assertTrue($examen->procedimientos->contains($procedimiento));
        $this->assertTrue($procedimiento->examen->is($examen));
        $this->assertCount(1, $examen->fresh()->procedimientos);
    }

    public function test_no_incluye_procedimientos_eliminados(): void
    {
        $this->seed(PaisSeeder::class);
        $this->seed(TipoDocumentoSeeder::class);
        $this->seed(TestDataSeeder::class);

        $user = User::factory()->create();
        $sede = Sede::factory()->create();
        $paciente = Persona::factory()->create();
        $examen = Examen::factory()->create();

        $orden = Orden::create([
            'sede_id' => $sede->id,
            'numero' => 1002,
            'paciente_id' => $paciente->id,
            'total' => 0,
            'user_id' => $user->id,
        ]);

        $procedimiento = Procedimiento::create([
            'orden_id' => $orden->id,
            'examen_id' => $examen->id,
            'sede_id' => $sede->id,
            'fecha' => now(),
            'diagnostico_principal' => 'Z017',
            'codigo_finalidad' => '14',
            'codigo_modalidad' => '01',
            'codigo_via_ingreso' => '01',
        ]);

        $procedimiento->delete();

        $this->assertCount(0, $examen->fresh()->procedimientos);
    }
}
```

- [ ] **Step 2: Run test to verify it fails**

Run: `php artisan test --filter=ExamenProcedimientosTest`
Expected: FAIL con `RelationNotFoundException` porque `Examen::procedimientos()` no existe.

- [ ] **Step 3: Replace the relation in `app/Models/Examen.php`**

Reemplazar el método `procedimiento()` por:

```php
public function procedimientos(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(Procedimiento::class);
}
```

- [ ] **Step 4: Run test to verify it passes**

Run: `php artisan test --filter=ExamenProcedimientosTest`
Expected: PASS (2 tests).

- [ ] **Step 5: Commit**

```bash
git add app/Models/Examen.php tests/Feature/ExamenProcedimientosTest.php
git commit -m "fix: corrige la relacion examen-procedimientos"
```

---

### Task 2: Categorías muchos-a-muchos

**Files:**
- Create: `database/migrations/2026_11_12_000000_normalize_categorias_examen.php`
- Modify: `app/Models/Categoria.php`
- Modify: `app/Models/Examen.php`
- Test: `tests/Feature/ExamenCategoriasTest.php`

**Interfaces:**
- Consumes: `Examen` de la Task 1.
- Produces:
  - `Categoria::examenes(): BelongsToMany` contra `Examen` con pivote `categoria_examen` (`categoria_id`, `examen_id`).
  - `Examen::categorias(): BelongsToMany` contra `Categoria` con el mismo pivote.
  - Tabla `categorias` (`id`, `nombre` único, `descripcion`, `timestamps`).
  - Elimina `Examen::categoria()`, que apuntaba a una columna inexistente.

- [ ] **Step 1: Write the failing tests**

```php
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
```

- [ ] **Step 2: Run tests to verify they fail**

Run: `php artisan test --filter=ExamenCategoriasTest`
Expected: FAIL porque la tabla `categorias` y las relaciones nuevas no existen.

- [ ] **Step 3: Create the migration**

```php
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
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->foreignId('examen_id')->constrained('examenes')->cascadeOnDelete();
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

    public function down(): void
    {
        Schema::create('categoria_examen_legacy', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->string('descripcion')->nullable();
            $table->foreignId('examen_id')->constrained('examenes')->cascadeOnDelete();
            $table->timestamps();
        });

        foreach (DB::table('categorias')->orderBy('id')->get() as $categoria) {
            $examenId = DB::table('categoria_examen')
                ->where('categoria_id', $categoria->id)
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
```

- [ ] **Step 4: Update `app/Models/Categoria.php`**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Categoria extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
    ];

    protected $table = 'categorias';

    public function examenes(): BelongsToMany
    {
        return $this->belongsToMany(Examen::class, 'categoria_examen', 'categoria_id', 'examen_id')
            ->withTimestamps();
    }
}
```

- [ ] **Step 5: Update `app/Models/Examen.php`**

Eliminar `categoria()` y agregar:

```php
public function categorias(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
{
    return $this->belongsToMany(Categoria::class, 'categoria_examen', 'examen_id', 'categoria_id')
        ->withTimestamps();
}
```

- [ ] **Step 6: Run tests to verify they pass**

Run: `php artisan test --filter=ExamenCategoriasTest`
Expected: PASS (4 tests).

- [ ] **Step 7: Run the full suite**

Run: `php artisan test`
Expected: PASS, sin fallos nuevos respecto de la línea base.

- [ ] **Step 8: Commit**

```bash
git add database/migrations/2026_11_12_000000_normalize_categorias_examen.php app/Models/Categoria.php app/Models/Examen.php tests/Feature/ExamenCategoriasTest.php
git commit -m "feat: convierte las categorias de examenes en muchos-a-muchos"
```

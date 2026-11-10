# Normalización del modelo de sexo a F/M/I Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Sustituir los códigos inconsistentes de sexo (`H`, `M`, `I`, `F`) por el catálogo `REF_Sexo` normalizado a `F`, `M`, `I`, y conectar ese catálogo con el modelo `Persona`, formularios, validaciones, reportes y lógica que consume sexo, sin tocar `examenes.sexo_aplicable` (donde `A` significa “ambos”).

**Architecture:** Nueva tabla catálogo `sexos` (`codigo`, `nombre`, `cod_sexo`) sembrada desde `resources/utils/tablas/REF_Sexo.csv`. `personas.sexo` sigue siendo el código canónico (`F`/`M`/`I`, nullable) y se relaciona con `sexos.codigo` mediante `Persona::sexoCatalogo()`; una migración normaliza `H → M` y añade integridad referencial donde el motor lo permita. Las validaciones usan `exists:sexos,codigo`; las vistas y formularios consumen el catálogo; la selección de referencias distingue `F`/`M` y trata `I`/`null` como “sin criterio de sexo”. Los límites RIPS traducen `F↔M`, `M↔H`, `I↔I`.

**Tech Stack:** Laravel 12, Eloquent, Blade, React 19, Vite, PHPUnit/Pest, SQLite en tests y MySQL en producción.

**Spec:** `docs/INFORME_VALORES_REFERENCIA.md` y la petición del 2026-11-10.

## Global Constraints

- Códigos canónicos en toda la app: `F` = Femenino (`cod_sexo` 02), `I` = Indeterminado o Intersexual (`cod_sexo` 03), `M` = Masculino (`cod_sexo` 01).
- `personas.sexo` solo admite `F`, `M`, `I` o `null`. `H` se migra a `M`. No se admiten otros valores.
- `examenes.sexo_aplicable` conserva `M`, `F`, `A`; `A` significa “ambos sexos” y **no** se convierte en `I`.
- No cambiar el significado de `A` en filtros de exámenes, CUP ni aplicabilidad.
- `I` no debe tratarse como femenino ni como masculino en la selección de referencias: equivale a “sin criterio de sexo”.
- Los códigos RIPS solo se traducen en los límites de exportación/importación: `F→M`, `M→H`, `I→I`; no se guardan códigos RIPS en `personas.sexo`.
- No romper los tests existentes. La suite usa SQLite en memoria (`phpunit.xml`).
- No tocar ni commitear `docs/INFORME_VALORES_REFERENCIA.md` ni `docs/INDEX.md`; son cambios de documentación previos.
- Commits pequeños y descriptivos.

## Review Focus

- Una persona con `sexo = 'I'` o `null` no debe recibir una referencia de `hombres` ni de `mujeres`; debe usar la referencia general/etaria o quedar sin referencia.
- Un examen con `sexo_aplicable = 'A'` debe seguir disponible para todos; no debe convertirse en `I`.
- Un reporte histórico de una persona con `I` debe mostrar “Indeterminado o Intersexual”, no “Femenino”.
- La validación debe rechazar `H` en altas y ediciones, y aceptar `F`, `M`, `I`.
- La migración debe dejar `H` como `M` sin romper filas nulas.
- El frontend debe seguir funcionando si el catálogo `sexos` aún no está en localStorage (fallback F/M/I).

---

### Task 1: Catálogo `sexos` y normalización de datos

**Files:**
- Create: `database/migrations/2026_11_10_000000_create_sexos_table.php`
- Create: `database/migrations/2026_11_10_000100_normalize_personas_sexo.php`
- Create: `app/Models/Sexo.php`
- Create: `database/seeders/SexoSeeder.php`
- Modify: `database/seeders/CatalogoBaseSeeder.php`
- Modify: `database/seeders/EmpresaSeeder.php`
- Test: `tests/Unit/SexoTest.php`

**Interfaces:**
- `App\Models\Sexo` con `$fillable = ['codigo', 'nombre', 'cod_sexo']`, `$primaryKey = 'codigo'`, `$keyType = 'string'`, `$incrementing = false`.
- `Sexo::opciones(): array` devuelve `['F' => 'Femenino', 'I' => 'Indeterminado o Intersexual', 'M' => 'Masculino']` ordenado por `nombre`.
- `Sexo::porCodigo(?string $codigo): ?self`.
- `Sexo::normalizar(?string $codigo): ?string` devuelve `M` para `H`/`M`, `F` para `F`, `I` para `I`, `null` para vacío; cualquier otro valor lanza `InvalidArgumentException`.
- `Sexo::aRips(?string $codigo): ?string` devuelve `F→M`, `M→H`, `I→I`, `null→null`.
- `Sexo::desdeRips(?string $codigo): ?string` devuelve `H→M`, `M→F`, `I→I`, `null→null`.

- [ ] **Step 1: Escribir el test del catálogo**

```php
// tests/Unit/SexoTest.php
it('expone los tres códigos canonicos del catalogo', function () {
    expect(App\Models\Sexo::normalizar('H'))->toBe('M');
    expect(App\Models\Sexo::normalizar('M'))->toBe('M');
    expect(App\Models\Sexo::normalizar('F'))->toBe('F');
    expect(App\Models\Sexo::normalizar('I'))->toBe('I');
    expect(App\Models\Sexo::normalizar(null))->toBeNull();
});

it('traduce a y desde RIPS sin guardar codigos RIPS', function () {
    expect(App\Models\Sexo::aRips('F'))->toBe('M');
    expect(App\Models\Sexo::aRips('M'))->toBe('H');
    expect(App\Models\Sexo::aRips('I'))->toBe('I');
    expect(App\Models\Sexo::desdeRips('H'))->toBe('M');
    expect(App\Models\Sexo::desdeRips('M'))->toBe('F');
});
```

- [ ] **Step 2: Ejecutar el test para verlo fallar**

Run: `php artisan test --filter=SexoTest`
Expected: FAIL porque `App\Models\Sexo` no existe.

- [ ] **Step 3: Crear la migración `sexos` e insertar el catálogo**

La migración crea:

```php
Schema::create('sexos', function (Blueprint $table) {
    $table->string('codigo', 1)->primary();
    $table->string('nombre');
    $table->string('cod_sexo', 2);
    $table->timestamps();
});
```

Después inserta, con `DB::table('sexos')->updateOrInsert`, las filas `F/Femenino/02`, `I/Indeterminado o Intersexual/03`, `M/Masculino/01`. Debe ser idempotente.

- [ ] **Step 4: Crear la migración de normalización**

Antes de cualquier FK:

```php
DB::table('personas')->where('sexo', 'H')->update(['sexo' => 'M']);
DB::table('personas')->whereNotNull('sexo')->whereNotIn('sexo', ['F', 'M', 'I'])->update(['sexo' => 'I']);
```

Si el driver no es SQLite, añadir la FK `personas.sexo → sexos.codigo` (`nullOnDelete` no aplica; usar `restrictOnDelete`). En SQLite omitir la FK y dejar la validación de aplicación. La migración debe poder ejecutarse dos veces sin fallar.

- [ ] **Step 5: Crear el modelo `Sexo`**

Implementar los métodos de la interfaz. `opciones()` puede consultar la tabla y hacer fallback a los tres valores si está vacía.

- [ ] **Step 6: Crear `SexoSeeder` y registrarlo**

`SexoSeeder` usa `Sexo::updateOrCreate(['codigo' => ...], ['nombre' => ..., 'cod_sexo' => ...])` para los tres códigos. Agregarlo a `CatalogoBaseSeeder` antes de `TipoDocumentoSeeder`.

- [ ] **Step 7: Corregir `EmpresaSeeder`**

Cambiar el único `'sexo' => 'H'` por `'sexo' => 'M'`. No cambiar los `M`, `I` ni `F`.

- [ ] **Step 8: Ejecutar tests y commit**

Run: `php artisan test --filter=SexoTest`
Expected: PASS.

```bash
git add app/Models/Sexo.php database/migrations/2026_11_10_000000_create_sexos_table.php database/migrations/2026_11_10_000100_normalize_personas_sexo.php database/seeders/SexoSeeder.php database/seeders/CatalogoBaseSeeder.php database/seeders/EmpresaSeeder.php tests/Unit/SexoTest.php
git commit -m "feat: agrega catalogo de sexos y normaliza personas"
```

---

### Task 2: Relación `Persona` y validaciones

**Files:**
- Modify: `app/Models/Persona.php`
- Modify: `app/Http/Requests/StorePersonaRequest.php`
- Modify: `app/Http/Requests/UpdatePersonaRequest.php`
- Modify: `app/Http/Requests/ApiUpdatePersonaRequest.php`
- Modify: `app/Http/Controllers/PersonaController.php`
- Test: `tests/Feature/SexoPersonaTest.php`

**Interfaces:**
- `Persona::sexoCatalogo(): BelongsTo` con `belongsTo(Sexo::class, 'sexo', 'codigo')`.
- `Persona::getSexoNombreAttribute(): string` devuelve el nombre del catálogo o `'No especificado'`.
- `Persona::getSexoRipsAttribute(): ?string` devuelve `Sexo::aRips($this->sexo)`.
- Las validaciones usan `['nullable', 'exists:sexos,codigo']`.

- [ ] **Step 1: Escribir el test de validación y relación**

```php
it('rechaza H y acepta F M I al actualizar una persona', function () {
    $persona = Persona::factory()->create(['sexo' => 'F']);
    $user = User::factory()->create();
    // actingAs + PUT route('personas.update', $persona), rellenar los campos requeridos.
    // H debe devolver error en sexo; M e I deben guardar.
});

it('resuelve el nombre del catalogo y el codigo RIPS', function () {
    $persona = Persona::factory()->create(['sexo' => 'I']);
    expect($persona->sexo_nombre)->toBe('Indeterminado o Intersexual');
    expect($persona->sexo_rips)->toBe('I');
});
```

- [ ] **Step 2: Ejecutar el test para verlo fallar**

Run: `php artisan test --filter=SexoPersonaTest`
Expected: FAIL por la validación `H` y por los accesos inexistentes.

- [ ] **Step 3: Implementar relación y accesores en `Persona`**

Añadir el `use` de `Sexo`, la relación y los dos accesores. No romper los casts existentes.

- [ ] **Step 4: Cambiar las reglas de validación**

En los tres FormRequest y en `PersonaController::index`, reemplazar `in:H,M,I,F` / `in:M,F,I` por `exists:sexos,codigo`. Mantener `nullable` donde ya estaba.

- [ ] **Step 5: Ejecutar tests y commit**

Run: `php artisan test --filter=SexoPersonaTest`
Expected: PASS.

```bash
git add app/Models/Persona.php app/Http/Requests/StorePersonaRequest.php app/Http/Requests/UpdatePersonaRequest.php app/Http/Requests/ApiUpdatePersonaRequest.php app/Http/Controllers/PersonaController.php tests/Feature/SexoPersonaTest.php
git commit -m "feat: valida sexo contra el catalogo y expone nombre RIPS"
```

---

### Task 3: Formularios y presentación (Blade + React)

**Files:**
- Modify: `resources/views/personas/edidt.blade.php`
- Modify: `resources/views/personas/index.blade.php`
- Modify: `resources/views/personas/show.blade.php`
- Modify: `resources/views/components/formPersona.blade.php`
- Modify: `resources/views/ordenes/show.blade.php`
- Modify: `resources/views/resultados/create.blade.php`
- Modify: `resources/views/resultados/show.blade.php`
- Modify: `resources/views/resultados/historia_show.blade.php`
- Modify: `resources/views/resultados/multiple.blade.php`
- Modify: `resources/views/resultados/reporte_orden.blade.php`
- Modify: `resources/js/components/FormPersona.jsx`
- Modify: `resources/js/components/DatosPersona.jsx`
- Modify: `app/Http/Controllers/Api/FrontendDataController.php`
- Modify: `resources/js/lib/catalogs.js`
- Test: `tests/Feature/SexoPresentacionTest.php`, `tests/js/catalogs.test.js`

**Interfaces:**
- El endpoint `/api/static-data-for-frontend` añade `sexos` con `[{codigo, nombre, cod_sexo}]`.
- `CATALOG_SCHEMA_VERSION` sube a `3`.
- `FormPersona.jsx` lee `getCatalogs().sexos` y usa fallback `[{codigo:'F',nombre:'Femenino'}, ...]`.

- [ ] **Step 1: Escribir el test de presentación**

Crear una persona `I`, una orden/procedimiento y pedir `route('resultados.reporte_orden', $orden)`. Verificar `assertSee('Indeterminado o Intersexual')` y `assertDontSee('Femenino')` en el bloque de sexo. Añadir una aserción análoga para `personas.show`.

- [ ] **Step 2: Ejecutar el test para verlo fallar**

Run: `php artisan test --filter=SexoPresentacionTest`
Expected: FAIL porque las vistas imprimen “Femenino” para `I`.

- [ ] **Step 3: Reemplazar los ternarios de sexo en Blade**

Usar `{{ $persona->sexo_nombre }}` en todas las vistas listadas. En `personas/index.blade.php`, generar las opciones con `@foreach (\App\Models\Sexo::opciones() as $codigo => $nombre)`. En `personas/edidt.blade.php`, usar el catálogo en vez del array con `H`.

- [ ] **Step 4: Conectar el catálogo al frontend**

Añadir `$sexos = Sexo::orderBy('nombre')->get(['codigo','nombre','cod_sexo']);` en `FrontendDataController` y la clave `'sexos' => $sexos` al payload. Subir `CATALOG_SCHEMA_VERSION` a `3` en `resources/js/lib/catalogs.js`.

- [ ] **Step 5: Usar el catálogo en React**

En `FormPersona.jsx`, importar `getCatalogs` y derivar `const sexos = getCatalogs().sexos ?? fallback`. Renderizar los radios desde ese array, manteniendo los `id` estables (`sexo_femenino`, `sexo_masculino`, `sexo_otro`) o generándolos con el código. En `DatosPersona.jsx`, usar el nombre del catálogo si está disponible y el mapa actual como fallback.

- [ ] **Step 6: Ajustar `tests/js/catalogs.test.js` si es necesario**

El test no afirma las claves del payload, pero verificar que sigue pasando. Si se añade una aserción de `sexos`, hacerlo con un payload de ejemplo.

- [ ] **Step 7: Ejecutar tests y commit**

Run: `php artisan test --filter=SexoPresentacionTest` y `npm test`
Expected: PASS.

```bash
git add resources/views resources/js/components/FormPersona.jsx resources/js/components/DatosPersona.jsx app/Http/Controllers/Api/FrontendDataController.php resources/js/lib/catalogs.js tests/Feature/SexoPresentacionTest.php tests/js/catalogs.test.js
git commit -m "feat: conecta formularios y reportes al catalogo de sexos"
```

---

### Task 4: Lógica que consume sexo (referencias, exámenes y RIPS)

**Files:**
- Modify: `app/Services/EscogerReferencia.php`
- Modify: `resources/js/components/ModalExamenes.jsx`
- Modify: `resources/js/components/DatosExamenes.jsx`
- Modify: `app/Http/Controllers/Api/ResultadosController.php`
- Modify: `app/Http/Controllers/ProcedimientoController.php`
- Modify: `app/Http/Controllers/ReprocesarController.php` (solo si traduce códigos)
- Test: `tests/Unit/EscogerReferenciaTest.php`, `tests/Feature/SexoRipsTest.php`

**Interfaces:**
- `EscogerReferencia::datosDemograficos()` devuelve `['sexo' => 'hombres'|'mujeres'|null, 'etario' => 'menores'|'adultos']`.
- `M` → `hombres`; `F` → `mujeres`; `I`/`null` → `null`.
- `estableceReferencia()` conserva la colección original y no la vacía al filtrar. Si hay referencias solo por sexo, las encuentra. Si `sexo` es `null`, no filtra por sexo.
- `ModalExamenes` recibe `sexoPaciente` `F`/`M`/`I`/`null`; `I`/`null` muestra todos los exámenes. `A` se sigue tratando como “ambos”.
- RIPS: `Api\ResultadosController::json_rips` y `ProcedimientoController` usan `$persona->sexo_rips`.

- [ ] **Step 1: Escribir los tests de referencias**

```php
it('mapea F a mujeres y M a hombres', function () {
    // Construir Parametro con setRelation('valoresReferencia', collect([...]))
    // y verificar datosDemograficos con personas F/M/I.
});

it('selecciona una referencia solo por sexo', function () {
    // refs hombres/mujeres; persona M adulta -> hombres.
});

it('no usa referencias de sexo para una persona I', function () {
    // refs hombres/mujeres; persona I -> null.
});
```

- [ ] **Step 2: Ejecutar los tests para verlos fallar**

Run: `php artisan test --filter=EscogerReferenciaTest`
Expected: FAIL en el caso de referencia solo por sexo (hoy devuelve `null`) y en `I`.

- [ ] **Step 3: Corregir `EscogerReferencia`**

Mapear `M→hombres`, `F→mujeres`, `I/null→null`. En `estableceReferencia`, guardar `$original = $referencias`, aplicar filtros sin perder la colección original y usar la referencia por sexo como fallback cuando la búsqueda por edad no la encuentre. No tratar `I` como `mujeres`.

- [ ] **Step 4: Ajustar el frontend de exámenes**

En `ModalExamenes`, tratar `I`/`null`/`A` como “sin filtro”. En `DatosExamenes`, el valor por defecto de `sexoPaciente` puede seguir siendo `'A'` porque `A` ya significa “ambos” en aplicabilidad; no convertirlo a `I`.

- [ ] **Step 5: Traducir RIPS en los límites**

Usar `$persona->sexo_rips` en `json_rips` y `ProcedimientoController:198`. Si `ReprocesarController` compara códigos RIPS, usar `Sexo::desdeRips()` solo para agrupar/mostrar; no modificar los datos originales del archivo.

- [ ] **Step 6: Añadir el test RIPS**

Crear una persona `M` y verificar que `json_rips` emite `codSexo = 'H'`; una `F` emite `'M'`; una `I` emite `'I'`.

- [ ] **Step 7: Ejecutar toda la suite y commit**

Run: `php artisan test` y `npm test`
Expected: PASS.

```bash
git add app/Services/EscogerReferencia.php resources/js/components/ModalExamenes.jsx resources/js/components/DatosExamenes.jsx app/Http/Controllers/Api/ResultadosController.php app/Http/Controllers/ProcedimientoController.php app/Http/Controllers/ReprocesarController.php tests/Unit/EscogerReferenciaTest.php tests/Feature/SexoRipsTest.php
git commit -m "feat: normaliza sexo en referencias, examenes y RIPS"
```

---

## Self-Review

- Cobertura: catálogo, migración, modelo, validaciones, formularios Blade, formularios React, reportes, referencias, exámenes y RIPS.
- `A` de aplicabilidad no se toca ni se convierte en `I`.
- Los tests existentes deben seguir pasando en SQLite; la FK solo se añade en motores que la soportan.
- Los reportes usan `sexo_nombre`; la lógica de referencias usa `F`/`M` y descarta `I`.
- No se incluye `docs/INFORME_VALORES_REFERENCIA.md` ni `docs/INDEX.md` en los commits.

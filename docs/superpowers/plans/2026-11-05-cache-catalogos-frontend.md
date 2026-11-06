# Caché de catálogos del frontend (A+B+D) — Plan de implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Eliminar CUPS y CIE10 de `localStorage`, endurecer la caché de catálogos del frontend y cachear el endpoint en el servidor con `ETag`/304, sin romper el aislamiento multi-instancia.

**Architecture:** Un único módulo `resources/js/lib/catalogs.js` concentra la lectura/escritura de catálogos, la versión de esquema, el TTL de revalidación y el manejo de cuota. El backend construye una sola vez el payload liviano (sin CUPS/CIE), lo guarda con `Cache::remember`, lo invalida mediante un observer de modelos y responde `304` cuando el `ETag` coincide. Los componentes React consumen el módulo en vez de leer `localStorage` directamente.

**Tech Stack:** PHP 8.5, Laravel 13, Pest 4, React 19, Vite 7, Node 24 (`node --test`), SQLite `:memory:` en tests, `CACHE_STORE=database` en producción.

**Spec:** `docs/INFORME_CACHE_LOCALSTORAGE.md`

## Global Constraints

- **No hacer `git commit` ni `git push` sin confirmación explícita del usuario.** Los pasos de commit de este plan son propuestas: hay que pedir confirmación antes de ejecutarlos.
- **No crear ramas ni tags** sin pedirlo. Se trabaja sobre la rama actual (`conciliador`).
- **Suite base verde:** hoy `php artisan test --compact` da `118 passed, 8 skipped, 0 failed`. Cualquier fallo nuevo es un bloqueo; los 8 `skipped` son `ExamenesPrecioApiTest` (I-11).
- **Aislamiento multi-instancia intacto:** no introducir `empresa_id`, global scopes ni rutas compartidas. Cada cliente sigue siendo una instancia con su propia BD, `APP_KEY` y `CACHE_STORE`.
- **CUPS y CIE10 no pueden viajar en `/api/static-data-for-frontend` ni guardarse en `localStorage`.** El catálogo grande se resuelve con los endpoints de búsqueda.
- **Un solo módulo toca `localStorage`:** `resources/js/lib/catalogs.js` y `resources/js/lib/cieRecents.js`. Ningún componente, hook o entrypoint puede llamar `localStorage` directamente.
- **El endpoint de catálogos exige `auth:sanctum` y `throttle:60,1`.**
- **Sin dependencias npm nuevas.** Los tests de JS usan el runner nativo de Node.
- **Producción usa `CACHE_STORE=database`** y la tabla `cache` ya existe (`0001_01_01_000001_create_cache_table.php`). Si una instancia usa `file`, también funciona.
- **Idioma:** nombres, comentarios y textos de interfaz en español, siguiendo el estilo del repositorio.
- **No tocar** `AplicabilidadSexoCupSeeder`, la deuda C-03 ni los seeders de clientes.

## Review Focus

Estos son los modos de fallo que el spec implica y que no se ven en el camino feliz. Cada uno tiene su test en la tarea que lo posee:

1. **Sesión expirada en el formulario de orden:** `loadCatalogs()` recibe 401 y no hay caché. Los hooks deben quedar con arreglos vacíos y la pantalla debe renderizar, no lanzar. Test en Task 5 (`rechaza si no hay caché y la respuesta no es ok`) y manejo `.catch()` en Task 7.
2. **Cuota de `localStorage` agotada:** la escritura falla pero la app sigue funcionando con los datos en memoria y reintenta en la próxima carga. Test en Task 5.
3. **Dos componentes montan a la vez** (`FormPersona` y `DatosOrden` en la misma página): una sola petición de red. Test en Task 5.
4. **`ETag` viejo con payload ausente** (alguien borró solo el payload): no se debe aceptar un 304 vacío; hay que hacer fetch completo. Test en Task 5.
5. **Admin activa un catálogo desde Setup:** la caché del servidor se invalida; pero el `resetearNiveles()` de `ServicioHabilitado` guarda `nivel` en cada request y **no** debe invalidar la caché. Tests en Task 3.

---

## Estructura de archivos

### Backend

| Archivo | Responsabilidad |
|---|---|
| `app/Support/CatalogsCache.php` (nuevo) | Clave única, `remember()` y `forget()` de la caché de catálogos. |
| `app/Observers/CatalogoObserver.php` (nuevo) | Invalida la caché cuando cambia un catálogo, ignorando cambios solo de `nivel`. |
| `app/Providers/AppServiceProvider.php` | Registra el observer en los modelos de catálogo. |
| `app/Http/Controllers/Api/FrontendDataController.php` | Payload liviano, `Cache::remember`, `ETag`, 304 y headers de caché. |
| `app/Http/Controllers/Api/CupsController.php` | Búsqueda por nombre acotada y validada. |
| `app/Http/Controllers/Api/CodigoDiagnosticoController.php` | Búsqueda CIE acotada y validada. |
| `routes/api.php` | Endpoint dentro de `auth:sanctum` + throttle; orden correcto de rutas de CUPS. |
| `tests/Feature/FrontendDataApiTest.php` (nuevo) | Auth, payload, caché, ETag y observer. |
| `tests/Feature/CatalogosBusquedaApiTest.php` (nuevo) | Búsquedas CUPS/CIE, límites y validación. |

### Frontend

| Archivo | Responsabilidad |
|---|---|
| `resources/js/lib/catalogs.js` (nuevo) | Caché versionada, TTL, cuota, `If-None-Match`, dedupe. |
| `resources/js/lib/cieRecents.js` (nuevo) | Recientes de CIE por usuario, sin colisión con el catálogo. |
| `resources/js/components/hooks/usePersonaReferencias.jsx` | Lee del módulo de catálogos. |
| `resources/js/components/hooks/useTablasRef.jsx` | Lee del módulo de catálogos. |
| `resources/js/components/ConvenioForm.jsx` | Países/municipios/documentos desde el módulo. |
| `resources/js/convenioCreate.jsx` | Deja de leer `localStorage`; conserva el JSON embebido. |
| `resources/js/components/setup/Setup.jsx` | Deja de escribir `localStorage`; invalida la caché al activar. |
| `resources/js/components/CieSearchInput.jsx` | Recientes con `cieRecents`. |
| `resources/views/layouts/app.blade.php` | Meta `user-id` para separar recientes. |
| `resources/views/dashboard.blade.php`, `sedes/select.blade.php` | Se quita el cargador legacy. |
| `vite.config.js` | Se quitan los entrypoints muertos. |
| `package.json` | Script `test` con `node --test`. |
| `tests/js/catalogs.test.js` (nuevo) | Tests del módulo de caché. |
| `tests/js/cieRecents.test.js` (nuevo) | Tests de recientes. |
| `tests/js/no-legacy-storage.test.js` (nuevo) | Impide que vuelvan las lecturas directas y los módulos muertos. |

### Archivos que se eliminan (Task 8)

`resources/js/obtenerStaticos.js`, `main.js`, `manejadorEventos.js`, `crearExamenes.js`, `variables.js`, `api.js`, `formularioPersona.js`, `crearOrdenes.js`, `crearPersona.js`, `buscarPersona.js`, `resultado.js`, `setup.js`, `components/hooks/useCieCups.jsx`, `utils/paisesLocalStorage.js`, `ejemplos/`, `resources/views/diagnosticos/create.blade.php`, `resources/views/cups/create.blade.php`.

---

## Task 1: Endpoint autenticado, con throttle y sin CUPS/CIE

**Files:**
- Modify: `routes/api.php:16`
- Modify: `app/Http/Controllers/Api/FrontendDataController.php`
- Test: `tests/Feature/FrontendDataApiTest.php` (nuevo)

**Interfaces:**
- Consumes: `ServicioHabilitado`, `ViaIngreso`, `TipoAfiliacion`, `CausaExterna`, `ModalidadAtencion`, `Finalidad`, `TipoDocumento`, `Pais`, `Municipio`, `Eps`.
- Produces: `GET /api/static-data-for-frontend` (auth + throttle) → JSON con `documentos_paciente`, `documentos_pagador`, `paises`, `municipios`, `eps`, `servicios_habilitados`, `via_ingreso`, `modalidad_atencion`, `tipo_afiliacion`, `causa_externa`, `finalidad_consulta`. Sin `cups` ni `defaultCies`.

- [ ] **Step 1: Escribir los tests que fallan**

```php
<?php

use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Database\Seeders\TestDataSeeder;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(TestDataSeeder::class);
    $this->prestador = User::factory()->create();
    $this->prestador->assignRole('prestador');
});

it('exige autenticación', function () {
    $this->getJson('/api/static-data-for-frontend')->assertUnauthorized();
});

it('entrega los catálogos livianos sin CUPS ni CIE', function () {
    $this->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertOk()
        ->assertJsonStructure([
            'documentos_paciente',
            'documentos_pagador',
            'paises',
            'municipios',
            'eps',
            'servicios_habilitados',
            'via_ingreso',
            'modalidad_atencion',
            'tipo_afiliacion',
            'causa_externa',
            'finalidad_consulta',
        ])
        ->assertJsonMissingPath('cups')
        ->assertJsonMissingPath('defaultCies');
});

it('está protegido por sanctum y throttle', function () {
    $route = collect(Route::getRoutes())->first(
        fn ($route) => $route->uri() === 'api/static-data-for-frontend'
    );

    expect($route)->not->toBeNull();
    expect($route->gatherMiddleware())
        ->toContain('auth:sanctum')
        ->toContain('throttle:60,1');
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `php artisan test --filter=FrontendDataApiTest`
Expected: FAIL. El primer test recibe 200 (hoy es público) y el segundo encuentra `cups`/`defaultCies`.

- [ ] **Step 3: Mover la ruta dentro del grupo autenticado**

En `routes/api.php`, eliminar la línea 16 y agregar dentro de `Route::middleware(['auth:sanctum', 'api'])->group(function () { ... })`:

```php
Route::get('/static-data-for-frontend', [FrontendDataController::class, 'getStaticData'])
    ->middleware('throttle:60,1');
```

- [ ] **Step 4: Quitar CUPS y CIE del payload**

En `FrontendDataController.php`:
- Borrar las líneas que consultan `CodigoCup` y `CodigoDiagnostico` (incluido el fallback de 12 CIE).
- Borrar `'cups' => ...` y `'defaultCies' => ...` del arreglo `$data`.
- Borrar los imports `CodigoCup`, `Cache` y `use function Pest\Laravel\get;`.
- Mantener el resto de las consultas y llaves tal cual.

- [ ] **Step 5: Ejecutar y verificar que pasa**

Run: `php artisan test --filter=FrontendDataApiTest`
Expected: PASS (3 tests).

- [ ] **Step 6: Commit (pedir confirmación antes)**

```bash
git add routes/api.php app/Http/Controllers/Api/FrontendDataController.php tests/Feature/FrontendDataApiTest.php
git commit -m "fix: protege y aligera el endpoint de catálogos"
```

---

## Task 2: Caché de servidor + `ETag`/304

**Files:**
- Create: `app/Support/CatalogsCache.php`
- Modify: `app/Http/Controllers/Api/FrontendDataController.php`
- Test: `tests/Feature/FrontendDataApiTest.php`

**Interfaces:**
- Produces:
  - `CatalogsCache::KEY` = `'frontend.catalogs.v2'`.
  - `CatalogsCache::remember(\Closure $builder): array`.
  - `CatalogsCache::forget(): void`.
- Consumes: `Task 1` (payload sin CUPS/CIE).

- [ ] **Step 1: Escribir los tests que fallan**

Agregar al mismo archivo:

```php
use App\Support\CatalogsCache;
use Illuminate\Support\Facades\Cache;

it('cachea el payload en el servidor', function () {
    Cache::flush();
    $this->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertOk();

    expect(Cache::has(CatalogsCache::KEY))->toBeTrue();
});

it('devuelve ETag y 304 cuando coincide', function () {
    $first = $this->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertOk();

    $etag = $first->headers->get('ETag');
    expect($etag)->not->toBeNull();

    $this->withHeaders(['If-None-Match' => $etag])
        ->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertStatus(304)
        ->assertHeader('ETag', $etag);
});

it('marca la respuesta como privada y revalidable', function () {
    $response = $this->actingAs($this->prestador)
        ->getJson('/api/static-data-for-frontend')
        ->assertOk()
        ->assertHeader('Vary', 'Cookie');

    $cacheControl = $response->headers->get('Cache-Control');
    expect($cacheControl)
        ->toContain('private')
        ->toContain('max-age=0')
        ->toContain('must-revalidate');
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `php artisan test --filter=FrontendDataApiTest`
Expected: FAIL. `CatalogsCache` no existe y no hay `ETag`.

- [ ] **Step 3: Crear `app/Support/CatalogsCache.php`**

```php
<?php

namespace App\Support;

use Closure;
use Illuminate\Support\Facades\Cache;

final class CatalogsCache
{
    public const KEY = 'frontend.catalogs.v2';

    public static function remember(Closure $builder): array
    {
        return Cache::remember(self::KEY, now()->addHour(), $builder);
    }

    public static function forget(): void
    {
        Cache::forget(self::KEY);
    }
}
```

- [ ] **Step 4: Usar la caché y el `ETag` en el controlador**

Firma: `public function getStaticData(Request $request): JsonResponse|Response`.

```php
$payload = CatalogsCache::remember(fn () => $this->buildPayload());
$etag = md5(json_encode($payload));

if ($request->header('If-None-Match') === '"' . $etag . '"') {
    return response()
        ->noContent(304)
        ->setEtag($etag)
        ->setCache(['private' => true, 'max_age' => 0, 'must_revalidate' => true])
        ->header('Vary', 'Cookie');
}

return response()
    ->json($payload)
    ->setEtag($etag)
    ->setCache(['private' => true, 'max_age' => 0, 'must_revalidate' => true])
    ->header('Vary', 'Cookie');
```

Mover las 11 consultas a `private function buildPayload(): array`. El `try/catch` actual se elimina: Laravel ya responde 500 sin filtrar detalles en producción.

- [ ] **Step 5: Ejecutar y verificar que pasa**

Run: `php artisan test --filter=FrontendDataApiTest`
Expected: PASS (6 tests).

- [ ] **Step 6: Commit (pedir confirmación antes)**

```bash
git add app/Support/CatalogsCache.php app/Http/Controllers/Api/FrontendDataController.php tests/Feature/FrontendDataApiTest.php
git commit -m "feat: cachea los catálogos del frontend con ETag"
```

---

## Task 3: Invalidación de la caché por cambios de catálogo

**Files:**
- Create: `app/Observers/CatalogoObserver.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Test: `tests/Feature/FrontendDataApiTest.php`

**Interfaces:**
- Produces: observer registrado para `ServicioHabilitado`, `ViaIngreso`, `TipoAfiliacion`, `CausaExterna`, `ModalidadAtencion`, `Finalidad`, `TipoDocumento`, `Pais`, `Municipio`, `Eps`.
- Consumes: `CatalogsCache` (Task 2).

- [ ] **Step 1: Escribir los tests que fallan**

```php
use App\Models\ServicioHabilitado;
use Illuminate\Support\Facades\Cache;

it('invalida la caché al activar un catálogo desde la API', function () {
    Cache::flush();
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $this->actingAs($admin)->getJson('/api/static-data-for-frontend')->assertOk();
    expect(Cache::has(CatalogsCache::KEY))->toBeTrue();

    $servicio = ServicioHabilitado::firstOrFail();
    $this->actingAs($admin)
        ->patchJson("/api/servicios-habilitados/{$servicio->codigo}/activar")
        ->assertOk();

    expect(Cache::has(CatalogsCache::KEY))->toBeFalse();
});

it('invalida la caché cuando cambia el nombre de un catálogo', function () {
    Cache::flush();
    $this->actingAs($this->prestador)->getJson('/api/static-data-for-frontend')->assertOk();

    $servicio = ServicioHabilitado::firstOrFail();
    $servicio->nombre = 'LABORATORIO CLINICO ACTUALIZADO';
    $servicio->save();

    expect(Cache::has(CatalogsCache::KEY))->toBeFalse();
});

it('no invalida la caché cuando solo cambia el nivel', function () {
    Cache::flush();
    $this->actingAs($this->prestador)->getJson('/api/static-data-for-frontend')->assertOk();
    expect(Cache::has(CatalogsCache::KEY))->toBeTrue();

    $servicio = ServicioHabilitado::firstOrFail();
    $servicio->nivel = 250;
    $servicio->save();

    expect(Cache::has(CatalogsCache::KEY))->toBeTrue();
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `php artisan test --filter=FrontendDataApiTest`
Expected: FAIL. Sin observer, la caché no se invalida.

- [ ] **Step 3: Crear el observer**

```php
<?php

namespace App\Observers;

use App\Support\CatalogsCache;
use Illuminate\Database\Eloquent\Model;

class CatalogoObserver
{
    public function saved(Model $model): void
    {
        if ($model->wasRecentlyCreated) {
            CatalogsCache::forget();
            return;
        }

        // ServicioHabilitado::resetearNiveles() guarda `nivel` en cada request.
        // Ignorar ese cambio evita invalidar la caché en cada petición.
        if (array_diff(array_keys($model->getChanges()), ['nivel']) !== []) {
            CatalogsCache::forget();
        }
    }

    public function deleted(Model $model): void
    {
        CatalogsCache::forget();
    }
}
```

- [ ] **Step 4: Registrar el observer**

En `AppServiceProvider::boot()`, después de las políticas:

```php
$modelosCatalogo = [
    \App\Models\ServicioHabilitado::class,
    \App\Models\ViaIngreso::class,
    \App\Models\TipoAfiliacion::class,
    \App\Models\CausaExterna::class,
    \App\Models\ModalidadAtencion::class,
    \App\Models\Finalidad::class,
    \App\Models\TipoDocumento::class,
    \App\Models\Pais::class,
    \App\Models\Municipio::class,
    \App\Models\Eps::class,
];

foreach ($modelosCatalogo as $modelo) {
    $modelo::observe(\App\Observers\CatalogoObserver::class);
}
```

- [ ] **Step 5: Ejecutar y verificar que pasa**

Run: `php artisan test --filter=FrontendDataApiTest`
Expected: PASS (9 tests).

- [ ] **Step 6: Commit (pedir confirmación antes)**

```bash
git add app/Observers/CatalogoObserver.php app/Providers/AppServiceProvider.php tests/Feature/FrontendDataApiTest.php
git commit -m "feat: invalida la caché de catálogos al modificarlos"
```

---

## Task 4: Endpoints de búsqueda CUPS/CIE acotados y con la ruta correcta

**Files:**
- Modify: `app/Http/Controllers/Api/CupsController.php`
- Modify: `app/Http/Controllers/Api/CodigoDiagnosticoController.php`
- Modify: `routes/api.php`
- Test: `tests/Feature/CatalogosBusquedaApiTest.php` (nuevo)

**Interfaces:**
- Produces:
  - `GET /api/cups/buscar?nombre=...` → `{"data":[{"codigo","nombre"}]}` máx. 20.
  - `POST /api/cie10/buscar` con `{"query":"..."}` → `{"message","data":[{"codigo","nombre","descripcion","grupo","sub_grupo","nivel"}]}` máx. 20.
- Consumes: modelos `CodigoCup` y `CodigoDiagnostico`.

- [ ] **Step 1: Escribir los tests que fallan**

```php
<?php

use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->prestador = User::factory()->create();
    $this->prestador->assignRole('prestador');
});

it('busca CUPS por nombre y no cae en la ruta show', function () {
    DB::table('codigo_cups')->insert([
        ['codigo' => '90.01.01', 'nombre' => 'HEMOGRAMA COMPLETO', 'activo' => true, 'nivel' => 1],
        ['codigo' => '90.01.02', 'nombre' => 'HEMOGLOBINA', 'activo' => true, 'nivel' => 1],
    ]);

    $this->actingAs($this->prestador)
        ->getJson('/api/cups/buscar?nombre=HEMO')
        ->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonFragment(['codigo' => '90.01.01'])
        ->assertJsonMissingPath('data.0.descripcion');
});

it('limita la búsqueda de CUPS a 20 resultados', function () {
    $rows = [];
    for ($i = 1; $i <= 25; $i++) {
        $rows[] = [
            'codigo' => sprintf('90.02.%02d', $i),
            'nombre' => "HEMO {$i}",
            'activo' => true,
            'nivel' => 1,
        ];
    }
    DB::table('codigo_cups')->insert($rows);

    $this->actingAs($this->prestador)
        ->getJson('/api/cups/buscar?nombre=HEMO')
        ->assertOk()
        ->assertJsonCount(20, 'data');
});

it('valida el parámetro nombre de CUPS', function () {
    $this->actingAs($this->prestador)
        ->getJson('/api/cups/buscar')
        ->assertStatus(422);
});

it('busca CIE y limita a 20 resultados', function () {
    $rows = [];
    for ($i = 1; $i <= 25; $i++) {
        $rows[] = [
            'codigo' => sprintf('ZZ%02d', $i),
            'nombre' => "PRUEBA {$i}",
            'descripcion' => "Descripcion {$i}",
            'activo' => true,
            'nivel' => 1,
        ];
    }
    DB::table('codigo_diagnosticos')->insert($rows);

    $this->actingAs($this->prestador)
        ->postJson('/api/cie10/buscar', ['query' => 'PRUEBA'])
        ->assertOk()
        ->assertJsonCount(20, 'data');
});

it('valida el parámetro query de CIE', function () {
    $this->actingAs($this->prestador)
        ->postJson('/api/cie10/buscar', ['query' => ''])
        ->assertStatus(422);
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `php artisan test --filter=CatalogosBusquedaApiTest`
Expected: FAIL. `GET /api/cups/buscar` cae en `cups/{codigo}` y devuelve 404; además no hay límite ni validación.

- [ ] **Step 3: Reordenar las rutas de CUPS**

En `routes/api.php`, dentro de `can:ver_examen`, dejar este orden:

```php
Route::get('cups', [CupsController::class, 'index']);
Route::get('cups/buscar/{codigo}', [CupsController::class, 'buscarPorCodigo']);
Route::get('cups/buscar', [CupsController::class, 'buscarPorNombre']);
Route::get('cups/{codigo}', [CupsController::class, 'show']);
```

- [ ] **Step 4: Acotar `CupsController::buscarPorNombre`**

```php
$validated = $request->validate([
    'nombre' => ['required', 'string', 'min:2', 'max:100'],
]);

$cups = CodigoCup::query()
    ->where('nombre', 'like', '%' . $validated['nombre'] . '%')
    ->orderBy('nombre')
    ->limit(20)
    ->get(['codigo', 'nombre']);

return response()->json(['data' => $cups]);
```

- [ ] **Step 5: Acotar `CodigoDiagnosticoController::search`**

```php
$validated = $request->validate([
    'query' => ['required', 'string', 'min:2', 'max:100'],
]);

$query = $validated['query'];

$resultados = CodigoDiagnostico::query()
    ->where(function ($consulta) use ($query) {
        $consulta->where('nombre', 'like', "%{$query}%")
            ->orWhere('descripcion', 'like', "%{$query}%")
            ->orWhere('grupo', 'like', "%{$query}%")
            ->orWhere('sub_grupo', 'like', "%{$query}%")
            ->orWhere('codigo', 'like', "%{$query}%");
    })
    ->orderByDesc('nivel')
    ->limit(20)
    ->get(['codigo', 'nombre', 'descripcion', 'grupo', 'sub_grupo', 'nivel']);

return response()->json([
    'message' => 'Resultados de búsqueda',
    'data' => $resultados,
]);
```

No filtrar por `activo`: `CieSearchInput` también resuelve el nombre de un CIE ya guardado en una orden y ese código puede estar inactivo. Cambiar ese comportamiento sería una regresión clínica.

- [ ] **Step 6: Ejecutar y verificar que pasa**

Run: `php artisan test --filter=CatalogosBusquedaApiTest`
Expected: PASS (5 tests).

- [ ] **Step 7: Commit (pedir confirmación antes)**

```bash
git add app/Http/Controllers/Api/CupsController.php app/Http/Controllers/Api/CodigoDiagnosticoController.php routes/api.php tests/Feature/CatalogosBusquedaApiTest.php
git commit -m "fix: acota y valida las búsquedas de CUPS y CIE"
```

---

## Task 5: Módulo de caché del frontend y runner de tests

**Files:**
- Create: `resources/js/lib/catalogs.js`
- Create: `tests/js/catalogs.test.js`
- Modify: `package.json`

**Interfaces:**
- Produces:
  - `createCatalogCache({ storage, fetchImpl, now, revalidateAfterMs })` → `{ load, getCatalogs, getCatalog, invalidate, clear }`.
  - `load({ force }) → Promise<{ data, etag, savedAt, fromCache }>`.
  - `getCatalog(nombre) → Array` (nunca `undefined`).
  - `invalidate()`, `clear()`.
  - `createMemoryStorage()`.
  - Constantes `CATALOG_SCHEMA_VERSION = 2`, `CATALOG_ENDPOINT = '/api/static-data-for-frontend'`, `CATALOG_REVALIDATE_AFTER_MS = 5 * 60 * 1000`, `STORAGE_KEYS`, `LEGACY_STORAGE_KEYS`.
  - Singleton exportado: `loadCatalogs`, `getCatalogs`, `getCatalog`, `invalidateCatalogs`, `clearCatalogs`.

- [ ] **Step 1: Agregar el script de tests**

En `package.json`:

```json
"scripts": {
    "build": "vite build",
    "dev": "vite",
    "test": "node --test tests/js"
}
```

- [ ] **Step 2: Escribir los tests que fallan**

`tests/js/catalogs.test.js` (usar `node:test` y `node:assert/strict`):

```js
import test from 'node:test';
import assert from 'node:assert/strict';
import {
    CATALOG_SCHEMA_VERSION,
    STORAGE_KEYS,
    createCatalogCache,
    createMemoryStorage,
} from '../../resources/js/lib/catalogs.js';

const payload = { paises: [{ codigo_iso: 'CO', nombre: 'Colombia' }], eps: [] };

const ok = (body, etag = '"v1"') => ({
    status: 200,
    ok: true,
    headers: { get: (name) => (name.toLowerCase() === 'etag' ? etag : null) },
    json: async () => body,
});

const notModified = (etag = '"v1"') => ({
    status: 304,
    ok: false,
    headers: { get: (name) => (name.toLowerCase() === 'etag' ? etag : null) },
    json: async () => { throw new Error('304 no debe leerse'); },
});
```

Casos obligatorios:

1. `guarda y devuelve los catálogos` — `load()` devuelve `payload`, `fromCache === false`, `getCatalog('paises')` coincide y `STORAGE_KEYS.version === String(CATALOG_SCHEMA_VERSION)`.
2. `no vuelve a pedir dentro de la ventana de revalidación` — dos `load()` con `now` fijo y `revalidateAfterMs: 5000` → una sola llamada a `fetchImpl`.
3. `revalida con If-None-Match y acepta 304` — primer `load()` 200 con `ETag: "v1"`; avanzar `now` más allá de la ventana; el segundo `fetchImpl` recibe `options.headers['If-None-Match'] === '"v1"'` y devuelve 304; el resultado conserva `payload` y `fromCache === true`.
4. `si falta el payload no envía If-None-Match` — guardar solo `version` y `etag`; `load()` debe llamar `fetchImpl` con `If-None-Match === undefined`.
5. `descarta una caché de otra versión de esquema` — guardar `version = '1'` y un payload viejo; `load()` debe pedir de nuevo y devolver el payload nuevo.
6. `sigue funcionando si el almacenamiento lanza QuotaExceededError` — storage que lanza al escribir `STORAGE_KEYS.payload`; `load()` devuelve `payload` y `getCatalog('paises')` sigue funcionando desde memoria.
7. `deduplica cargas concurrentes` — `Promise.all([cache.load(), cache.load()])` → una sola llamada a `fetchImpl`.
8. `invalidate obliga a pedir de nuevo sin ETag` — después de `load()`, `cache.invalidate()`, avanzar `now`; el segundo fetch no lleva `If-None-Match`.
9. `si la red falla, devuelve la caché existente` — primer `load()` ok; `invalidate()`; segundo fetch lanza; el resultado devuelve el payload con `fromCache === true`.
10. `devuelve un arreglo vacío para un catálogo ausente` — `getCatalog('desconocido')` → `[]`.
11. `rechaza si no hay caché y la respuesta no es ok` — fetch responde 401; `await assert.rejects(() => cache.load(), /HTTP 401/)`.

- [ ] **Step 3: Ejecutar y verificar que falla**

Run: `pnpm test`
Expected: FAIL con `ERR_MODULE_NOT_FOUND` para `resources/js/lib/catalogs.js`.

- [ ] **Step 4: Implementar `resources/js/lib/catalogs.js`**

Reglas exactas:

- `CATALOG_SCHEMA_VERSION = 2`.
- `CATALOG_REVALIDATE_AFTER_MS = 5 * 60 * 1000` (la caché local se revalida contra el servidor cada 5 minutos; el `ETag` abarata esa revalidación).
- `STORAGE_KEYS = { payload: 'lis.catalogs.payload', etag: 'lis.catalogs.etag', savedAt: 'lis.catalogs.savedAt', version: 'lis.catalogs.version' }`.
- `LEGACY_STORAGE_KEYS` incluye: `frontend_data_last_update`, `documentos_paciente_data`, `documentos_pagador_data`, `paises_data`, `municipios_data`, `eps_data`, `cups_data`, `default_cies_data`, `servicios_habilitados_data`, `via_ingreso_data`, `vias_ingreso_data`, `modalidad_atencion_data`, `tipos_atencion_data`, `finalidad_consulta_data`, `causas_externas_data`, `tipos_afiliacion_data`, `tipos_documento_data`, `paises`, `municipios`.
- `createMemoryStorage()` implementa `getItem`, `setItem`, `removeItem` sobre un `Map`.
- `writeStored` escribe en este orden: `payload`, `etag`, `version`, **`savedAt` al final**. Si `payload` falla, no escribe `savedAt` (H-02).
- `readStored` devuelve `null` si falta `version`, si no coincide con `CATALOG_SCHEMA_VERSION`, si falta `payload` o si el JSON es inválido. En ese caso limpia **solo** las claves de storage (`clearStored()`), sin borrar el fallback en memoria.
- `load`:
  - Deduplica con una promesa `inFlight` a nivel de instancia.
  - Devuelve la caché si `!force` y `now() - savedAt < revalidateAfterMs`.
  - Envía `If-None-Match` solo si hay `payload` y `etag`.
  - `304` → devuelve la caché y reescribe `savedAt`.
  - `200` → valida que sea un objeto (no array), guarda, actualiza `memory` y devuelve `fromCache: false`.
  - Error de red con caché → devuelve la caché con `fromCache: true`.
  - Error de red sin caché → relanza.
- `invalidate()` elimina `savedAt` y `etag`, conserva `payload`.
- `clear()` elimina las claves nuevas, las legacy y el fallback en memoria.
- El singleton usa `window.localStorage` si existe; si no, `createMemoryStorage()`. `fetchImpl` por defecto es `(...args) => fetch(...args)` y se llama con `{ headers: { Accept: 'application/json' }, credentials: 'same-origin', cache: 'no-cache' }`.

- [ ] **Step 5: Ejecutar y verificar que pasa**

Run: `pnpm test`
Expected: PASS (11 tests).

- [ ] **Step 6: Commit (pedir confirmación antes)**

```bash
git add resources/js/lib/catalogs.js tests/js/catalogs.test.js package.json
git commit -m "feat: agrega el módulo de caché de catálogos del frontend"
```

---

## Task 6: Recientes de CIE separados y por usuario

**Files:**
- Create: `resources/js/lib/cieRecents.js`
- Modify: `resources/js/components/CieSearchInput.jsx`
- Modify: `resources/views/layouts/app.blade.php`
- Test: `tests/js/cieRecents.test.js` (nuevo)

**Interfaces:**
- Produces:
  - `CIE_RECENTS_LIMIT = 10`.
  - `cieRecentsKey(userId) → 'lis.cie.recientes.<userId|anon>'`.
  - `createCieRecents({ storage, userId, limit })` → `{ key, list, add, clear }`.
  - Singleton `cieRecents`.
- Consumes: `createMemoryStorage` (Task 5).

- [ ] **Step 1: Escribir los tests que fallan**

```js
import test from 'node:test';
import assert from 'node:assert/strict';
import {
    CIE_RECENTS_LIMIT,
    cieRecentsKey,
    createCieRecents,
} from '../../resources/js/lib/cieRecents.js';
import { createMemoryStorage } from '../../resources/js/lib/catalogs.js';

test('agrega al inicio, deduplica y limita a 10', () => {
    const storage = createMemoryStorage();
    const recents = createCieRecents({ storage, userId: 7 });

    for (let i = 1; i <= 12; i++) {
        recents.add({ codigo: `Z${i}`, nombre: `CIE ${i}` });
    }
    recents.add({ codigo: 'Z12', nombre: 'CIE 12 actualizado' });

    const list = recents.list();
    assert.equal(list.length, CIE_RECENTS_LIMIT);
    assert.equal(list[0].codigo, 'Z12');
    assert.equal(list.filter((item) => item.codigo === 'Z12').length, 1);
});

test('devuelve lista vacía si el JSON está corrupto', () => {
    const storage = createMemoryStorage();
    storage.setItem(cieRecentsKey(7), '{no-json');
    assert.deepEqual(createCieRecents({ storage, userId: 7 }).list(), []);
});

test('separa los recientes por usuario', () => {
    const storage = createMemoryStorage();
    createCieRecents({ storage, userId: 1 }).add({ codigo: 'A01', nombre: 'Uno' });
    createCieRecents({ storage, userId: 2 }).add({ codigo: 'B02', nombre: 'Dos' });

    assert.equal(createCieRecents({ storage, userId: 1 }).list()[0].codigo, 'A01');
    assert.equal(createCieRecents({ storage, userId: 2 }).list()[0].codigo, 'B02');
});

test('clear borra solo los recientes del usuario', () => {
    const storage = createMemoryStorage();
    createCieRecents({ storage, userId: 1 }).add({ codigo: 'A01', nombre: 'Uno' });
    createCieRecents({ storage, userId: 2 }).add({ codigo: 'B02', nombre: 'Dos' });

    createCieRecents({ storage, userId: 1 }).clear();

    assert.deepEqual(createCieRecents({ storage, userId: 1 }).list(), []);
    assert.equal(createCieRecents({ storage, userId: 2 }).list().length, 1);
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `pnpm test`
Expected: FAIL con `ERR_MODULE_NOT_FOUND` para `cieRecents.js`.

- [ ] **Step 3: Implementar `resources/js/lib/cieRecents.js`**

- `list()` parsea el JSON, exige que sea un arreglo y filtra entradas que no tengan `codigo` y `nombre` como strings.
- `add(item)` descarta duplicados por `codigo`, inserta al inicio y corta a `limit`.
- `clear()` elimina solo la clave del usuario.
- `currentUserId()` lee `document.querySelector('meta[name="user-id"]')?.content`.
- El singleton usa `window.localStorage` o `createMemoryStorage()`.

- [ ] **Step 4: Usar el módulo en `CieSearchInput.jsx`**

- Borrar `RECENT_KEY`, `getStoredRecents`, `pushToRecents` y las llamadas a `localStorage`.
- Importar `{ cieRecents }` desde `../lib/cieRecents`.
- Reemplazar:
  - `useEffect(() => setRecents(cieRecents.list()), [])`.
  - `getStoredRecents().find(...)` → `cieRecents.list().find(...)`.
  - `pushToRecents(item); setRecents(getStoredRecents())` → `setRecents(cieRecents.add(item))`.
  - `handleFocus` → `setRecents(cieRecents.list())`.

- [ ] **Step 5: Exponer el id del usuario**

En `resources/views/layouts/app.blade.php`, dentro de `<head>`, después del `csrf-token`:

```blade
<meta name="user-id" content="{{ auth()->id() }}">
```

- [ ] **Step 6: Ejecutar y verificar**

Run: `pnpm test`
Expected: PASS (15 tests: 11 + 4).

Run: `pnpm build`
Expected: build correcto.

- [ ] **Step 7: Commit (pedir confirmación antes)**

```bash
git add resources/js/lib/cieRecents.js resources/js/components/CieSearchInput.jsx resources/views/layouts/app.blade.php tests/js/cieRecents.test.js
git commit -m "fix: separa los recientes de CIE del catálogo y por usuario"
```

---

## Task 7: Migrar los consumidores al módulo de catálogos

**Files:**
- Modify: `resources/js/components/hooks/usePersonaReferencias.jsx`
- Modify: `resources/js/components/hooks/useTablasRef.jsx`
- Modify: `resources/js/components/ConvenioForm.jsx`
- Modify: `resources/js/convenioCreate.jsx`
- Modify: `resources/js/components/setup/Setup.jsx`
- Test: `tests/js/no-legacy-storage.test.js` (nuevo)

**Interfaces:**
- Consumes: `getCatalogs()`, `loadCatalogs()`, `getCatalog()`, `invalidateCatalogs()` (Task 5).
- Produces: hooks y componentes que ya no leen `localStorage`; `Setup` invalida la caché tras activar un ítem.

- [ ] **Step 1: Escribir el test que falla**

`tests/js/no-legacy-storage.test.js`:

```js
import test from 'node:test';
import assert from 'node:assert/strict';
import { readFile } from 'node:fs/promises';
import path from 'node:path';

const root = process.cwd();

const consumidores = [
    'resources/js/components/hooks/usePersonaReferencias.jsx',
    'resources/js/components/hooks/useTablasRef.jsx',
    'resources/js/components/ConvenioForm.jsx',
    'resources/js/convenioCreate.jsx',
    'resources/js/components/setup/Setup.jsx',
    'resources/js/components/CieSearchInput.jsx',
];

test('los consumidores usan el módulo de catálogos, no localStorage', async () => {
    for (const relativo of consumidores) {
        const fuente = await readFile(path.join(root, relativo), 'utf8');
        assert.equal(fuente.includes('localStorage'), false, `${relativo} no debe usar localStorage`);
        assert.equal(fuente.includes("_data'"), false, `${relativo} no debe usar claves *_data`);
    }
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `pnpm test -- --test-name-pattern="consumidores"`
Expected: FAIL con al menos `usePersonaReferencias.jsx` y `Setup.jsx`.

- [ ] **Step 3: Migrar `usePersonaReferencias.jsx`**

```js
import { useEffect, useState } from 'react';
import { getCatalogs, loadCatalogs } from '../../lib/catalogs';

const mapReferencias = (data) => ({
    tiposDocumento: data.documentos_paciente ?? [],
    paises: data.paises ?? [],
    municipios: data.municipios ?? [],
    epsList: data.eps ?? [],
});

export const usePersonaReferencias = () => {
    const [referencias, setReferencias] = useState(() => mapReferencias(getCatalogs()));

    useEffect(() => {
        let cancelled = false;

        loadCatalogs()
            .then(({ data }) => {
                if (!cancelled) setReferencias(mapReferencias(data));
            })
            .catch(() => {});

        return () => { cancelled = true; };
    }, []);

    return referencias;
};
```

- [ ] **Step 4: Migrar `useTablasRef.jsx`**

```js
import { useEffect, useState } from 'react';
import { getCatalogs, loadCatalogs } from '../../lib/catalogs';

const mapTablas = (data) => ({
    modalidades: data.modalidad_atencion ?? [],
    finalidades: data.finalidad_consulta ?? [],
    viasIngreso: data.via_ingreso ?? [],
    tiposAfiliacion: data.tipo_afiliacion ?? [],
    servicios: data.servicios_habilitados ?? [],
    causaAtencion: data.causa_externa ?? [],
});

export const useTablasRef = () => {
    const [tablas, setTablas] = useState(() => mapTablas(getCatalogs()));

    useEffect(() => {
        let cancelled = false;

        loadCatalogs()
            .then(({ data }) => {
                if (!cancelled) setTablas(mapTablas(data));
            })
            .catch(() => {});

        return () => { cancelled = true; };
    }, []);

    return tablas;
};
```

- [ ] **Step 5: Migrar `ConvenioForm.jsx`**

- Importar `{ getCatalogs, loadCatalogs }` desde `../lib/catalogs`.
- Inicializar `paises` y `municipios` con `getCatalogs()`.
- Reemplazar el `useEffect` que leía `localStorage` por `loadCatalogs()`.
- Agregar `const [documentosState, setDocumentosState] = useState(documentos);` y, si `documentos.length === 0`, poblarlo con `data.documentos_pagador ?? []`.
- Renderizar `documentosState.map(...)` en el `<select>` de tipo de documento.

- [ ] **Step 6: Migrar `convenioCreate.jsx`**

- Borrar `documentosDefault`, `municipiosDefault` y `paisesDefault`.
- Inicializar `let documentos = [];`.
- Conservar el bloque que lee `#documentos-data` y hace `JSON.parse`.
- El fallback asíncrono de documentos queda en `ConvenioForm`.

- [ ] **Step 7: Migrar `Setup.jsx`**

- Borrar la propiedad `localStorageKey` de las 7 categorías de `initialCategorias`.
- Borrar la función `sincronizarLocalStorage`.
- Importar `{ invalidateCatalogs }` desde `../../lib/catalogs`.
- En `toggleActivarItem`, reemplazar `sincronizarLocalStorage(activa, actualizado);` por `invalidateCatalogs();` (después de validar la respuesta del servidor).

- [ ] **Step 8: Ejecutar y verificar**

Run: `pnpm test`
Expected: PASS (16 tests).

Run: `pnpm build`
Expected: build correcto.

Run: `grep -rn "localStorage" resources/js --include="*.js" --include="*.jsx"` y comprobar que solo aparecen `resources/js/lib/catalogs.js` y `resources/js/lib/cieRecents.js` (y los archivos legacy que se eliminan en Task 8).

- [ ] **Step 9: Commit (pedir confirmación antes)**

```bash
git add resources/js/components/hooks/usePersonaReferencias.jsx resources/js/components/hooks/useTablasRef.jsx resources/js/components/ConvenioForm.jsx resources/js/convenioCreate.jsx resources/js/components/setup/Setup.jsx tests/js/no-legacy-storage.test.js
git commit -m "refactor: consume los catálogos desde el módulo de caché"
```

---

## Task 8: Retirar el formulario legacy y los módulos muertos

**Files:**
- Delete: los archivos listados en "Archivos que se eliminan".
- Modify: `vite.config.js`
- Modify: `resources/views/dashboard.blade.php`
- Modify: `resources/views/sedes/select.blade.php`
- Test: `tests/js/no-legacy-storage.test.js`

**Interfaces:**
- Consumes: Tasks 5–7 (nadie vivo depende de estos módulos).
- Produces: `vite.config.js` solo con entrypoints vivos; sin `obtenerStaticos.js`.

- [ ] **Step 1: Extender el test**

Agregar a `tests/js/no-legacy-storage.test.js`:

```js
import { access } from 'node:fs/promises';

const eliminados = [
    'resources/js/obtenerStaticos.js',
    'resources/js/main.js',
    'resources/js/manejadorEventos.js',
    'resources/js/crearExamenes.js',
    'resources/js/variables.js',
    'resources/js/api.js',
    'resources/js/formularioPersona.js',
    'resources/js/crearOrdenes.js',
    'resources/js/crearPersona.js',
    'resources/js/buscarPersona.js',
    'resources/js/resultado.js',
    'resources/js/setup.js',
    'resources/js/components/hooks/useCieCups.jsx',
    'resources/js/utils/paisesLocalStorage.js',
];

test('los módulos legacy fueron retirados', async () => {
    for (const relativo of eliminados) {
        await assert.rejects(access(path.join(root, relativo)), `${relativo} no debe existir`);
    }
});

test('vite no incluye entrypoints muertos', async () => {
    const vite = await readFile(path.join(root, 'vite.config.js'), 'utf8');

    for (const entry of ['main.js', 'crearOrdenes.js', 'obtenerStaticos.js', 'crearPersona.js', 'buscarPersona.js']) {
        assert.equal(vite.includes(entry), false, `vite.config.js no debe incluir ${entry}`);
    }
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `pnpm test`
Expected: FAIL porque los archivos todavía existen.

- [ ] **Step 3: Verificar que no quedan referencias antes de borrar**

Run:

```bash
grep -rn "variables.js\|crearExamenes\|manejadorEventos\|obtenerStaticos\|crearOrdenes\|crearPersona\|buscarPersona\|resultado.js\|setup.js\|useCieCups\|paisesLocalStorage" resources app routes tests
```

Expected: solo referencias en los propios archivos a eliminar, en `vite.config.js`, en las dos vistas con `@vite('resources/js/obtenerStaticos.js')` y en `docs/`. Si aparece una referencia viva en un componente, **detenerse y reportar**.

- [ ] **Step 4: Borrar archivos y directorios**

```bash
rm resources/js/obtenerStaticos.js resources/js/main.js resources/js/manejadorEventos.js resources/js/crearExamenes.js resources/js/variables.js resources/js/api.js resources/js/formularioPersona.js resources/js/crearOrdenes.js resources/js/crearPersona.js resources/js/buscarPersona.js resources/js/resultado.js resources/js/setup.js
rm resources/js/components/hooks/useCieCups.jsx
rm resources/js/utils/paisesLocalStorage.js
rm -r resources/js/ejemplos
rm resources/views/diagnosticos/create.blade.php resources/views/cups/create.blade.php
```

- [ ] **Step 5: Limpiar `vite.config.js`**

Dejar como entradas:

```js
'resources/css/app.css',
'resources/js/app.js',
'resources/js/app.jsx',
'resources/js/appOrden.jsx',
'resources/js/appPersona.jsx',
'resources/js/ExamenDetail.jsx',
'resources/js/components/guest/Typewriter.jsx',
'resources/js/convenioCreate.jsx',
```

- [ ] **Step 6: Quitar el cargador legacy de las vistas**

- En `resources/views/dashboard.blade.php`, borrar `@vite('resources/js/obtenerStaticos.js')`.
- En `resources/views/sedes/select.blade.php`, borrar `@vite('resources/js/obtenerStaticos.js')`.

- [ ] **Step 7: Ejecutar y verificar**

Run: `pnpm test`
Expected: PASS (18 tests).

Run: `pnpm build`
Expected: build correcto.

Run: `php artisan test --compact`
Expected: `0 failed`, `8 skipped`.

- [ ] **Step 8: Commit (pedir confirmación antes)**

```bash
git add -A resources/js resources/views/dashboard.blade.php resources/views/sedes/select.blade.php resources/views/diagnosticos/create.blade.php resources/views/cups/create.blade.php vite.config.js tests/js/no-legacy-storage.test.js
git commit -m "chore: retira el formulario legacy y los módulos muertos"
```

---

## Task 9: Job de JavaScript en CI

**Files:**
- Modify: `.github/workflows/tests.yml`

**Interfaces:**
- Consumes: `package.json` con `"test": "node --test tests/js"` (Task 5).
- Produces: job `javascript` que corre `pnpm install --frozen-lockfile`, `pnpm test` y `pnpm build`.

- [ ] **Step 1: Agregar el job**

Después del job `tests` existente:

```yaml
  javascript:
    runs-on: ubuntu-latest

    name: JavaScript

    steps:
      - name: Checkout code
        uses: actions/checkout@v4

      - name: Setup pnpm
        uses: pnpm/action-setup@v4
        with:
          version: 11.5.1

      - name: Setup Node
        uses: actions/setup-node@v4
        with:
          node-version: 24
          cache: pnpm

      - name: Install dependencies
        run: pnpm install --frozen-lockfile

      - name: Run tests
        run: pnpm test

      - name: Build assets
        run: pnpm build
```

- [ ] **Step 2: Verificar localmente**

Run:

```bash
pnpm test
pnpm build
```

Expected: ambos comandos terminan con código 0.

- [ ] **Step 3: Verificar el YAML**

Run:

```bash
grep -n "javascript:" .github/workflows/tests.yml
grep -n "pnpm test\|pnpm build" .github/workflows/tests.yml
```

Expected: `javascript:` aparece al mismo nivel que `tests:` dentro de `jobs`, y los dos comandos de pnpm aparecen en el job nuevo.

- [ ] **Step 4: Commit (pedir confirmación antes)**

```bash
git add .github/workflows/tests.yml
git commit -m "ci: corre los tests y el build de JavaScript"
```

---

## Task 10: Documentación y runbook

**Files:**
- Modify: `docs/INFORME_CACHE_LOCALSTORAGE.md`
- Modify: `docs/CHECKLIST_DEUDA_TECNICA.md`
- Modify: `docs/DESPLIEGUE_INSTANCIAS.md`

**Interfaces:**
- Consumes: todo lo anterior.
- Produces: documentación que refleja el estado real y el paso de `cache:clear` en despliegues.

- [ ] **Step 1: Actualizar el informe**

Agregar al final de `docs/INFORME_CACHE_LOCALSTORAGE.md`:

```markdown
## 10. Estado de implementación (2026-11-05)

Se implementó la ruta **A + B + D**:

- **A:** módulo único `resources/js/lib/catalogs.js` con versión de esquema,
  TTL de revalidación, escritura atómica y manejo de cuota.
- **B:** CUPS y CIE10 ya no viajan en `/api/static-data-for-frontend` ni se
  guardan en `localStorage`; se resuelven con `/api/cups/buscar` y
  `/api/cie10/buscar`.
- **D:** `Cache::remember` + `ETag`/304 + endpoint dentro de `auth:sanctum`
  con `throttle:60,1`.

Hallazgos cerrados: H-01 a H-12, con la salvedad de H-12 (los recientes de
CIE quedan separados por usuario, no se borran al cerrar sesión).
```

- [ ] **Step 2: Marcar M-07 en el checklist**

En `docs/CHECKLIST_DEUDA_TECNICA.md`, cambiar la fila `M-07` a `[x]` y anotar:

```markdown
**Hecho 2026-11-05.** `/api/static-data-for-frontend` se cachea con
`Cache::remember` + `ETag`/304 y exige autenticación; el frontend usa un
único módulo de caché y ya no guarda CUPS/CIE en `localStorage`.
```

- [ ] **Step 3: Agregar el paso de caché al runbook**

En `docs/DESPLIEGUE_INSTANCIAS.md`, en la sección de despliegue, después de `php artisan migrate --force` y de los seeders, agregar:

```bash
# Los catálogos se cachean 1 hora. Si el despliegue cambia catálogos por
# seeders o SQL directo (no por la UI), hay que invalidar la caché.
php artisan cache:clear
```

Agregar también una nota:

```markdown
`CACHE_STORE` debe ser propio de cada instancia. Nunca compartir la misma
base de datos ni el mismo servidor de caché entre dos clientes.
```

- [ ] **Step 4: Verificar**

Run:

```bash
grep -n "M-07" docs/CHECKLIST_DEUDA_TECNICA.md
grep -n "cache:clear" docs/DESPLIEGUE_INSTANCIAS.md
grep -n "Estado de implementación" docs/INFORME_CACHE_LOCALSTORAGE.md
```

Expected: las tres búsquedas encuentran la línea correspondiente.

- [ ] **Step 5: Commit (pedir confirmación antes)**

```bash
git add docs/INFORME_CACHE_LOCALSTORAGE.md docs/CHECKLIST_DEUDA_TECNICA.md docs/DESPLIEGUE_INSTANCIAS.md
git commit -m "docs: documenta la implementación de la caché de catálogos"
```

---

## Verificación final

Antes de reportar la implementación como terminada:

```bash
php artisan test --compact
pnpm test
pnpm build
grep -rn "localStorage" resources/js --include="*.js" --include="*.jsx"
```

Criterios:

- `php artisan test --compact`: `0 failed`, `8 skipped`, resto `passed`.
- `pnpm test`: todos los tests en verde.
- `pnpm build`: código 0.
- `grep`: solo aparecen `resources/js/lib/catalogs.js` y `resources/js/lib/cieRecents.js`.
- No hay CUPS/CIE en `localStorage` ni en el payload del endpoint.
- El endpoint responde 401 sin sesión, 200 con sesión y 304 con el `ETag` correcto.

Si algo falla, **no hacer commit**: reportar el fallo con el comando y la salida exacta.

# Despliegue multi-cliente por instancia — Plan de implementación

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Desplegar la misma aplicación en dos instancias aisladas (un cliente por instancia) sobre una cuenta cPanel compartida, sincronizando el código por Git, sin tocar controladores ni el modelo de datos.

**Architecture:** Un repositorio y un tag; dos clones en `~/apps/<cliente>`, cada uno con su `.env`, su base de datos y su `storage/`. El aislamiento es lógico y se refuerza con tres guardas en código: verificación de hostname por instancia, verificación de configuración por comando, y una prueba que impide rutas absolutas en `.cpanel.yml`.

**Tech Stack:** Laravel 13.38, PHP 8.5, Pest, Spatie Permission, cPanel Git Version Control, MySQL 8.4.

**Spec:** `docs/superpowers/specs/2026-10-30-multitenant-design.md`

## Global Constraints

- `composer.json` exige `"php": "^8.5"`. Si el hosting no ofrece 8.5, bajar a `"^8.3"` (Laravel 13 lo soporta) y registrar el cambio en el spec.
- Un solo repositorio, una sola rama. Cada instancia despliega un **tag explícito**.
- Prohibido hardcodear nombres de clientes, dominios o rutas absolutas en el código o en `.cpanel.yml`.
- `.cpanel.yml` solo admite **rutas relativas**.
- La suite base tiene **43 tests pasando y 24 fallando**. Los tests nuevos deben pasar y el número de fallos no debe aumentar.
- Sin `APP_INSTANCE` definido, la aplicación se comporta exactamente como hoy (desarrollo y tests).
- Las migraciones **nunca** se ejecutan como efecto de un deploy automático.

## Review Focus

1. **Host desconocido o falsificado** → debe responder 503 y no servir ninguna página.
2. **`APP_INSTANCE` ausente o con error de tipeo en producción** → debe fallar cerrado (503), no abrir.
3. **`SESSION_DOMAIN` con punto inicial** → la cookie cruza instancias y mezcla sesiones.
4. **Instancia recién creada y sin catálogo** → el login debe funcionar, no lanzar 500.
5. **Entorno local y tests sin `APP_INSTANCE`** → la suite existente no debe romperse.

---

## Task 0: Spike S0 — resolver `vendor/`

Bloquea todo lo demás: sin `vendor/` en el servidor la aplicación no arranca, y el commit `041d1fa4` lo sacó de git.

**Files:**
- Modify: `docs/superpowers/specs/2026-10-30-multitenant-design.md` (§7, registrar el resultado)
- Possibly modify: `.gitignore`, `.cpanel.yml`

**Interfaces:**
- Consumes: nada
- Produces: decisión documentada entre **A** (Composer en el deploy) y **B** (volver a versionar `vendor/`), que determina el contenido de Task 5

- [ ] **Step 1: Verificar acceso a Terminal/SSH en cPanel**

Entrar a cPanel y comprobar si existe la sección *Terminal* o si hay acceso SSH.
Anotar el resultado: `SSH = sí / no`.

- [ ] **Step 2: Si hay Terminal, verificar PHP CLI y Composer**

```bash
php -v
composer -V
```

Esperado: `PHP 8.5.x` (o la versión máxima disponible) y una versión de Composer.
Anotar la versión exacta de PHP: es el dato P1 del spec.

- [ ] **Step 3: Probar la opción A (Composer en el deploy)**

En local, generar `composer.phar` en la raíz del repo:

```bash
composer self-update --stable
php -r "copy('https://getcomposer.org/installer','/tmp/ci.php');"
php /tmp/ci.php --install-dir=. --filename=composer.phar
```

Subirlo por File Manager, y desde Terminal:

```bash
cd ~/apps/prueba
php composer.phar install --no-dev --optimize-autoloader
```

Esperado: termina sin error y `vendor/autoload.php` existe.
Si falla por memoria o tiempo límite → **opción A descartada**.

- [ ] **Step 4: Decidir y documentar**

- Si A funcionó → opción **A**. Añadir `composer.phar` al repo.
- Si A falló → opción **B**: revertir el commit de `vendor/`.

```bash
git revert --no-commit 041d1fa4
git commit -m "chore: volver a versionar vendor para despliegue en cPanel"
```

Registrar la decisión y la versión de PHP en `docs/superpowers/specs/2026-10-30-multitenant-design.md` §7.

- [ ] **Step 5: Commit**

```bash
git add -A
git commit -m "chore: resolver vendor para despliegue en cPanel (spike S0)"
```

**Verificación:** un `composer install --no-dev` limpio (o un checkout con `vendor/` versionado) deja la app arrancando con `php artisan --version`.

---

## Task 1: Guarda de instancia por hostname

Impide que una instancia sirva peticiones dirigidas al dominio de la otra.

**Files:**
- Create: `config/instancias.php`
- Create: `app/Http/Middleware/EnsureInstance.php`
- Modify: `bootstrap/app.php`
- Test: `tests/Feature/Instancias/EnsureInstanceTest.php`

**Interfaces:**
- Consumes: nada
- Produces:
  - `config('instancias.actual'): ?string` — valor de `APP_INSTANCE`
  - `config('instancias.hosts'): string[]` — valor de `APP_HOSTS` separado por comas
  - `App\Http\Middleware\EnsureInstance::handle(Request $request, Closure $next): Response`

- [ ] **Step 1: Escribir el test que falla**

```php
<?php

beforeEach(function () {
    config(['instancias.actual' => null, 'instancias.hosts' => []]);
});

test('sin instancia configurada no bloquea en entorno local', function () {
    $this->get('http://biotek.test/login')->assertOk();
});

test('en produccion sin dominios configurados responde 503', function () {
    $this->app['env'] = 'production';
    config(['instancias.actual' => 'biotek', 'instancias.hosts' => []]);

    $this->get('http://biotek.test/login')->assertStatus(503);
});

test('acepta el dominio declarado por la instancia', function () {
    config(['instancias.actual' => 'biotek', 'instancias.hosts' => ['biotek.test']]);

    $this->get('http://biotek.test/login')->assertOk();
});

test('rechaza un dominio ajeno', function () {
    config(['instancias.actual' => 'biotek', 'instancias.hosts' => ['biotek.test']]);

    $this->get('http://sanpablo.test/login')->assertStatus(503);
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `php artisan test tests/Feature/Instancias/EnsureInstanceTest.php`
Expected: FAIL — los tests de 503 reciben 200 porque la guarda no existe.

- [ ] **Step 3: Crear `config/instancias.php`**

```php
<?php

return [

    'actual' => env('APP_INSTANCE'),

    'hosts' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('APP_HOSTS', ''))
    ))),

    'cliente' => [
        'razon_social' => env('CLIENTE_RAZON_SOCIAL', 'Laboratorio Demo'),
        'nit' => env('CLIENTE_NIT', '000000000-0'),
        'sede' => env('CLIENTE_SEDE', 'Sede Principal'),
        'codigo_prestador' => env('CLIENTE_CODIGO_PRESTADOR', '0000000000'),
        'admin_email' => env('CLIENTE_ADMIN_EMAIL', 'admin@ejemplo.test'),
        'admin_password' => env('CLIENTE_ADMIN_PASSWORD', 'password'),
    ],

];
```

- [ ] **Step 4: Crear `app/Http/Middleware/EnsureInstance.php`**

Regla: sin `APP_INSTANCE` no hace nada; con instancia pero sin dominios, solo falla en producción; con dominios, exige coincidencia exacta de host.

- [ ] **Step 5: Registrar la guarda en `bootstrap/app.php`**

Añadir el middleware al grupo `web` y al grupo `api`, conservando lo que ya existe en el grupo `api`:

```php
$middleware->web(append: [
    \App\Http\Middleware\EnsureInstance::class,
]);

$middleware->api(append: [
    \App\Http\Middleware\EnsureInstance::class,
]);
```

- [ ] **Step 6: Ejecutar el test**

Run: `php artisan test tests/Feature/Instancias/EnsureInstanceTest.php`
Expected: PASS (4 tests).

- [ ] **Step 7: Verificar que no rompe la suite existente**

Run: `php artisan test`
Expected: **43 passed, 24 failed** — mismo conteo que la línea base.

- [ ] **Step 8: Commit**

```bash
git add config/instancias.php app/Http/Middleware/EnsureInstance.php bootstrap/app.php tests/Feature/Instancias/EnsureInstanceTest.php
git commit -m "feat: guarda de instancia por hostname"
```

---

## Task 2: Comando `instancia:verificar` y `.env.example`

Detecta la configuración equivocada antes de que un cliente entre a la instancia del otro.

**Files:**
- Create: `app/Console/Commands/VerificarInstancia.php`
- Modify: `.env.example`
- Test: `tests/Feature/Instancias/VerificarInstanciaTest.php`

**Interfaces:**
- Consumes: `config('instancias.actual')`, `config('instancias.hosts')` (Task 1)
- Produces: comando `instancia:verificar` → exit code `0` si todo pasa, `1` si algo falla

**Checks que implementa** (todos leen de `config()`, nunca de `env()`):

| Check | Falla cuando |
|---|---|
| `APP_INSTANCE` | está vacío |
| `APP_KEY` | está vacío o es el placeholder |
| `APP_HOSTS` | está vacío |
| `APP_URL` | su host no está en `APP_HOSTS` |
| `SESSION_DOMAIN` | no está vacío (debe ser host-only) |
| `SESSION_COOKIE` | no contiene el valor de `APP_INSTANCE` |
| `APP_DEBUG` | es `true` con `APP_ENV=production` |

- [ ] **Step 1: Escribir el test que falla**

```php
<?php

function configValida(): array
{
    return [
        'instancias.actual' => 'biotek',
        'instancias.hosts' => ['biotek.test'],
        'app.url' => 'https://biotek.test',
        'app.key' => 'base64:'.base64_encode(random_bytes(32)),
        'session.domain' => null,
        'session.cookie' => 'biotek_session',
        'app.debug' => false,
    ];
}

test('falla si no hay instancia configurada', function () {
    config(['instancias.actual' => null]);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si el dominio de sesion es compartido', function () {
    config(configValida());
    config(['session.domain' => '.dinamylab.test']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si la cookie no lleva el nombre de la instancia', function () {
    config(configValida());
    config(['session.cookie' => 'laravel_session']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('falla si APP_URL no esta en APP_HOSTS', function () {
    config(configValida());
    config(['app.url' => 'https://otro.test']);

    $this->artisan('instancia:verificar')->assertExitCode(1);
});

test('pasa con una configuracion correcta', function () {
    config(configValida());

    $this->artisan('instancia:verificar')->assertExitCode(0);
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `php artisan test tests/Feature/Instancias/VerificarInstanciaTest.php`
Expected: FAIL — el comando no existe.

- [ ] **Step 3: Crear `app/Console/Commands/VerificarInstancia.php`**

Firma: `class VerificarInstancia extends Command` con `protected $signature = 'instancia:verificar';`.
Recorre los checks de la tabla, imprime `OK`/`FALLA` por línea con `$this->line()`, acumula fallos y termina con `return self::FAILURE` si hubo alguno, `self::SUCCESS` si no.

- [ ] **Step 4: Ejecutar el test**

Run: `php artisan test tests/Feature/Instancias/VerificarInstanciaTest.php`
Expected: PASS (5 tests).

- [ ] **Step 5: Actualizar `.env.example`**

Añadir, documentados con comentario, los bloques de instancia:

```
APP_INSTANCE=
APP_HOSTS=

CLIENTE_RAZON_SOCIAL=
CLIENTE_NIT=
CLIENTE_SEDE=
CLIENTE_CODIGO_PRESTADOR=
CLIENTE_ADMIN_EMAIL=
CLIENTE_ADMIN_PASSWORD=
```

Y fijar en el mismo archivo `SESSION_DOMAIN=` (vacío) y `SESSION_COOKIE=${APP_INSTANCE}_session`.

- [ ] **Step 6: Verificar el comando contra el `.env` local**

Run: `php artisan instancia:verificar`
Expected: exit code `1` (el `.env` local no tiene `APP_INSTANCE`), con el listado de qué falta. Es el comportamiento correcto.

- [ ] **Step 7: Commit**

```bash
git add app/Console/Commands/VerificarInstancia.php .env.example tests/Feature/Instancias/VerificarInstanciaTest.php
git commit -m "feat: comando instancia:verificar y plantilla de entorno"
```

---

## Task 3: Parametrizar el título de la aplicación

Elimina el último texto de marca fijo en las vistas.

**Files:**
- Modify: `resources/views/layouts/app.blade.php:8`
- Test: `tests/Feature/Instancias/BrandingTest.php`

**Interfaces:**
- Consumes: `config('app.name')` (ya existe en `config/app.php:16`)
- Produces: nada

- [ ] **Step 1: Escribir el test que falla**

```php
<?php

test('la vista base usa el nombre configurado y no un titulo fijo', function () {
    $blade = file_get_contents(resource_path('views/layouts/app.blade.php'));

    expect($blade)->toContain("config('app.name')")
        ->and($blade)->not->toContain('Laboratorio  Clinico');
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `php artisan test tests/Feature/Instancias/BrandingTest.php`
Expected: FAIL — el archivo contiene `'Laboratorio  Clinico'`.

- [ ] **Step 3: Cambiar la línea 8**

De `{{ $title ?? 'Laboratorio  Clinico' }}` a `{{ $title ?? config('app.name') }}`.

- [ ] **Step 4: Ejecutar el test**

Run: `php artisan test tests/Feature/Instancias/BrandingTest.php`
Expected: PASS.

- [ ] **Step 5: Commit**

```bash
git add resources/views/layouts/app.blade.php tests/Feature/Instancias/BrandingTest.php
git commit -m "refactor: titulo de la app desde config"
```

---

## Task 4: Separar catálogo base, datos de cliente y datos de demo

Una instancia nueva necesita catálogos comunes, pero los datos del cliente salen de su `.env`, no del código.

**Files:**
- Create: `database/seeders/RolesYPermisosSeeder.php`
- Create: `database/seeders/CatalogoBaseSeeder.php`
- Create: `database/seeders/ClienteSeeder.php`
- Create: `database/seeders/DemoSeeder.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Test: `tests/Feature/Instancias/SeedersTest.php`

**Interfaces:**
- Consumes: `config('instancias.cliente.*')` (Task 1)
- Produces:
  - `RolesYPermisosSeeder::run(): void` — idempotente
  - `CatalogoBaseSeeder::run(): void` — llama a `RolesYPermisosSeeder` y a los seeders de catálogo
  - `ClienteSeeder::run(): void` — crea exactamente 1 `Empresa`, 1 `Sede` y 1 usuario admin
  - `DemoSeeder::run(): void` — el contenido actual de `DatabaseSeeder`

- [ ] **Step 1: Escribir el test que falla**

```php
<?php

use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\ClienteSeeder;
use Database\Seeders\RolesYPermisosSeeder;
use Spatie\Permission\Models\Role;

test('el seeder de roles es idempotente', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(RolesYPermisosSeeder::class);

    expect(Role::count())->toBe(5);
});

test('el seeder de cliente crea una sola empresa y su sede', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(ClienteSeeder::class);

    expect(Empresa::count())->toBe(1)
        ->and(Sede::count())->toBe(1)
        ->and(User::where('email', config('instancias.cliente.admin_email'))->exists())->toBeTrue();
});

test('el admin sembrado puede iniciar sesion', function () {
    $this->seed(RolesYPermisosSeeder::class);
    $this->seed(ClienteSeeder::class);

    $this->post('/login', [
        'email' => config('instancias.cliente.admin_email'),
        'password' => config('instancias.cliente.admin_password'),
    ])->assertRedirect(route('dashboard'));
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `php artisan test tests/Feature/Instancias/SeedersTest.php`
Expected: FAIL — los seeders no existen.

- [ ] **Step 3: Crear `RolesYPermisosSeeder`**

Extraer de `DatabaseSeeder` **solo** los `Role::create` / `Permission::create` y la asignación de permisos a roles, cambiando `create` por `firstOrCreate` para que sea idempotente. Los 5 roles son: `admin`, `agente`, `prestador`, `paciente`, `contable`.

**No** mover aquí las asignaciones de rol a usuarios (`$admin->assignRole(...)`): esas pertenecen a `DemoSeeder` y `ClienteSeeder`.

- [ ] **Step 4: Crear `CatalogoBaseSeeder`**

Llama a `RolesYPermisosSeeder` y luego, en este orden: `PaisSeeder`, `MunicipioSeeder`, `TipoDocumentoSeeder`, `EPSsedeer`, `FinalidadSeeder`, `CUPSeeder`, `RelacionesCupSeeder`, `AplicabilidadSexoCupSeeder`, `ModalidadAtencionSeeder`, `ServicioHabilitadoSeeder`, `ViaIngresoSeeder`, `CausaExternaSeeder`, `MediosDePagoSeeder`, `DefaultOptionSeeder`, `SetupSeeder`, `ExamenSeeder`.

- [ ] **Step 5: Crear `ClienteSeeder`**

Crea una `Empresa` con `config('instancias.cliente.razon_social')` y `config('instancias.cliente.nit')`, una `Sede` con `config('instancias.cliente.sede')` y `config('instancias.cliente.codigo_prestador')`, y un `User` con `config('instancias.cliente.admin_email')` / `admin_password` al que asigna el rol `admin`. Usa `firstOrCreate` en los tres.

No usar `contacto_id`: está en `$fillable` de `App\Models\Empresa` pero la columna no existe en la tabla.

- [ ] **Step 6: Crear `DemoSeeder` y ajustar `DatabaseSeeder`**

Mover a `DemoSeeder::run()` **el resto** del contenido actual de `DatabaseSeeder::run()`: los usuarios de demo, sus `assignRole` y la llamada a `EmpresaSeeder`. `DatabaseSeeder::run()` queda como:

```php
public function run(): void
{
    $this->call(DemoSeeder::class);
}
```

- [ ] **Step 7: Ejecutar el test**

Run: `php artisan test tests/Feature/Instancias/SeedersTest.php`
Expected: PASS (3 tests).

- [ ] **Step 8: Verificar que el seeding local sigue funcionando**

Run: `php artisan migrate:fresh --seed`
Expected: termina sin error y crea los datos de demo.

- [ ] **Step 9: Commit**

```bash
git add database/seeders tests/Feature/Instancias/SeedersTest.php
git commit -m "refactor: separar catalogo base, datos de cliente y datos de demo"
```

---

## Task 5: `.cpanel.yml` y runbook de despliegue

El archivo de deploy es compartido por ambos clones: una ruta absoluta haría que un cliente despliegue el código del otro.

**Files:**
- Create: `.cpanel.yml`
- Create: `docs/DESPLIEGUE_INSTANCIAS.md`
- Test: `tests/Feature/Instancias/CpanelDeployTest.php`

**Interfaces:**
- Consumes: la decisión de Task 0 (Composer o `vendor/` versionado)
- Produces: `.cpanel.yml` sin rutas absolutas

- [ ] **Step 1: Escribir el test que falla**

```php
<?php

test('el archivo de despliegue no contiene rutas absolutas', function () {
    $yaml = file_get_contents(base_path('.cpanel.yml'));

    expect($yaml)->not->toMatch('#/home/#')
        ->and($yaml)->not->toMatch('#~/\.?/#');
});

test('el archivo de despliegue no ejecuta migraciones', function () {
    $yaml = file_get_contents(base_path('.cpanel.yml'));

    expect($yaml)->not->toContain('migrate');
});
```

- [ ] **Step 2: Ejecutar y verificar que falla**

Run: `php artisan test tests/Feature/Instancias/CpanelDeployTest.php`
Expected: FAIL — `.cpanel.yml` no existe.

- [ ] **Step 3: Crear `.cpanel.yml`**

Solo rutas relativas y sin `migrate`. Las tareas corren con el directorio de trabajo en la raíz del clone:

```yaml
---
deployment:
  tasks:
    - php artisan config:cache
    - php artisan route:cache
    - php artisan view:cache
```

Si Task 0 eligió la opción **A**, añadir como primera tarea `php composer.phar install --no-dev --optimize-autoloader`.
Si eligió **B**, no añadir nada.

- [ ] **Step 4: Ejecutar el test**

Run: `php artisan test tests/Feature/Instancias/CpanelDeployTest.php`
Expected: PASS (2 tests).

- [ ] **Step 5: Escribir `docs/DESPLIEGUE_INSTANCIAS.md`**

Debe cubrir:

1. **Preparación por instancia:** crear BD y usuario MySQL; crear addon domain con document root en `<app>/public`; clonar el repo en `~/apps/<cliente>`; copiar `.env.example` a `.env` y completar; `php artisan key:generate`; `php artisan storage:link`; `php artisan instancia:verificar`; `php artisan db:seed --class=CatalogoBaseSeeder`; `php artisan db:seed --class=ClienteSeeder`.
2. **Despliegue de una versión:** tag → *Deploy HEAD Commit* en una sola instancia → verificar `/up`, `/login` y un listado → repetir en la otra.
3. **Migraciones:** paso manual y explícito, nunca en `.cpanel.yml`.
4. **Cron:** cola (`queue:work --stop-when-empty --max-time=50`) solo si `QUEUE_CONNECTION` no es `sync`; backup diario con `mysqldump` descargado fuera del hosting.
5. **Disco compartido:** revisar la cuota de la cuenta y activar la alerta de uso del hosting; ambas instancias comparten el mismo disco, así que un llenado tumba a las dos.
6. **Regla de release:** ambas instancias en el mismo tag; si una se atrasa, se registra como deuda.

- [ ] **Step 6: Verificar el runbook con una instancia de prueba**

Ejecutar el paso 1 completo sobre una instancia desechable.
Esperado: `/up` responde 200 y `instancia:verificar` sale con código 0.

- [ ] **Step 7: Commit**

```bash
git add .cpanel.yml docs/DESPLIEGUE_INSTANCIAS.md tests/Feature/Instancias/CpanelDeployTest.php
git commit -m "feat: despliegue por git en cPanel y runbook"
```

---

## Task 6: Onboarding de los dos clientes

Ejecutar el runbook dos veces y verificar los criterios de aceptación del spec.

**Files:**
- Modify: `docs/DESPLIEGUE_INSTANCIAS.md` (registrar dominios, instancias y fechas)

**Interfaces:**
- Consumes: todo lo anterior
- Produces: dos instancias operativas

- [ ] **Step 1: Desplegar la instancia del cliente 1**

Seguir `docs/DESPLIEGUE_INSTANCIAS.md` §1 completo.
Esperado: `php artisan instancia:verificar` → código 0.

- [ ] **Step 2: Verificar el aislamiento del cliente 1**

- `/up` → 200
- `/login` → 200 y el título muestra el `APP_NAME` de esa instancia
- Login con el admin sembrado → redirige a `/dashboard`
- Acceder por el dominio del cliente 2 → **503**

- [ ] **Step 3: Desplegar la instancia del cliente 2**

Repetir el Step 1 con su propio `.env`, BD y dominio.
Esperado: `php artisan instancia:verificar` → código 0.

- [ ] **Step 4: Verificar el aislamiento cruzado**

- El admin del cliente 1 no existe en la instancia del cliente 2.
- Una cookie de sesión del cliente 1 no autentica en el cliente 2.
- `storage/app/public` de cada instancia contiene solo sus archivos.
- Desplegar un tag nuevo en el cliente 1 deja el cliente 2 sin cambios.

- [ ] **Step 5: Registrar el onboarding**

Anotar en `docs/DESPLIEGUE_INSTANCIAS.md` los dominios, la BD, la fecha y el tag desplegado de cada instancia.

- [ ] **Step 6: Commit**

```bash
git add docs/DESPLIEGUE_INSTANCIAS.md
git commit -m "docs: registrar onboarding de instancias"
```

**Verificación:** se cumplen los 7 criterios de aceptación de §12 del spec.

---

## Cierre

- [ ] `php artisan test` → **58 passed**, **24 failed** (los mismos 24 de la línea base)
- [ ] `php artisan instancia:verificar` pasa en las dos instancias
- [ ] Ningún archivo de código contiene nombres de clientes, dominios ni rutas absolutas
- [ ] El spec y el runbook reflejan el estado real

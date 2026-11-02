# Diseño: despliegue multi-cliente por instancia (cPanel + Git)

**Fecha:** 2026-10-30
**Estado:** Propuesta — pendiente de aprobación
**Alcance:** Infraestructura y configuración. No modifica el modelo de datos ni los controladores.

---

## 1. Contexto

DinamyLAB es una aplicación Laravel 13 para laboratorio clínico. Se van a incorporar
**2 clientes urgentes** y el desarrollo/corrección continúa en producción.

Se evaluaron tres modelos de multi-tenancy:

| Modelo | Estimación | Decisión |
|---|---|---|
| Base compartida + `empresa_id` + global scopes | 6–9 semanas | ❌ Descartado |
| Una base de datos por cliente (mismo despliegue) | 8–12 semanas | ❌ Descartado |
| **Una instancia completa por cliente** | **2,5–3 semanas** | ✅ **Elegido** |

**Razón:** elimina la fase más costosa y riesgosa (auditar 44 controladores y ~35 tablas),
no toca el código de negocio, y con datos en cero no hay migración que hacer.

---

## 2. Decisiones tomadas

| # | Decisión | Fecha |
|---|---|---|
| D1 | Aislamiento por **instancia completa** por cliente | 2026-10-30 |
| D2 | **Un solo repositorio**, una rama, tags. Cada instancia despliega un tag explícito | 2026-10-30 |
| D3 | Hosting: **una cuenta cPanel compartida**, dos directorios y dos bases de datos | 2026-10-30 |
| D4 | Sincronización de código vía **cPanel Git Version Control** (dos clones del mismo repo) | 2026-10-30 |
| D5 | Cada instancia tiene **exactamente 1 `Empresa`** y N `Sedes` | 2026-10-30 |
| D6 | La base de datos de producción está **en ceros** → sin migración de datos | 2026-10-30 |

---

## 3. Lo que NO se toca

Esta decisión evita trabajo en:

- **44 controladores** — sin cambios.
- **~45 tablas de dominio** — sin `empresa_id` nuevo, sin global scopes.
- **24 restricciones `unique()`** — siguen siendo globales *dentro de cada instancia*, que es lo correcto: cada cliente numera su propia facturación.
- **`session('sede')`** (7 archivos) — sigue funcionando.
- **Spatie sin `teams`**, **`User hasOne Empleado`** — válidos tal como están.
- **156 vistas** — sin cambios.

Esto es lo que hace la opción 3× más barata que las alternativas.

---

## 4. Topología

```
/home/USUARIO/
├── apps/
│   ├── biotek/                  ← git clone del repo (rama upgrade/laravel-13, tag X)
│   │   ├── .env                 ← NO versionado, propio de la instancia
│   │   ├── public/              ← document root de biotek.dominio.com
│   │   ├── storage/             ← NO versionado, propio
│   │   └── vendor/              ← NO versionado (ver §7)
│   └── sanpablo/                ← git clone del mismo repo, mismo tag
│       ├── .env
│       ├── public/              ← document root de sanpablo.dominio.com
│       └── storage/
├── repositorios/                ← (opcional) área de cPanel Git
└── ...

MySQL: biotek_db (usuario biotek_u)  |  sanpablo_db (usuario sanpablo_u)
```

**Decisión clave:** el clone **es** el directorio de la aplicación. El document root
apunta a `<app>/public`. No hay paso de "copiar a docroot", lo que evita duplicar
14.000 archivos de `vendor` en cada deploy.

---

## 5. Capas: qué se aísla y qué no

| Capa | Aislada | Cómo |
|---|---|---|
| Código | ❌ (a propósito) | Mismo repo, mismo tag |
| `.env` | ✅ | Un archivo por instancia, fuera de git (ya ignorado) |
| `APP_KEY` | ✅ | Distinta por instancia |
| Base de datos | ✅ | BD + usuario MySQL propios |
| `storage/` | ✅ | Directorio propio por instancia |
| `vendor/` | ✅ | Instalado por instancia (ver §7) |
| Sesiones / cache | ✅ | Driver `file` o `database` por instancia |
| Logs | ✅ | `storage/logs` propio |
| Backups | ✅ | Por BD, fuera del hosting |
| Branding | ✅ | `APP_NAME` + tabla `sedes` (logo, nombre). Ya es data-driven |
| Roles / usuarios | ✅ (gratis) | Consecuencia de la BD separada |
| **Usuario de sistema** | ❌ | El mismo en ambas instancias |
| **Pool PHP / CPU / RAM** | ❌ | Compartido |
| **Disco / cuota** | ❌ | Compartido |
| **Servidor MySQL** | ⚠️ | Mismo motor, BDs separadas |

El aislamiento es **lógico**, no de sistema. Es aceptable para 2 clientes, pero exige
las mitigaciones de §6.

---

## 6. Riesgos y mitigaciones

### R1 — `.env` equivocado (riesgo alto)

Dos directorios idénticos. Un copiar-pegar y el cliente A apunta a la BD del cliente B.

**Mitigación obligatoria:** variable `APP_INSTANCE` y verificación al arrancar.
Si el hostname de la petición no corresponde a la instancia declarada, la aplicación
responde 503 y registra el incidente.

```php
// bootstrap/app.php (o middleware EnsureInstance)
$declarada = config('app.instance');            // APP_INSTANCE=biotek
$permitidos = config("instancias.$declarada");  // hostnames válidos
abort_unless(in_array($request->getHost(), $permitidos, true), 503);
```

**Criterio de aceptación:** un test que verifique que el guard rechaza un host ajeno.

### R2 — Cookie de sesión compartida (riesgo alto)

Si ambos usan subdominios del mismo dominio y `SESSION_DOMAIN=.dominio.com`,
el navegador envía la cookie a las dos instancias.

**Mitigación obligatoria:**

- `SESSION_DOMAIN=null` (cookie host-only) en ambas instancias.
- `APP_KEY` distintas (red de seguridad: el cifrado falla entre instancias).
- `SESSION_COOKIE` con nombre distinto por instancia (defensa adicional).

### R3 — `.cpanel.yml` compartido con rutas absolutas (riesgo alto)

`.cpanel.yml` está versionado, por lo que **ambos clones reciben el mismo archivo**.
Si contiene una ruta absoluta (`/home/USUARIO/apps/biotek`), el deploy de `sanpablo`
sobrescribiría `biotek`.

**Mitigación:** usar únicamente rutas **relativas**, asumiendo que cPanel ejecuta las
tareas con el directorio de trabajo en la raíz del clone. **Verificar en el spike S0.**

### R4 — `vendor/` no se puede instalar (riesgo alto) — ver §7

### R5 — Auto-deploy simultáneo (riesgo medio)

Un webhook que despliegue en ambas instancias al hacer push propaga un commit roto a
los dos clientes a la vez.

**Mitigación:** deploy **manual por instancia** ("Deploy HEAD Commit"), o webhook
automático solo en la instancia de pruebas. Nunca push → ambos clientes.

### R6 — Disco y cuota compartidos (riesgo medio)

Si una instancia llena el disco, caen las dos.

**Mitigación:** alerta de uso, y backups descargados fuera del hosting.

### R7 — Sin worker de colas (riesgo medio)

cPanel no permite procesos persistentes.

**Mitigación:** `QUEUE_CONNECTION=sync` (recomendado para el volumen actual), o
cron cada minuto con `queue:work --stop-when-empty --max-time=50`.

### R8 — Versión de PHP (riesgo bloqueante)

`composer.json` exige `^8.5`. Si el host no ofrece 8.5, hay que bajar a `^8.3`
(Laravel 13 lo soporta). **Verificar antes de empezar.**

### R9 — Migraciones en deploy (riesgo medio)

Ejecutar `artisan migrate --force` automáticamente en cada deploy es cómodo y peligroso.

**Mitigación:** migraciones como paso **manual y explícito**, no dentro del deploy
automático. Ver §8.

---

## 7. Spike S0: resolución de `vendor/`

**Problema:** el commit `041d1fa4` sacó `vendor/` de git. En cPanel compartido no hay
garantía de acceso a Composer, y sin él la aplicación no arranca.

Tres salidas, en orden de preferencia:

| Opción | Cómo | Riesgo |
|---|---|---|
| **A. Composer en el deploy** | Versionar `composer.phar` y ejecutar `php composer.phar install --no-dev --optimize-autoloader` en `.cpanel.yml` | Puede fallar por memoria/tiempo límite o por PHP CLI deshabilitado |
| **B. Volver a versionar `vendor/`** | Revertir `041d1fa4` | Repo inflado; pero cero dependencias de shell. Es el *fallback* seguro |
| **C. Artefacto** | Compilar local/CI y subir un zip | Rompe el flujo "todo por git" que se quiere |

**Plan:** dedicar **1 día** a probar A. Si falla, aplicar B sin más discusión.

**Criterio de aceptación:** un deploy limpio desde cero produce una aplicación que
responde 200 en `/login`, sin intervención manual más allá del `.env`.

---

## 8. Flujo de despliegue

### 8.1 Preparación (una vez por instancia)

1. Crear la BD y el usuario MySQL en cPanel.
2. Crear el addon domain / subdominio con document root en `<app>/public`.
3. Clonar el repo en `apps/<cliente>` con cPanel Git Version Control.
4. Crear `.env` desde `.env.example` con los valores de la instancia.
5. `php artisan key:generate`
6. `php artisan storage:link`
7. Ejecutar el bootstrap de catálogos (§9).
8. Crear las entradas de cron (§8.4).

### 8.2 Despliegue de una versión

```
1. Tag en el repo (ej. v1.5.0) y push
2. En cPanel → Git Version Control → <cliente> → "Deploy HEAD Commit"
3. Post-deploy (manual o en .cpanel.yml, según S0):
   - composer install --no-dev        (si cambió composer.lock)
   - php artisan migrate --force      (paso manual, explícito)
   - php artisan config:cache
   - php artisan route:cache
   - php artisan view:cache
4. Verificación: /up, /login, y un listado con datos
5. Repetir en la segunda instancia
```

**Regla:** primero una instancia, se verifica, y solo entonces la otra.

### 8.3 `.cpanel.yml` (borrador)

```yaml
---
deployment:
  tasks:
    # Rutas relativas a propósito: el mismo archivo sirve a ambas instancias (R3)
    - php artisan config:cache
    - php artisan route:cache
    - php artisan view:cache
```

Sin `migrate` y sin rutas absolutas. Las migraciones se ejecutan aparte.

### 8.4 Cron

| Tarea | Frecuencia | Comando |
|---|---|---|
| Colas | cada minuto | `cd ~/apps/<cliente> && php artisan queue:work --stop-when-empty --max-time=50` |
| Backup BD | diario | `mysqldump ... \| gzip > ~/backups/<cliente>-$(date +%F).sql.gz` |

Solo si `QUEUE_CONNECTION` no es `sync`.

---

## 9. Bootstrap de catálogos

Una instancia vacía no es funcional. Los seeders deben separarse en dos capas:

### Catálogo común (idéntico en ambas instancias)

- `paises`, `municipios`, `tipo_documentos`, `tipos_afiliaciones`
- `codigo_cups`, `codigo_diagnosticos`, `cup_diagnostico`, `cup_finalidad`
- `causas_externas`, `finalidades`, `vias_ingreso`, `modalidades_atencion`
- `eps`, `metodo_pagos`, `impuestos`, `categoria_examen`

### Dato propio del cliente (carga manual por cliente)

- `empresas` (1 fila), `sedes`, `empleados`, `users` + roles
- `examenes`, `tarifas`, `convenios`, `servicios`, `productos`
- `resoluciones`, `parametros`, `valor_referencias`

**Acción:** crear `CatalogoBaseSeeder` y `ClienteSeeder`. Los seeders actuales
(`EmpresaSeeder` con Biotek y San Pablo) pasan a `DemoSeeder`, no a producción.

**Criterio de aceptación:** una instancia recién creada permite iniciar sesión, elegir
sede y registrar una orden de punta a punta.

---

## 10. Fases y estimación

| Fase | Contenido | Días |
|---|---|---|
| **S0** | Spike de `vendor/` (§7) y verificación de `.cpanel.yml` con rutas relativas | 1 |
| **F0** | Parametrización: `config/instancias.php`, `APP_INSTANCE`, guard de host, título fijo en `layouts/app.blade.php`, `.env.example` completo | 2–3 |
| **F1** | `.cpanel.yml`, script de verificación post-deploy, runbook, cron | 2–3 |
| **F2** | Aislamiento de estado: `storage`, sesiones, cache, logs, `SESSION_DOMAIN` | 1–2 |
| **F3** | Bootstrap de catálogos (`CatalogoBaseSeeder` + `ClienteSeeder` + `DemoSeeder`) | 2–3 |
| **F4** | Onboarding cliente 1 y 2: BD, dominio, TLS, `.env`, catálogos, verificación | 2–3 |
| **F5** | Disciplina de release: tags, changelog, checklist de deploy | 1–2 |
| | **Total** | **11–17 días** |

Se puede solapar con el trabajo de corrección porque **F0–F3 no cambian comportamiento**.

---

## 11. Fuera de alcance

- Vista consolidada entre clientes (imposible por diseño).
- Multi-empresa dentro de una misma instancia (se descarta: 1 `Empresa` por instancia).
- Migración a VPS o contenedores.
- Alta disponibilidad o balanceo.
- Los **24 tests en fallo** de la línea base: deuda pendiente, no bloqueante para este diseño.

---

## 12. Criterios de aceptación

1. Un push a `main` **no** despliega automáticamente a ningún cliente.
2. Desplegar el tag `vX` en `biotek` deja `sanpablo` intacto.
3. La instancia `biotek` responde 503 si se accede por el hostname de `sanpablo`.
4. Una cookie de sesión de `biotek` no es aceptada por `sanpablo`.
5. `storage/app/public` de cada instancia contiene solo sus propios archivos.
6. Un deploy desde cero (instancia nueva) queda operativo sin tocar archivos a mano.
7. Las migraciones nunca se ejecutan como efecto de un deploy automático.

---

## 13. Preguntas abiertas

| # | Pregunta | Bloquea |
|---|---|---|
| P1 | ¿Qué versión de PHP ofrece el host? | Sí — `composer.json` exige `^8.5` |
| P2 | ¿Hay Terminal/SSH en cPanel? | Sí — determina S0 (opción A vs B) |
| P3 | ¿El host permite fijar document root por addon domain? | Sí |
| P4 | ¿Cuántas BDs MySQL permite? | Sí (se necesitan 2) |
| P5 | ¿Hay Redis, o se usa `file`/`database`? | No |
| P6 | ¿Cuota de disco? | No, pero condiciona R6 |
| P7 | ¿Los dominios serán subdominios del mismo dominio? | Condiciona R2 |

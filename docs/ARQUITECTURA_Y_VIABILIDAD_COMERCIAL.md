# DinamyLAB — Evaluación de Arquitectura y Viabilidad Comercial

**Fecha:** 2026-11-01 (revisión de la versión del 2026-10-14)
**Modelo:** Claude Opus 5.5
**Propósito:** Análisis técnico-comercial para propuesta a cliente
**Fuente:** código y historial de git de la rama `maloca` (commit `db5c0e91`).
**Documentos relacionados:** [MANUAL.md](./MANUAL.md) (funcionamiento, roles y permisos), [DESPLIEGUE_INSTANCIAS.md](./DESPLIEGUE_INSTANCIAS.md), [ENTORNO_DOCKER_WSL.md](./ENTORNO_DOCKER_WSL.md).

---

## TL;DR

**Base sólida, más cerca de producción que hace dos semanas. MVP productivo en 5–7 semanas.**
Desde la versión anterior se resolvieron varios bloqueantes: el proyecto corre en Docker con MySQL 8.4, subió a Laravel 13 y PHP 8.5, los listados tienen paginación y búsqueda, existe auditoría de cambios en resultados, ingreso de resultados por lotes, reporte imprimible y un modelo de despliegue por cliente (una instancia por cliente en cPanel).

Siguen pendientes:
- **Ruta destructiva `GET /migracion`**, que ejecuta `migrate:fresh --seed`.
- **Creación de facturas bloqueada** por un `dd()`.
- **Control de sede incompleto.**
- Notificaciones.
- PDF generado en servidor.
- Rate limiting.
- Configuración final de producción.

### Cambios desde la versión del 2026-10-14

| Área | Antes | Ahora |
|---|---|---|
| Framework | Laravel 12 | **Laravel 13.38** |
| PHP | 8.2 (XAMPP, vulnerable) | **PHP 8.5** (contenedor `php:8.5-fpm`) |
| Base de datos | SQLite | **MySQL 8.4** (Docker); SQLite solo para tests |
| Entorno dev | XAMPP + `artisan serve` | **Docker Compose**: nginx, PHP-FPM, MySQL, worker de colas, Vite |
| Sesiones / colas | `file` | `database` |
| `vendor/` | Versionado en git | **Fuera de git** (commit `041d1fa4`) |
| Despliegue | No definido | **Una instancia por cliente** en cPanel + Git (`.cpanel.yml`, runbook) |
| Aislamiento de clientes | Solo por sesión | Middleware `EnsureInstance` por hostname + BD propia por cliente |
| Listado de pacientes | `Persona::all()` | Paginado (25) con búsqueda y filtros |
| Resultados | Edición sin rastro | **Auditoría** en `resultado_cambios` (valor anterior/nuevo, usuario, observación) |
| Ingreso de resultados | Uno a uno | **Por lotes** (`GuardarResultadosLote` + isla React `ResultadosLote`) |
| Reporte de resultados | Layout sin implementar | **Vista imprimible** (CSS `@media print`); `barryvdh/laravel-dompdf` instalado, sin usar |
| Número de orden | Entero, lógica en controller | `string`, servicio `SugerirNumeroOrden` (respeta prefijos y ceros) |
| Errores | Páginas por defecto | Páginas propias en español (401–504) y notificaciones de UI |
| Seeders | Mezclados | Separados: `CatalogoBaseSeeder`, `ClienteSeeder`, `DemoSeeder`, `RolesYPermisosSeeder` |
| Tests | 18 archivos | **26 archivos** |

---

## Tabla de Contenidos

1. [Estadísticas del Codebase](#1-estadísticas-del-codebase)
2. [Estructura del Repositorio](#2-estructura-del-repositorio)
3. [Schema de Base de Datos](#3-schema-de-base-de-datos)
4. [Arquitectura Backend](#4-arquitectura-backend)
5. [Arquitectura Frontend](#5-arquitectura-frontend)
6. [Infraestructura y Despliegue](#6-infraestructura-y-despliegue)
7. [Features Implementados](#7-features-implementados)
8. [Deuda Técnica](#8-deuda-técnica)
9. [Cobertura de Tests](#9-cobertura-de-tests)
10. [Dependencias](#10-dependencias)
11. [Seguridad](#11-seguridad)
12. [Comparación con Software Similar](#12-comparación-con-software-similar)
13. [Costos y Pricing](#13-costos-y-pricing)
14. [Tiempos de Entrega](#14-tiempos-de-entrega)
15. [Riesgos para la Propuesta](#15-riesgos-para-la-propuesta)
16. [Resumen Ejecutivo](#16-resumen-ejecutivo)

---

## 1. Estadísticas del Codebase

| Métrica | Valor (2026-11-01) | Antes (2026-10-14) |
|---|---|---|
| Controladores PHP | 75 archivos — 6,277 LOC (22 en `Api/`) | 75 — 5,911 LOC |
| Modelos Eloquent | 43 archivos — 1,838 LOC | 43 — 1,848 LOC |
| Vistas Blade | 169 archivos | 156 |
| Archivos React/JS | 79 archivos — ~10,650 LOC | 45+ — ~10,215 LOC |
| Migraciones | 54 archivos | 52 |
| Tests | 26 archivos | 18 |
| Services | 11 archivos | 8 |
| Form Requests | 5 archivos | 5 |
| Policies | 3 archivos | 3 |
| Middleware propio | 1 (`EnsureInstance`) | 0 |
| Comandos Artisan propios | 1 (`instancia:verificar`) | 0 |
| Declaraciones `Route::` | web 89 · api 74 · auth 17 | ~93 web · 40+ api |

---

## 2. Estructura del Repositorio

```
DinamyLAB/
├── app/
│   ├── Console/Commands/       # instancia:verificar
│   ├── Http/
│   │   ├── Controllers/        # 75 controladores (22 API)
│   │   ├── Middleware/         # EnsureInstance (guarda por hostname)
│   │   └── Requests/           # 5 Form Requests
│   ├── Models/                 # 43 modelos Eloquent
│   ├── Services/               # 11 servicios de negocio
│   ├── Policies/               # 3 políticas de autorización
│   ├── Jobs/, Providers/, Support/, View/
├── config/instancias.php       # Identidad, dominios y datos del cliente (desde .env)
├── database/
│   ├── migrations/             # 54 migraciones
│   ├── seeders/                # Catálogo base / Cliente / Demo / Roles
│   └── factories/
├── docker/                     # Dockerfile PHP 8.5, php.ini, nginx
├── compose.yaml                # Entorno de desarrollo
├── .cpanel.yml                 # Tareas de despliegue en cPanel
├── resources/{views,js,css}
├── routes/{web,api,auth}.php
├── tests/{Feature,Unit}
└── docs/
```

---

## 3. Schema de Base de Datos

**54 migraciones** que cubren el dominio del sector salud colombiano. Motor de desarrollo y producción: **MySQL 8.4** (`utf8mb4_unicode_ci`, zona horaria `America/Bogota`).

### Cambios recientes

| Migración | Efecto |
|---|---|
| `alter_ordenes_medicas_numero_to_string` | El número de orden pasa a texto para admitir prefijos y ceros a la izquierda |
| `create_resultado_cambios_table` | Bitácora de ediciones de resultados: procedimiento, resultado, parámetro, usuario, valor anterior, valor nuevo, observación |

### Entidades Núcleo

| Tabla | Descripción | Relaciones clave |
|---|---|---|
| `users` | Autenticación Laravel | — |
| `personas` | Pacientes/personas | → tipo_documento, pais_origen |
| `empleados` | Personal del laboratorio | → user_id, empresa_id |
| `empresas` | Organizaciones/laboratorios | — |
| `sedes` | Sucursales | → empresa_id |
| `ordenes_medicas` | Órdenes de trabajo | → paciente_id, sede_id, user_id |

### Datos Clínicos

| Tabla | Descripción |
|---|---|
| `examenes` | Catálogo de análisis (hemograma, glucosa, etc.) |
| `procedimientos` | Instancias de análisis vinculadas a órdenes |
| `resultados` | Valores obtenidos del análisis |
| `resultado_cambios` | **Nuevo.** Auditoría de ediciones de resultados |
| `valor_referencias` | Rangos de referencia por examen |
| `orden_examen` | Pivot M2M: órdenes ↔ exámenes (con cantidad) |
| `observacions` | Notas sobre procedimientos |

### Estándares Colombianos

| Tabla | Descripción |
|---|---|
| `codigo_cups` | Códigos de procedimientos (CUPS) |
| `codigo_diagnosticos` | Diagnósticos CIE-10 |
| `causa_externas` | Causas externas de lesión |
| `cup_diagnostico` | Pivot: CUPS ↔ diagnósticos |
| `cup_finalidad` | Pivot: CUPS ↔ finalidades |

### Sistema de Salud

| Tabla | Descripción |
|---|---|
| `eps` | Aseguradoras de salud |
| `municipios` | Municipios colombianos |
| `paises` | Países (códigos ISO) |
| `afiliacion_saluds` | Afiliación a salud por persona |
| `tipo_afiliacions` | Contributivo, subsidiado, etc. |
| `tipo_documentos` | CC, CE, TI, PA, etc. |

### Financiero

| Tabla | Descripción |
|---|---|
| `convenios` | Contratos de servicio y tarifas negociadas |
| `tarifas` | Reglas de precio (polimórfico: examen, servicio, procedimiento) |
| `sede_tarifa` / `convenio_tarifa` | Precios por sede / por convenio |
| `facturas`, `factura_medio_pagos` | Facturas y métodos de pago |
| `impuestos`, `metodo_pagos` | Impuestos y métodos de pago |
| `resolucions` | Resoluciones DIAN para facturación |

### Datos de Contacto (Polimórficos)

`contacto_emergencias`, `direccions`, `telefonos`, `correo_electronicos`, `red_socials`.

### Atención Clínica

`modalidad_atencions`, `finalidads`, `via_ingresos`, `servicios`, `servicio_habilitados`.

### Especiales

`pre_registros_citas` (público), `parametros`, `opcions`, `roles`/`permissions` (Spatie), `personal_access_tokens` (Sanctum), `sessions`, `jobs` (sesiones y colas en BD).

### Modelo de Relaciones Principales

```
Persona (paciente)
├── has many Ordenes
├── has one AfiliacionSalud
├── morphOne Direccion
└── morphMany Telefonos / CorreoElectronicos / RedSocials

Orden
├── belongs to Persona, Sede
├── has many Procedimientos, Facturas
└── belongs to many Examenes (pivot orden_examen, con cantidad)

Procedimiento
├── belongs to Orden, Examen, Empleado, Sede
├── has many Resultados
├── has many ResultadoCambios   ← nuevo
└── has many Observacions

Tarifa (polimórfica)
├── tarifable: Examen | Servicio | Procedimiento
├── belongs to Sede
└── belongs to many Convenios
```

---

## 4. Arquitectura Backend

### Controladores (75 total)

**Web:** `PersonaController`, `OrdenController`, `ProcedimientoController`, `ResultadosController`, `FacturaController`, `ExamenController` (incluye lotes), `ConvenioController`, `EmpleadoController`, `SedeController`, `AdministracionController`, `PreRegistroCitaController`, `CodigoCupController`, `CodigoDiagnosticoController` y controladores de soporte.

**API (22 en `Http/Controllers/Api/`):** personas, órdenes, exámenes y precios, resultados, pre-registro de citas, tarifas, datos estáticos del frontend, dashboard, CUPS, CIE-10 (nuevo endpoint paginado con búsqueda), municipios y sistema de salud.

**Auth (Breeze):** registro, login, restablecimiento de contraseña, verificación de email. Se corrigió la pérdida de sesión por doble desencriptado de cookies en la API (`bootstrap/app.php`).

**Cambio propuesto, sin implementar: Laravel Fortify.** Reemplazaría a Breeze como backend de autenticación web y mantendría Sanctum para la API. Es un cambio de backend que queda en manos del responsable backend.

### Middleware

- `EnsureInstance` (web y API): rechaza con 503 las peticiones a un hostname que no está en `APP_HOSTS`. Sin `APP_INSTANCE` no actúa (desarrollo y tests).
- Sanctum stateful + `StartSession` en la API, para que los endpoints lean la sede activa de la sesión.

### Services (11 archivos)

| Servicio | Responsabilidad |
|---|---|
| `GuardarPersona` | Parsea nombres, guarda paciente con contactos |
| `GuardarConvenio` | Guarda contratos de servicio |
| `GuardarContacto` | Guarda teléfono, email, dirección |
| `EscogerPrecioExamen` | Priorización de tarifas |
| `EscogerReferencia` | Selecciona rango de referencia (refactorizado) |
| `ElegirEmpresa` | Selección de organización según sesión |
| `NombreParser` | Divide nombre completo |
| `ParseadorListaPersonas` | Importación masiva de pacientes |
| `ActualizarResultados` | **Nuevo.** Edita resultados en transacción con bloqueo de fila, valida por tipo de dato (número, fecha, opción) y registra el cambio en `resultado_cambios` |
| `GuardarResultadosLote` | **Nuevo.** Guarda resultados de varios procedimientos de un examen en una sola operación |
| `SugerirNumeroOrden` | **Nuevo.** Propone el siguiente número de orden por sede, conservando prefijo y relleno de ceros |

### Lógica de Tarifas (EscogerPrecioExamen)

1. `convenio+sede` — precio específico por contrato y sucursal
2. `sede` — precio específico por sucursal
3. `precio_base` — precio base del examen

### Form Requests y Policies

Sin cambios: 5 Form Requests (`StorePersonaRequest`, `OrdenStoreRequest` ajustado, `ProfileUpdateRequest`, `CambiarNombrePersonaRequest`, `LoginRequest`) y 3 Policies (`EmpresaPolicy`, `PreRegistroCitaPolicy`, `SedePolicy`, con el trait `ChecksEmpresaAccess`).

---

## 5. Arquitectura Frontend

### Vistas Blade (169 archivos)

Además de los módulos existentes (`personas/`, `ordenes/`, `resultados/`, `procedimientos/`, `examenes/`, `facturas/`, `convenios/`, `citas/`, `admin/`, `empresa/`, `sedes/`, `empleados/`):

- **Listados con búsqueda y filtros** en personas, órdenes, procedimientos, resultados, exámenes, facturas, convenios, CUPS y CIE-10.
- **`resultados/show`** rediseñado como reporte imprimible: encabezado, datos del paciente, tabla de parámetros y pie de página que solo aparecen al imprimir.
- **`resultados/edit`** con observación obligatoria y visualización del historial de cambios.
- **`errors/`**: páginas de error propias (401, 403, 404, 419, 422, 429, 500–504) sobre una plantilla común.
- **`layouts/notifications`**: avisos de éxito y error unificados.
- Correcciones del modo oscuro.

### Componentes React

**Entry points (vite.config.js):** `app.jsx` (dashboard, citas, lotes de resultados, etc.), `appOrden.jsx`, `appPersona.jsx`, `ExamenDetail.jsx`, `convenioCreate.jsx`.

| Componente | Función |
|---|---|
| `CrearOrdenComponent` | Flujo paciente → exámenes → diagnósticos (corregido; valores por defecto revisados) |
| `ResultadosLote` | **Nuevo.** Ingreso de resultados por lotes para un examen |
| `Setup` | Configuración inicial (reescrito) |
| `FormPersona` | Formulario de paciente reutilizable |
| `AutocompleteInput`, `CieSearchInput`, `BuscarModelo` | Búsquedas con autocompletado |
| `TablaExamenes`, `StepperOrden` | Tabla de exámenes y formulario multipaso |
| `FormPreRegistroCita`, `RecepcionCitas` | Pre-registro y recepción |
| `TarifasExamenesSede`, `ConvenioForm` | Tarifas y convenios |

### Patrón de islas

Cada componente React se monta sobre un `<div id="...">` dentro de una vista Blade y recibe sus datos por atributos `data-*`. No hay SPA ni estado global; el estado es local (`useState`/`useReducer`) y las llamadas van por axios.

---

## 6. Infraestructura y Despliegue

### Desarrollo (Docker Compose)

| Servicio | Imagen | Función |
|---|---|---|
| `app` | `php:8.5-fpm` (propia) | PHP-FPM con la aplicación |
| `web` | `nginx:1.27-alpine` | Servidor web en `:8000` |
| `db` | `mysql:8.4` | Base de datos en `:3307` del host, con healthcheck |
| `queue` | misma imagen que `app` | `php artisan queue:work` |
| `vite` | `node:22-alpine` | HMR con pnpm en `:5173` |

Las credenciales salen del `.env`; el compose no tiene secretos.

### Producción: una instancia por cliente

- Cada cliente tiene **su propio clone, `.env`, base de datos y `storage/`**. El código es el mismo repositorio y el mismo tag para todos.
- Despliegue con **cPanel → Git Version Control** y tareas en `.cpanel.yml`.
- Aislamiento: `APP_INSTANCE`, `APP_HOSTS`, `SESSION_COOKIE` propio y `SESSION_DOMAIN` vacío, para que una instancia no atienda ni comparta cookies con otra.
- Datos del cliente (razón social, NIT, sede, código de prestador, admin) en variables `CLIENTE_*`, cargados por `ClienteSeeder`. El código no conoce a ningún cliente.
- `php artisan instancia:verificar` revisa la configuración antes de abrir la instancia.

Este modelo es más simple que una arquitectura multi-tenant en una sola base de datos. Encaja con un MVP: cada cliente queda aislado a nivel de BD y el costo de hosting sigue siendo bajo.

### Pendientes de infraestructura

- **Spike S0 (`vendor/`)**: como `vendor/` ya no está en git, el hosting debe poder correr Composer. Si no puede, hay que volver a versionarlo.
- **Versión de PHP en el hosting**: `composer.json` exige `^8.5`. Si el hosting no la ofrece, bajar a `^8.3` (mínimo de Laravel 13).
- **CI**: el workflow de GitHub Actions usa PHP 8.5, pero solo se dispara en `master` y `*.x`, y la rama principal es `main`.

---

## 7. Features Implementados

### ✅ Funcionales

1. **Gestión de pacientes**: crear, buscar y filtrar con paginación, ver perfil con historial, contactos (la edición web falta; ver Parciales).
2. **Órdenes médicas**: paciente → exámenes → CIE-10 → orden; número sugerido por sede con prefijos; múltiples exámenes con cantidades; totales por tarifa.
3. **Procedimientos**: auto-creados por examen, estados, empleado, sede, datos RIPS.
4. **Ingreso de resultados**: por procedimiento y **por lotes**; rangos de referencia; validación por tipo de dato.
5. **Edición auditada de resultados**: cada cambio guarda valor anterior, valor nuevo, usuario y observación obligatoria, dentro de una transacción con bloqueo.
6. **Reporte de resultados imprimible**: vista con formato de impresión (encabezado, paciente, parámetros, pie). Se imprime o guarda como PDF desde el navegador.
7. **Precios**: convenio+sede → sede → precio base; multisede.
8. **Pre-registro de citas** (público): código de confirmación, check-in, confirmación o cancelación.
9. **Sistema de salud**: EPS, tipo de afiliación, plan.
10. **RIPS**: exportación JSON/CSV (conserva valores de prueba en algunos puntos; falta validación normativa).
11. **Usuarios y roles**: Breeze + Spatie (roles `admin`, `prestador`, `agente`, `contable`, `paciente`), vínculo empleado ↔ usuario ↔ organización.
12. **Dashboard**: métricas de procedimientos, pacientes y órdenes.
13. **Búsqueda y filtrado** en todos los listados principales, incluidos CUPS y CIE-10.
14. **Despliegue por cliente**: instancias aisladas, seeders separados, verificación de instancia.

### 🟡 Parciales

- **Facturación**: hay listado, detalle, medios de pago y resoluciones DIAN, pero **la creación está bloqueada**: `FacturaController.php:95` tiene un `dd()` antes de guardar.
- **PDF generado en servidor**: `barryvdh/laravel-dompdf` está instalado, pero ningún código lo usa todavía. Hoy el PDF sale de la impresión del navegador, lo que alcanza para entregar en mostrador pero no para enviar por correo ni archivar.
- **Cargador de listas de pacientes**: parsea la lista y deja elegir una persona para llenar el formulario. **No** crea registros en lote.
- **Edición de persona**: en web, `edit`, `update` y `destroy` de `PersonaController` están vacíos; solo existe la actualización por API.
- **Control por sede**: `elegirSede()` no verifica que el usuario pertenezca a la sede, y las escrituras de resultados (`store` web y API) no aplican el control que sí tiene `update`. Detalle en [MANUAL.md §7](./MANUAL.md#7-roles-permisos-y-sedes).
- **Auditoría HABEAS DATA**: cubre la edición de resultados; falta registrar accesos y cambios a datos de pacientes y órdenes.

### ❌ No implementado

- Notificaciones por email, SMS o WhatsApp (`MAIL_MAILER=log`; sigue el `TODO` en `PreRegistroCitaController`)
- Portal de pacientes
- Recordatorios de citas
- Multilenguaje (español fijo; aceptable para el mercado objetivo)

---

## 8. Deuda Técnica

### ✅ Resuelto desde la versión anterior

| # anterior | Ítem | Cómo se resolvió |
|---|---|---|
| 3 | `Persona::all()` sin paginación | Paginación de 25 con búsqueda en todos los listados principales |
| 4 (parcial) | SQLite como base de datos | MySQL 8.4 en Docker y en el modelo de despliegue |
| 8 (parcial) | Sin auditoría de resultados | Tabla `resultado_cambios` + `ActualizarResultados` |
| 10 (parcial) | Aislamiento de organización | Una instancia y una BD por cliente + `EnsureInstance` |
| 13 (parcial) | Número de orden con lógica fija en el controller | `SugerirNumeroOrden` |
| — | `vendor/` versionado | Fuera de git |
| — | PHP 8.2 vulnerable / Laravel 12 con soporte hasta feb-2027 | PHP 8.5 + Laravel 13 |
| — | Sesión que se perdía al llamar la API | Corrección del middleware en `bootstrap/app.php` |

### 🔴 Crítico — Bloqueante para producción

**1. Ruta destructiva `GET /migracion`.** `routes/web.php:115` ejecuta `migrate:fresh --seed`, que borra toda la base de datos. Está protegida con `can:eliminar_persona`, un permiso que el seeder le asigna al rol `prestador`. Hay que retirarla antes de cargar datos reales.

**2. Creación de facturas bloqueada.** `FacturaController.php:95` tiene un `dd()` antes de guardar. Si la propuesta incluye facturación, hay que terminarla.

**3. Control de sede incompleto.** Un usuario puede activar una sede a la que no pertenece, y las escrituras de resultados por `store` web y por API no verifican la membresía.

**4. Sin notificaciones (email/WhatsApp).** El pre-registro conserva el `TODO` de envío del código, y no hay mailer configurado.

**5. Sin PDF generado en servidor.** El reporte se imprime desde el navegador. Para enviarlo por correo o archivarlo hace falta usar DomPDF, que ya está instalado.

**6. Sin rate limiting en endpoints públicos.** `POST /api/citas/pre-registrar`, `POST /api/citas/pre-registrar-lista` y `GET /api/citas/consultar/{codigo}` no tienen `throttle`. Solo las rutas de verificación de email lo tienen.

**7. Configuración de producción sin cerrar.** `.env.example` trae `APP_DEBUG=true`. El runbook indica `false`, pero no hay una verificación que lo obligue. Además falta resolver el spike de `vendor/` y la versión de PHP del hosting (ver §6).

### 🟡 Importante

**8. Permisos demasiado amplios.** `can:eliminar_persona` protege todo el grupo administrativo, y varios permisos que el código consulta (`crear_sedes`, `editar_empresa`, `gestionar_citas`, `eliminar_orden_medica`, …) no existen en el seeder. Faltan `OrdenPolicy` y `ProcedimientoPolicy`, y `SedePolicy` está registrada pero no se invoca.

**9. Edición web de persona sin implementar** (métodos vacíos en `PersonaController`).

**10. País hardcodeado como "170".** Más de 20 apariciones en controladores, servicios y en `Persona`. Debería ser una constante o un valor de configuración.

**11. N+1 queries.** Las vistas nuevas usan eager loading, pero `EmpleadoController::dashboard()` y `ListaPersonasController` siguen sin optimizar.

**12. Auditoría HABEAS DATA incompleta.** Falta registrar quién consulta o modifica datos de pacientes y órdenes. Hay 19 llamadas a `Log::` en `app/`, sin un formato común de usuario + acción + ID.

**13. Lógica en controllers.** Ahora hay 11 services frente a 75 controllers, y los flujos nuevos ya usan services. Los módulos antiguos siguen igual.

**14. Duplicación web/API.** Las validaciones de persona y la creación de órdenes están en dos lugares, igual que la captura de resultados.

### 🟠 Menor

**15. Valores fijos**: strings de tipo de tarifa y vencimiento de factura a 30 días (`FacturaController:92`).
**16. Validaciones inconsistentes**: unos flujos usan Form Requests y otros validan inline.
**17. Estado del frontend**: los datos estáticos se piden en cada montaje, y la sede activa vive en la sesión PHP y en el estado de React.
**18. Transacciones faltantes** en `ListaPersonasController` y `ReprocesarController`.
**19. `Empleado->nombre()` sin `return`** (`app/Models/Empleado.php:29-31`). Sigue sin corregir.
**20. Respuestas API sin API Resources** consistentes.
**21. `EmpresaPolicyTest` desalineado**: usa nombres de ruta `sedes.*` cuando las rutas son `admin.sedes.*`, y un test no tiene aserciones.

---

## 9. Cobertura de Tests

**26 archivos (Pest 4 + PHPUnit).** En esta revisión no se ejecutó la suite; las cifras de aprobados y fallidos deben confirmarse con `docker compose exec app php artisan test`.

### Feature (24 archivos)

| Grupo | Archivos | Cobertura |
|---|---|---|
| `Auth/` | 7 | Registro, login, reset, verificación, confirmación y actualización de contraseña, **persistencia de sesión** (nuevo) |
| `Instancias/` | 5 | **Nuevo.** Guarda por hostname, comando `instancia:verificar`, branding, deploy cPanel, seeders |
| `ResultadosEdicionTest` | 1 | **Nuevo.** Edición auditada de resultados |
| `ResultadosLoteTest` | 1 | **Nuevo.** Ingreso por lotes |
| `UiNotificationsTest` | 1 | **Nuevo.** Notificaciones de interfaz |
| `InicioRedirectTest` | 1 | **Nuevo.** Redirección sin sede |
| `OrdenesApiTest` | 1 | Creación y validación de órdenes (ampliado) |
| Otros | 7 | Dashboard, precios, elegir sede, welcome, perfil, `EmpresaPolicy`, ejemplo |

### Unit (2 archivos)

`EscogerPrecioExamenTest` y `ExampleTest` (vacío).

### Brechas

- ❌ Sin tests para: creación y edición de paciente, importación masiva, pre-registro de citas, facturación, exportación RIPS
- ❌ Sin tests de autorización para órdenes, procedimientos y resultados
- ❌ El flujo completo orden → resultado → reporte no tiene un test de extremo a extremo
- ⚠️ Fallas preexistentes reportadas el 2026-10-29 (sin verificar en esta revisión): a `Convenio` le falta `HasFactory`, la factory de `Empleado` usa un `tipo_documento_id` inexistente y `header.blade.php` lee `->sedes` sobre `null`
- ⚠️ El CI no corre en la rama `main` (ver §6)

---

## 10. Dependencias

### Backend (composer.json)

| Paquete | Versión | Propósito |
|---|---|---|
| php | ^8.5 | Runtime |
| laravel/framework | **^13.0** (13.38) | Framework |
| laravel/sanctum | ^4.0 | Autenticación API |
| laravel/tinker | **^3.0** | REPL |
| barryvdh/laravel-dompdf | **^3.1** (nuevo) | PDF (instalado, sin usar) |
| spatie/array-to-xml | ^3.4 | Exportación RIPS |
| spatie/laravel-permission | ^6.18 | Roles y permisos |

**Dev:** faker ^1.23, **laravel-lang/common ^6.7**, breeze ^2.3, pail ^1.2.2, pint ^1.13, sail ^1.41, mockery ^1.6, collision ^8.6, **pest ^4.0**, **pest-plugin-laravel ^4**.

### Frontend (package.json, pnpm)

react ^19.2, react-dom ^19.2, vite ^7.2.4, @vitejs/plugin-react ^5.1.1, laravel-vite-plugin ^2.0.1, tailwindcss ^3.4.18, @tailwindcss/forms, axios ^1.13.2, postcss, autoprefixer, concurrently.

### Ausencias relevantes

| Librería / servicio | Impacto |
|---|---|
| Proveedor de email (SMTP, Mailgun, SES) | **Crítico**: sin notificaciones |
| WhatsApp Business API | Alto: es el canal esperado en Colombia |
| Vitest/Jest | Sin tests de frontend |
| TypeScript | Sin tipado en React |

---

## 11. Seguridad

### ✅ Fortalezas

- PHP 8.5 y Laravel 13: versiones con soporte, que eliminan la exposición de PHP 8.2 / XAMPP
- Breeze (web) + Sanctum (API); Spatie para roles y permisos
- CSRF, bcrypt (12 rondas), Form Requests en los flujos principales
- Secretos en `.env`; el compose no tiene credenciales
- **Aislamiento por cliente**: BD, `storage/` y cookie de sesión propios, más la guarda de hostname
- **Trazabilidad de resultados clínicos**: toda edición queda auditada con observación
- Sesiones en BD (no en archivos)
- Páginas de error propias que no exponen detalles internos

### ⚠️ Preocupaciones

- **`GET /migracion` borra la base de datos** y la puede ejecutar el rol `prestador`
- **Selección de sede sin verificar membresía**, y escrituras de resultados sin control de sede
- **Endpoints públicos sin rate limiting** (pre-registro y consulta de citas)
- `APP_DEBUG=true` por defecto en `.env.example`
- PII de pacientes en texto plano; sin cifrado en reposo
- Las APIs devuelven modelos Eloquent completos
- HABEAS DATA: falta la bitácora de accesos a datos de pacientes
- `/api/static-data-for-frontend` expone todos los datos de referencia (probablemente aceptable)
- Faltan validaciones en algunos endpoints (`OpcionController`, `TributoController`)

---

## 12. Comparación con Software Similar

| Criterio | DinamyLAB | LabSoft (CO) | OpenELIS | LIS1 SaaS (CO) | SCC LIS |
|---|---|---|---|---|---|
| **Dominio colombiano** | ✅ Nativo | ✅ | ❌ EEUU | ✅ | Parcial |
| **CUPS / CIE-10 / RIPS** | ✅ | ✅ | Parcial | ✅ | ❌ |
| **Stack moderno** | ✅ Laravel 13 / React 19 / PHP 8.5 | ❌ Legacy | ❌ Java/legacy | ✅ | ❌ |
| **Multi-sede** | ✅ | Parcial | ✅ | ✅ | ✅ |
| **Código propio (customizable)** | ✅ | ❌ | ✅ (OSS) | ❌ | ❌ |
| **Reporte de resultados** | 🟡 Imprimible (sin PDF por correo) | ✅ | ✅ | ✅ | ✅ |
| **Auditoría de resultados** | ✅ | ✅ | ✅ | ✅ | Parcial |
| **Ingreso por lotes** | ✅ | ✅ | ✅ | ✅ | Parcial |
| **Notificaciones** | ❌ Falta | ✅ | Parcial | ✅ | Parcial |
| **Facturación** | 🟡 Creación bloqueada | ✅ | ❌ | ✅ | Parcial |
| **Integración EPS/Seguros** | ✅ | ✅ | ❌ | ✅ | ❌ |
| **Precio estimado (COP/mes)** | A definir | $800K–$2M | Gratis* | $400K–$1.2M | $600K–$1.5M |
| **Facilidad de implementación** | Media-fácil (instancia por cliente) | Media | Compleja | Fácil | Media |

*OpenELIS es OSS, pero la implementación y el soporte cuestan. Los precios de competidores son estimaciones de referencia y no se volvieron a verificar en esta revisión.

### Ventajas diferenciales

1. **Stack moderno + dominio colombiano**, ahora en las versiones más recientes de Laravel y PHP.
2. **Código 100% propio**, customizable por cliente.
3. **Aislamiento fuerte por cliente** (BD propia), un argumento de venta frente a SaaS compartidos en términos de privacidad.
4. **Costo de operación bajo** con hosting cPanel.

### Brechas frente a competidores

1. **Notificaciones**: WhatsApp es el estándar en Colombia.
2. **PDF por correo**: hoy el reporte solo sale impreso o guardado desde el navegador.
3. **Sin clientes en producción** todavía.

---

## 13. Costos y Pricing

### Costo de llevar a producción (MVP mínimo)

Estimado con 1 desarrollador senior (~$80.000 COP/hora):

| Ítem | Horas | Costo (COP) | Antes |
|---|---|---|---|
| Autorización crítica: retirar `/migracion`, membresía de sede, escrituras de resultados | 8–12h | $640.000 – $960.000 | — |
| Terminar la creación de facturas (quitar `dd()`, persistir, probar) | 6–10h | $480.000 – $800.000 | — |
| PDF en servidor con DomPDF (reutilizando la vista imprimible) | 8–12h | $640.000 – $960.000 | 20–30h |
| Email / WhatsApp de notificaciones | 15–20h | $1.200.000 – $1.600.000 | 15–20h |
| N+1 restantes (dashboard, listas de personas) | 6–10h | $480.000 – $800.000 | 8–12h |
| Cierre de producción (spike `vendor/`, PHP del host, `APP_DEBUG`, CI) | 4–6h | $320.000 – $480.000 | 8–10h |
| Rate limiting en endpoints públicos | 4–6h | $320.000 – $480.000 | 6–8h |
| Tests de flujos críticos + corrección de fallas preexistentes | 12–18h | $960.000 – $1.440.000 | 16–24h |
| **Total mínimo viable** | **63–94h** | **$5.040.000 – $7.520.000** | 73–104h |

### Trabajo adicional recomendado

| Ítem | Horas | Costo (COP) |
|---|---|---|
| Auditoría HABEAS DATA de accesos (la de resultados ya existe) | 6–10h | $480.000 – $800.000 |
| Matriz de permisos + Policies de Orden y Procedimiento | 6–8h | $480.000 – $640.000 |
| Edición web de persona (hoy son métodos vacíos) | 8–10h | $640.000 – $800.000 |
| País "170" a configuración + bug `Empleado->nombre()` | 2–4h | $160.000 – $320.000 |
| Migración de Breeze a Fortify (opcional) | 8–12h | $640.000 – $960.000 |
| **Subtotal adicional** | **30–44h** | **$2.400.000 – $3.520.000** |

**Costo total estimado para una versión de producción robusta: $7.440.000 – $11.040.000 COP** (antes: $8.72M – $12.24M). La reducción frente a la versión anterior es menor de lo que sugieren los avances porque esta revisión encontró bloqueantes que la anterior no registraba (`/migracion`, facturas, control de sede).

### Modelos de pricing para el cliente

Se mantienen los de la versión anterior.

**Opción A — Licencia mensual SaaS:**

| Segmento | Descripción | Precio |
|---|---|---|
| Lab pequeño | 1 sede, <500 órdenes/mes | $350.000 – $550.000 COP/mes |
| Lab mediano | 2-3 sedes, <2.000 órdenes/mes | $800.000 – $1.200.000 COP/mes |
| Lab grande | 4+ sedes, >2.000 órdenes/mes | $1.500.000+ COP/mes (a negociar) |

**Opción B — Licencia única + implementación:** fee de $8M – $15M COP y soporte de $2M – $4M COP/año.

**Opción C — Híbrido (recomendado para el primer cliente):** implementación de $5M – $8M COP y mensualidad de $400.000 – $600.000 COP con hosting, soporte y actualizaciones.

### Borrador de propuesta "Plan Emprendedor" (10-oct-2026)

Se redactó una propuesta concreta para un laboratorio de 1 sede (antes estaba en `openCode_comercial.md`). Sus precios quedan **por debajo** de los rangos de arriba, porque apuntan a un cliente piloto. Su validez de 15 días ya venció, así que debe re-emitirse.

| Plan | Pago inicial | Mensualidad | Total año 1 |
|---|---|---|---|
| **Emprendedor Híbrido** (recomendado) | $3.500.000 (50% anticipo + 50% contra entrega) | $249.000 | $6.488.000 |
| Pago único (ancla) | $8.500.000, incluye 12 meses de hosting y soporte | — (renovación $2.500.000/año) | $8.500.000 |
| SaaS puro | Setup $900.000 | $349.000 (permanencia 12 meses) | $5.088.000 |

**Alcance ofrecido:** pacientes y carga desde Excel, órdenes, captura de resultados con hasta 30 exámenes parametrizados, reporte imprimible con logo y firma, usuarios ilimitados en la sede, facturación básica con resolución DIAN, RIPS JSON, instalación, 2 sesiones de capacitación, 30 días de garantía y soporte por WhatsApp/correo con 2 ajustes menores al mes. Las horas fuera de alcance se cobran a $120.000 COP/hora.

**No incluido:** interfaz con analizadores (ASTM/HL7), portal de pacientes, WhatsApp/SMS masivo, facturación electrónica ante DIAN, multisede, citas en línea, contabilidad e inventarios.

**Desajustes con el estado actual del código, que hay que resolver antes de re-emitirla:**
- Ofrece **facturación básica**, y la creación de facturas está bloqueada (§8, ítem 2).
- Ofrece **carga masiva**, y el cargador actual no crea registros en lote (§7).
- Promete un **cronograma de 2–3 semanas**, que no incluye las 63–94h de trabajo mínimo de este documento. Un plazo realista es el de §14.
- Excluye **multisede y citas en línea**, que el sistema ya tiene. Pueden ofrecerse como diferencial o como plan superior.

### Costos de hosting

El modelo de una instancia por cliente en cPanel permite alojar varios clientes en un mismo plan.

| Opción | Descripción | Costo mensual (USD, estimado) |
|---|---|---|
| Hosting cPanel (plan con PHP 8.3+, SSH y Git) | Varios clientes, una instancia cada uno | $10 – $30 |
| VPS (DigitalOcean / Linode) con Docker | Más control; reutiliza la configuración de desarrollo | $20 – $80 |
| Nube (AWS / GCP) | Alta disponibilidad | $80 – $150 |

---

## 14. Tiempos de Entrega

### Primer cliente (MVP de producción)

**Con 1 desarrollador senior a tiempo completo:**

```
Semana 1:    Autorización crítica (/migracion, sedes) + rate limiting + cierre de producción
Semana 2:    Terminar facturación + PDF en servidor
Semana 3:    Notificaciones por email (y WhatsApp si se prioriza) + N+1 restantes
Semana 4-5:  Tests de flujos críticos, corrección de fallas, instancia piloto en cPanel
Semana 6-7:  Piloto con el cliente (datos reales, ajustes de UX)
──────────────────────────────────────────────────────────
Total: 5–7 semanas   (antes: 6–8)
```

**Con 2 desarrolladores:** 3–5 semanas.

### Hitos

| Hito | Duración acumulada |
|---|---|
| Instancia del cliente en producción | Semana 5 |
| Estabilización con cliente real | Semana 9 |
| **Firma de aceptación formal** | **~2–2.5 meses** |

### Roadmap post-MVP

| Fase | Plazo | Contenido |
|---|---|---|
| **MVP v1** | 0–1.5 meses | Autorización crítica, facturación, notificaciones, PDF en servidor, producción, seguridad básica |
| **v1.5** | 1.5–3 meses | HABEAS DATA completo, policies, edición de persona, Fortify |
| **v2** | 3–5 meses | Portal de pacientes, reportes analíticos, RIPS automático |
| **v3** | 5–12 meses | WhatsApp Business API, app móvil, integración SISPRO |

---

## 15. Riesgos para la Propuesta

### Riesgos técnicos

| Riesgo | Probabilidad | Impacto | Mitigación |
|---|---|---|---|
| Pérdida total de datos por `GET /migracion` | Media | Crítico | Retirar la ruta antes de cargar datos reales |
| Un usuario opera sobre una sede ajena | Media | Alto | Verificar `empleado_sede` en la selección y en las escrituras |
| Se vende facturación y no funciona | Alta si se ofrece | Alto | Terminarla o sacarla del alcance de la propuesta |
| El hosting cPanel no tiene PHP 8.5 o no ejecuta Composer | Media | Alto | Resolver el spike S0 antes de firmar; bajar a `^8.3` o versionar `vendor/` |
| Spam en endpoints públicos de citas | Alta | Medio | `throttle` antes del piloto |
| Regresiones por tests insuficientes o fallas preexistentes | Media | Alto | Corregir las fallas y cubrir el flujo completo |
| N+1 en dashboard con volumen real | Media | Medio | Eager loading antes del piloto |
| Falta de notificaciones frena la adopción | Alta | Alto | Email en las primeras 2 semanas |
| ~~SQLite en producción~~ | — | — | **Resuelto**: MySQL 8.4 |

### Riesgos legales y regulatorios

| Riesgo | Descripción |
|---|---|
| **HABEAS DATA (Ley 1581)** | Avance parcial: la edición de resultados se audita. Falta la bitácora de accesos a datos de pacientes. No bloquea el MVP, pero debe aparecer en el roadmap. |
| **Facturación electrónica DIAN** | Las resoluciones están modeladas; la e-facturación real es trabajo adicional. |
| **RIPS electrónico** | Hay exportación JSON/CSV; el envío a SISPRO es trabajo adicional. |

### Riesgos comerciales

| Riesgo | Descripción |
|---|---|
| **Continuidad del equipo** | Hoy hay dos desarrolladores. Conviene ofrecer documentación de entrega o código en escrow. |
| **Sin clientes de referencia** | Proponer un precio de cliente piloto a cambio de un caso de estudio. |
| **Competidores establecidos** | Diferenciarse por precio, modernidad, customización y aislamiento de datos. |

---

## 16. Resumen Ejecutivo

### Estado del proyecto

> DinamyLAB tiene **cerca del 75% del trabajo hecho** (antes 70%; esta revisión encontró bloqueantes que la anterior no registraba). En dos semanas se pasó de un entorno local con SQLite y Laravel 12 a un proyecto en Docker con MySQL, Laravel 13 y PHP 8.5, con un modelo de despliegue por cliente definido y documentado. También se cerraron brechas funcionales: auditoría de resultados, ingreso por lotes, reporte imprimible y búsqueda en todos los listados. Lo que falta es acotado y **no requiere rediseño**.

### Fortalezas para la propuesta

1. **Dominio colombiano capturado**: CUPS, CIE-10, RIPS, EPS, afiliación.
2. **Flujo clínico funcional**: orden → análisis → resultado (con auditoría) → reporte imprimible.
3. **Multi-sede y una instancia aislada por cliente.**
4. **Stack actualizado** con soporte de seguridad a largo plazo.
5. **Código 100% propio.**

### Brechas para producción

1. Autorización crítica: retirar `/migracion` y verificar la membresía de sede
2. Terminar la creación de facturas
3. Notificaciones (email como mínimo, WhatsApp como ideal)
4. PDF generado en servidor, para envío y archivo
5. Rate limiting en endpoints públicos
6. Cierre de la configuración de producción (spike `vendor/`, PHP del hosting, `APP_DEBUG`, CI)
7. Tests del flujo completo y corrección de fallas preexistentes

### Mensaje para la propuesta comercial

> *Para un laboratorio clínico pequeño en Colombia, DinamyLAB es una solución moderna, económica y totalmente customizable, en la que los datos de cada laboratorio viven en su propia base de datos. El sistema está a 5–7 semanas de producción, con un costo de desarrollo restante acotado.*

### Números clave

| Métrica | Valor | Antes |
|---|---|---|
| Horas para producción mínima | 63–94h | 73–104h |
| Inversión de desarrollo restante (mínima) | $5.0M – $7.5M COP | $5.8M – $8.3M |
| Precio sugerido primer cliente (mensual) | $400.000 – $600.000 COP/mes (borrador piloto: $249.000) | igual |
| Fee de implementación sugerido | $5M – $8M COP (borrador piloto: $3.5M) | igual |
| Tiempo hasta firma de aceptación | ~2–2.5 meses | ~2.5 meses |
| Break-even del desarrollo restante (1 cliente a $500K/mes, sin contar fee) | ~10–15 meses | ~16 meses |

---

*Análisis generado por Claude Opus 5.5 — DinamyLAB — 2026-11-01. Revisión de la versión del 2026-10-14 (Claude Sonnet 4.6).*

# Checklist de trabajo y deuda técnica

**Creado:** 2026-11-01 · **Reorganizado:** 2026-11-09
**Fuentes:** [ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md](./ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md), [MANUAL.md §7](./MANUAL.md#7-roles-permisos-y-sedes), [MATRIZ_ROLES_PERMISOS.md](./MATRIZ_ROLES_PERMISOS.md) y la lista de pendientes del equipo (2026-11-09).

La versión anterior, con el detalle completo de cada ítem cerrado, está en el commit `f48fd679` (`git show f48fd679:docs/CHECKLIST_DEUDA_TECNICA.md`).

## Cómo usarlo

El trabajo se agrupa en **frentes** (ED, OB, EX, IM, NT, AU) y en una lista de **deuda técnica** (DT). Cada fila es un issue. Dentro de cada frente, el orden de las filas es el orden sugerido de ejecución.

| Estado | Significado |
|---|---|
| `[ ]` | Pendiente |
| `[~]` | En curso |
| `[x]` | Hecho |
| `[?]` | Bloqueado por una decisión (ver [Decisiones pendientes](#decisiones-pendientes)) |
| `[O]` | Omitido: no se hará (explicar el motivo) |

**Carril:** `FE` frontend (Blade, React, CSS) · `BE` backend (rutas, controllers, services, modelos, migraciones, tests) · `Infra` hosting y CI · `Decisión` requiere acuerdo del equipo o del responsable funcional.

**Prioridad:** 🔴 bloquea producción o a otro ítem · 🟡 importante · 🟠 menor.

---

## Reglas para trabajar en paralelo sin interferir

1. **Dueño por carpeta.** `FE` toca `resources/js/`, `resources/views/` y `resources/css/`. `BE` toca `app/`, `routes/`, `database/`, `config/` y `tests/`. Si un ítem necesita tocar la carpeta del otro, se avisa en el PR y lo revisa el dueño.
2. **Contrato antes que código.** Cuando un ítem `FE` depende de uno `BE`, el `BE` publica primero el contrato (ruta, payload, respuesta JSON o variables que recibe la vista) en la descripción del issue. El `FE` empieza con datos de ejemplo y conecta cuando el endpoint esté en `main`. Los pares con contrato están marcados con ⇄.
3. **No versionar `public/build/` en las ramas de feature.** El PR #57 subió 25 archivos de build; si ambos lo hacen, `manifest.json` choca en cada merge. Hasta cerrar DT-01: los PR no incluyen `public/build/`, y quien hace el merge a `main` corre `npm run build` y sube un commit `chore: build`.
4. **Archivos compartidos:** `routes/web.php`, `routes/api.php`, `RolesYPermisosSeeder` y los tests son de `BE`. `layouts/app.blade.php` y `layouts/notifications.blade.php` son de `FE`. Un cambio de markup en `FE` que rompa un test (p. ej. `UiNotificationsTest`) se arregla en el mismo PR y lo revisa `BE`.
5. **Ramas cortas:** `feature/fe-<tema>` y `feature/be-<tema>`, un PR por ítem o par de ítems, y rebase sobre `main` a diario.

---

## Revisión 2026-11-09 (cambios desde la última actualización del checklist)

| PR | Qué cambió | Efecto en el checklist |
|---|---|---|
| #55 `fix/tipo-afiliacion` | Corrige los tipos de usuario en `FormPersona.jsx` | Sin ítem asociado |
| #56 `fix/duplicidad-rutas` | Quita `fecha` y `sede_id` de `OrdenStoreRequest`; la sede sale de la sesión autorizada | Refuerza C-05 (control de sede) |
| #57 `feature/remisiones` | Remisiones en el paso 2 de la orden: tabla `orden_remisiones` (CUP, nombre, valor, destino), `OrdenRemision`, cambios en `Api\OrdenController`, `DatosExamenes`, `TablaExamenes` y `ordenes/show`; una orden solo con remisiones no exige datos RIPS. +110 líneas en `OrdenesApiTest` | Nuevos ítems EX-07 y EX-08 (huecos del modelo). Subió `public/build/` (ver regla 3) |

**Hallazgos de esta revisión** (se convierten en ítems abajo):

- `Examen::categoria()` usa `categoria_id`, pero esa columna no existe en `examenes`. La tabla `categorias` tiene `examen_id` y `nombre` único, así que cada categoría solo puede tener un examen. La relación está invertida y rota (EX-02).
- `Examen::procedimiento()` es `belongsTo`, pero `examenes` no tiene `procedimiento_id`; debería ser `hasMany` (EX-02).
- `sexo` acepta valores distintos según el lugar: `H,M,I,F` en el alta y la edición, `M,F,I` en el filtro de `PersonaController::index` (ED-03).
- La edición web de persona (`personas/edidt.blade.php`) solo cubre nombres, fecha de nacimiento, sexo y un teléfono. Ya existe `PUT /api/personas/{id}` (ED-01, ED-02).
- `/administracion/configuracion` (`Setup.jsx`) solo activa o desactiva catálogos RIPS. No hay pantallas para editar la empresa, listar sedes o empleados, ni CRUD de exámenes (frente OB).
- `AdministracionController::caja()` pregunta por `Cache::has('Eps')` pero lee `Cache::get('EPS')` y despacha `TestJob` en cada petición sin caché (DT-14).

**Tests:** en esta revisión no se corrió la suite (la WSL no tiene Docker y el PHP local es 8.2). Último resultado registrado: 118 pasan, 8 `skipped`, 0 fallan (2026-11-05), sin contar los tests nuevos del PR #57.

---

## ED — Edición de persona y de resultados

| Estado | ID | Carril | Issue | Ubicación | Esfuerzo | Prio | Notas |
|---|---|---|---|---|---|---|---|
| `[ ]` | ED-01 ⇄ ED-02 | FE | Edición de persona con `FormPersona` en modo edición (mismos campos y validación que el alta), contra `GET`/`PUT /api/personas/{id}`. Reemplaza el formulario mínimo actual | `resources/js/components/FormPersona.jsx`, `resources/views/personas/` | 6–8h | 🟡 | Puede empezar ya contra la API actual; ajustar cuando cierre ED-02 |
| `[ ]` | ED-02 | BE | `UpdatePersonaRequest` compartido por web y API; al cambiar el teléfono, reemplazar el registro en vez de crear otro; cubrir documento, contacto, dirección y afiliación; renombrar la vista `edidt` → `edit` | `PersonaController`, `Api\PersonaController`, `app/Http/Requests/` | 4–6h | 🟡 | Absorbe parte de M-08. Publicar el contrato del `PUT` |
| `[?]` | ED-03 | BE | Unificar los valores de `sexo` (RIPS usa `H`, `M`, `I`); migrar los registros con `F` si existen | `StorePersonaRequest`, `PersonaController:26,133` | 1–2h | 🟡 | Decisión D-06 |
| `[ ]` | ED-04 ⇄ ED-05 | FE | Edición de resultados: confirmación con resumen (valor anterior → nuevo) en lugar del `confirm()` nativo, resaltar valores fuera de rango y mostrar el historial de cambios | `resources/views/resultados/edit.blade.php`, `components/parametro-edit` | 4–6h | 🟡 | Datos clínicos críticos: confirmar antes de guardar |
| `[ ]` | ED-05 | BE | Pasar a la vista el historial de `resultado_cambios` (quién, cuándo, motivo, antes/después) y ampliar `ResultadosEdicionTest` | `ResultadosController::edit()` | 2–3h | 🟡 | |

## OB — Configuración inicial (onboarding)

Ya existen: `empresa.show` (solo lectura), `admin/sedes` y `admin/empleados` (sin `index`), CRUD de convenios, `TarifasExamenesSede`, CRUD de resoluciones y la activación de catálogos RIPS en `Setup.jsx`.

| Estado | ID | Carril | Issue | Ubicación | Esfuerzo | Prio | Notas |
|---|---|---|---|---|---|---|---|
| `[?]` | OB-01 | Decisión | Definir los pasos y el mínimo para operar. Propuesta: Empresa → Sedes → Empleados y roles → Catálogos RIPS → Exámenes y perfiles → Convenios y tarifas → Resolución de facturación (opcional) | — | 2h | 🔴 | Decisión D-02. Bloquea OB-02 a OB-05 |
| `[ ]` | OB-02 ⇄ OB-05 | BE | API faltante: actualizar empresa, listar sedes, listar empleados, asignar sedes (`empleado_sede`) y rol; con los permisos de la matriz y tests | `EmpresaController`, `SedeController`, `EmpleadoController`, `routes/api.php` | 8–12h | 🔴 | |
| `[ ]` | OB-03 | BE | Endpoint de estado del onboarding: qué pasos están completos (hay empresa, al menos una sede, un empleado por sede, exámenes con tarifa…) | nuevo service | 2–4h | 🟡 | `instancia:verificar` solo revisa configuración, no datos |
| `[ ]` | OB-04 ⇄ OB-03 | FE | Asistente por pasos que enlace las pantallas existentes y las nuevas, con el estado de cada paso; llevar al admin al asistente mientras la instancia no esté lista | `resources/js/components/setup/` | 8–10h | 🟡 | Reutilizar el patrón de `StepperOrden` |
| `[ ]` | OB-05 | FE | Pantallas nuevas: editar empresa, listado de sedes y listado/alta de empleados con sedes y rol | `resources/views/admin/`, `resources/js/` | 8–12h | 🟡 | |

## EX — Catálogo de exámenes, perfiles y remisiones

**Cómo está hoy:** `examenes` → `parametros` (con `grupo` en texto para secciones dentro de un examen, p. ej. el hemograma) → `opciones` y `valor_referencias`. No hay forma de agrupar varios exámenes. La categoría está rota (ver hallazgos). Las remisiones guardan CUP, nombre, valor y destino como texto libre: el CUP no se valida contra `codigo_cups`, no hay relación con `examenes` ni con tarifas, no hay estado (enviada, recibida) ni resultado, y no salen en el reporte.

**Propuesta para EX-01** (a discutir):

- **Perfil** = agrupador de exámenes con nombre, CUP opcional y precio propio (tarifable como `Examen`). Tablas nuevas `perfiles` y `perfil_examen` (con `posicion`). Al ordenar un perfil se crea un procedimiento por examen, con `perfil_id` para agruparlos en el reporte y cobrar el precio del perfil. Qué exámenes trae cada perfil lo configura cada laboratorio.
- **Categoría** = área del laboratorio (química, hematología…). Nueva columna `examenes.categoria_id` en una migración; `categorias.examen_id` deja de usarse.
- **Remisión** = un examen puede marcarse como `remitido` con un destino por defecto. `orden_remisiones` gana `examen_id` (opcional), así un perfil puede mezclar exámenes locales y remitidos, y el resultado externo se adjunta (PDF del otro laboratorio) o se transcribe.
- Los seeders y el CRUD usan el **mismo service** para crear exámenes, para que ninguno de los dos deje el modelo a medias.

| Estado | ID | Carril | Issue | Ubicación | Esfuerzo | Prio | Notas |
|---|---|---|---|---|---|---|---|
| `[?]` | EX-01 | Decisión + BE | Cerrar el modelo de perfiles, categorías y remisiones (documento corto con el diagrama de tablas) | `docs/` | 3–4h | 🔴 | Decisión D-01. Bloquea EX-03 a EX-08 e IM-01 (agrupación del reporte) |
| `[ ]` | EX-02 | BE | Corregir `Examen::categoria()` y `Examen::procedimiento()` (debe ser `hasMany`), con tests | `app/Models/Examen.php` | 1–2h | 🟡 | No depende de EX-01 si solo se corrige `procedimiento()`; la categoría espera EX-01 |
| `[ ]` | EX-03 | BE | Migraciones, modelos y service de perfiles; creación de órdenes con perfiles y precio en `EscogerPrecioExamen` | `database/migrations/`, `app/Models/`, `app/Services/`, `Api\OrdenController` | 10–14h | 🟡 | |
| `[ ]` | EX-04 ⇄ EX-05 | BE | Incluir perfiles en `/api/static-data-for-frontend` e invalidar la caché con `CatalogoObserver` | `FrontendDataController`, `CatalogoObserver` | 2h | 🟡 | Publicar la forma del JSON |
| `[ ]` | EX-05 | FE | Elegir perfiles al crear la orden y mostrarlos agrupados (y desplegables) en la tabla de exámenes | `DatosExamenes.jsx`, `ModalExamenes.jsx`, `TablaExamenes.jsx` | 6–8h | 🟡 | Puede avanzar con datos de ejemplo |
| `[ ]` | EX-06 | BE + FE | CRUD de exámenes (parámetros, valores de referencia, opciones) y de perfiles, como paso del onboarding | `ParametroController`, `resources/js/` | 16–24h | 🟡 | Hoy solo existe `parametros.index`. Partir en issues BE y FE al empezar |
| `[ ]` | EX-07 | BE | Remisiones: validar el CUP contra `codigo_cups`, llenar `destino` con el nombre de la sede cuando viene `destino_sede_id`, relación opcional con `examen_id`, estado y adjunto del resultado externo | `Api\OrdenController`, `OrdenRemision`, migración nueva | 6–8h | 🟡 | |
| `[ ]` | EX-08 ⇄ EX-07 | FE | Seguimiento de remisiones en la orden (estado, cargar el resultado externo) y mostrarlas en el reporte | `ordenes/show.blade.php`, reporte (IM-02) | 4–6h | 🟠 | |

## IM — Impresión y PDF de resultados

**Cómo está hoy:** `resultados/show.blade.php` se imprime desde el navegador con las variantes `print:` de Tailwind; la tabla repite el encabezado por página con `<thead>` y el pie usa `position: absolute`. DomPDF 3.1 está instalado y sin uso.

**Recomendación:** una sola plantilla Blade del reporte, maquetada con **tablas y CSS propio en `<style>`**, que sirva para la vista previa en el navegador y para el PDF de DomPDF. DomPDF no soporta flex ni grid, y Tailwind depende de ellos, así que esa plantilla no usa clases de Tailwind. Reglas de paginación:

- Cada examen va en su propio bloque con `page-break-inside: avoid`; un examen nunca queda partido salvo que no quepa en una página.
- Si un examen no cabe (hemograma completo, perfiles grandes), se parte entre grupos de parámetros y se repite el título con "(continuación)".
- Encabezado (logo, datos del paciente y de la orden) y pie (firma, página X de Y) con `position: fixed`, que DomPDF repite en cada página.
- Probar con el examen más largo y con una orden de 10 o más exámenes.

| Estado | ID | Carril | Issue | Ubicación | Esfuerzo | Prio | Notas |
|---|---|---|---|---|---|---|---|
| `[ ]` | IM-01 ⇄ IM-02 | BE | Service que arma los datos del reporte (paciente, orden, exámenes agrupados, resultados con referencia, firmas, remisiones) y ruta `GET /resultados/orden/{orden}/pdf` con permiso, `ControlSede` y registro de auditoría | `app/Services/`, `ResultadosController`, `routes/web.php` | 6–8h | 🔴 | Publicar la estructura de `$reporte`. Reemplaza el antiguo C-07. Las firmas no están en git: copiar a mano en cada instancia |
| `[x]` | IM-02 | FE | Plantilla `resultados/reporte.blade.php` en tablas con CSS compatible con DomPDF y las reglas de paginación | `resources/views/resultados/` | 8–10h | 🔴 | 2026-11-10: `reporte.blade.php`, `reporte/_examen`, `reporte/_parametro` y datos ficticios en `reporte/ejemplo.php`. Sirve para una orden, un examen o varios de distintas órdenes (historia). Contrato `$reporte` abajo |
| `[?]` | IM-03 | FE | Botones "Descargar PDF" e "Imprimir" en el resultado y en la orden; migrar `resultados/show` y `resultados/historia_show` a `resultados.reporte`; retirar el layout `print:` de Tailwind cuando el PDF esté validado | `resultados/show.blade.php`, `resultados/historia_show.blade.php`, `ordenes/show.blade.php` | 3–4h | 🟡 | Decisión D-05 (imprimir solo algunos exámenes; `historia_show` ya lo hace) |
| `[ ]` | IM-04 | BE | Tests: el PDF se genera (200 y `application/pdf`) y respeta la sede y los permisos | `tests/Feature/` | 2h | 🟡 | |

**Contrato `$reporte` (IM-01 ⇄ IM-02, propuesto por FE el 2026-11-10).** La plantilla recibe `$reporte` y `$enPdf`; sin `$reporte` usa `resources/views/resultados/reporte/ejemplo.php`.

```php
$reporte = [
    'laboratorio' => [
        'nombre_comercial', 'sede', 'nit',
        'telefonos' => ['...'],          // todos los de la sede
        'direccion',
        'email' => ?string,              // de la sede o, si no tiene, de la empresa
        'logo_src' => ?string,           // data URI (DomPDF no descarga asset() sin isRemoteEnabled)
    ],
    'paciente' => ['nombre', 'documento', 'sexo', 'edad', 'fecha_nacimiento'], // sexo en texto: Masculino/Femenino/Indeterminado
    'orden' => ['numero', 'fecha_atencion'] | null,  // null cuando el reporte mezcla órdenes (historia)
    'fecha_emision', 'total_examenes',
    'examenes' => [[
        'nombre', 'orden_numero', 'fecha_validacion',
        'bacteriologo' => ['nombre', 'documento', 'firma_src' => ?string], // firma en data URI
        'grupos' => [[
            'nombre' => ?string,         // Parametro::grupo; null para parámetros sin grupo
            'parametros' => [[
                'nombre', 'metodo' => ?string, 'resultado', 'unidades', 'referencia',
                'fuera_de_rango' => bool, 'tipo' => 'valor' | 'texto', // texto = textarea/observaciones, a todo el ancho
            ]],
        ]],
    ]],
    'remisiones' => [['cup', 'nombre', 'destino']],
];
```

Notas para el BE:

- Un solo service que arme `$reporte` desde una lista de procedimientos, para IM-01 (orden completa) y luego para `show` y `historia_show` (IM-03). Partir de `EscogerReferencia::obtenerResultados()`, agrupando los parámetros por `grupo` en orden de `posicion` y pasando `es_normal` a `fuera_de_rango`.
- Render: `Pdf::loadView('resultados.reporte', ['reporte' => $reporte, 'enPdf' => true])->setPaper('letter')`.
- La plantilla usa Helvetica (fuente base de PDF, Latin-1): tildes, ñ, µ y ³ salen bien; caracteres como ≤, ≥ o ⁶ no. Si los catálogos los tienen, cambiar a DejaVu Sans.
- El pie muestra "Página N". Para "Página N de M", DomPDF necesita `$pdf->getDomPDF()->getCanvas()->page_text(...)` con `{PAGE_NUM}` y `{PAGE_COUNT}` desde el controller (`counter(pages)` no existe en DomPDF); si se hace, avisar a FE para quitar el número del HTML.
- Paginación: un examen nunca se parte salvo que no quepa en una página; entonces se parte entre grupos (y, como último recurso, por filas) con "(continuación)" y la firma en el último trozo. Las alturas están calibradas en `reporte.blade.php` para carta con los márgenes actuales.
- En el navegador (`$enPdf = false`) el encabezado y el pie salen una vez por documento: Chrome no repite bien los `position: fixed` al imprimir. El encabezado en cada página es del PDF.

## NT — Notificaciones (toasts y correo)

| Estado | ID | Carril | Issue | Ubicación | Esfuerzo | Prio | Notas |
|---|---|---|---|---|---|---|---|
| `[?]` | NT-01 | FE | Toasts en lugar de los avisos fijos del encabezado: un solo módulo para los flash de Blade (`success`, `error`, `warning`, `info`) y para las islas React (función `toast()` o evento en `window`). Mantener `role` y `aria-live`; los errores de validación siguen junto a cada campo; los errores no se cierran solos | `layouts/notifications.blade.php`, `resources/js/` | 4–6h | 🟡 | Decisión D-07 (librería o propio). Actualizar `UiNotificationsTest` en el mismo PR |
| `[~]` | NT-02 | Infra | Un mailer para 2 instancias con 1 repo: **sí se puede.** El código es el mismo y cada instancia tiene su `.env` con `MAIL_*`; pueden compartir la cuenta SMTP o el proveedor y cambiar `MAIL_FROM_NAME`. Falta comprobar: SPF, DKIM y DMARC del dominio remitente, límite de envíos por hora del hosting (se comparte), y colas sin worker (`QUEUE_CONNECTION=database` más un cron con `queue:work --stop-when-empty`) | `.env.example`, `DESPLIEGUE_INSTANCIAS.md` | 3–4h | 🟡 | Hacerlo junto con DT-01 |
| `[?]` | NT-03 | Decisión | Qué se notifica y a quién. Propuesta: "resultados listos" al paciente, con un enlace firmado y con vencimiento al PDF; sin datos clínicos en el asunto ni en el cuerpo, sin adjuntos | — | 1h | 🟡 | Decisión D-03. Depende de IM-01 |
| `[ ]` | NT-04 | BE | Notification y Mailable, ruta firmada de descarga, cola y tests con `Mail::fake()` | `app/Notifications/`, `routes/web.php` | 8–10h | 🟡 | Antes C-08. WhatsApp queda para una fase posterior |
| `[ ]` | NT-05 ⇄ NT-04 | FE | Maqueta del correo (plantilla Markdown de Laravel con la marca de la instancia) | `resources/views/mail/` | 2–3h | 🟠 | |

## AU — Auditoría, bitácora y Habeas Data

**Cómo está hoy:** solo se audita la edición de resultados (`resultado_cambios`). Hay `Log::` en 8 archivos, sin formato común. No se registran lecturas, inicios de sesión ni exportaciones RIPS.

| Estado | ID | Carril | Issue | Ubicación | Esfuerzo | Prio | Notas |
|---|---|---|---|---|---|---|---|
| `[?]` | AU-01 | Decisión | Política de auditoría y seguridad: eventos que se registran (crear, editar y borrar persona, orden y resultado; ver y descargar reportes; exportar RIPS; inicio de sesión y fallos; cambios de rol), cuánto tiempo se guardan y quién los consulta | `docs/` | 3–4h | 🔴 | Decisión D-04. Marco: Ley 1581 de 2012, Decreto 1377 de 2013 y Resolución 1995 de 1999 (historia clínica). Validar con asesoría legal |
| `[?]` | AU-02 | BE | Registro de auditoría: usuario, acción, entidad, ID, sede, IP y fecha, más los cambios sin PII en claro. Eventos de modelo por trait u observer; lecturas por middleware | migración nueva, `app/` | 10–14h | 🔴 | Decisión D-08: `spatie/laravel-activitylog` (requiere aprobación) o una tabla propia. Antes I-04 |
| `[ ]` | AU-03 | BE | Canal de log `auditoria` y formato común en los `Log::` existentes; revisar que ninguno escriba PII | `config/logging.php`, `app/` | 2–3h | 🟡 | |
| `[ ]` | AU-04 ⇄ AU-02 | FE | Bitácora para el admin con filtros por usuario, fecha y entidad | `resources/views/admin/` | 6–8h | 🟡 | |
| `[ ]` | AU-05 | BE + FE | Autorización de tratamiento de datos en el alta de persona: BE guarda fecha y versión del aviso; FE muestra el aviso y la casilla | `personas`, `FormPersona.jsx` | 4–6h | 🔴 | Requisito de Habeas Data |
| `[ ]` | AU-06 | FE | Páginas de política de privacidad y de seguridad | `resources/views/` | 2h | 🟡 | El texto lo entrega el responsable funcional |
| `[ ]` | AU-07 | BE | API Resources para no devolver modelos Eloquent completos (reduce la PII expuesta) | `app/Http/Controllers/Api/` | 8–12h | 🟡 | Antes M-09 |
| `[ ]` | AU-08 | BE | Cifrado en reposo de la PII de pacientes | `personas`, contactos | Por estimar | 🟠 | Antes M-11. Evaluar después de AU-01 |

## DT — Deuda técnica

| Estado | ID | Carril | Issue | Ubicación | Esfuerzo | Prio | Notas |
|---|---|---|---|---|---|---|---|
| `[~]` | DT-01 | Infra | Spike S0: ¿el hosting ejecuta Composer y npm? ¿Qué versión de PHP ofrece? De eso depende si se versionan `vendor/` y `public/build/` | `composer.json`, `.cpanel.yml` | 2–4h | 🔴 | Antes C-04. Ver regla 3 |
| `[ ]` | DT-02 | BE | Borrar el bloque comentado de la ruta `/migracion` | `routes/web.php:28-35` | 0.5h | 🟠 | Resto de C-01 |
| `[ ]` | DT-03 | BE | Quitar cédulas de profesionales de `EmpresaSeeder` y de los valores por defecto de `json_rips` | `EmpresaSeeder`, `ProcedimientoController`, `Api\ResultadosController` | 1h | 🟡 | Resto de C-10 |
| `[ ]` | DT-14 | BE | `caja()`: clave de caché `Eps`/`EPS` inconsistente y `TestJob` en cada petición sin caché | `AdministracionController.php:14-40` | 1h | 🟠 | Nuevo |
| `[?]` | DT-04 | BE | `Api\ExamenesController::obtenerPrecio`: publicar la ruta o borrar el método y sus 8 tests `skipped` | `ExamenesPrecioApiTest` | 1–2h | 🟠 | Antes I-11. Revisar junto con EX-03 |
| `[ ]` | DT-05 | BE | Quitar N+1 del dashboard y del listado de personas | `EmpleadoController::dashboard()`, `Api\ListaPersonasController` | 6–10h | 🟡 | Antes I-05 |
| `[ ]` | DT-06 | BE | Test de extremo a extremo: orden (con perfil y remisión) → procedimiento → resultado → PDF | `tests/Feature/` | 10–14h | 🔴 | Antes I-08. Completar a medida que cierren EX e IM |
| `[ ]` | DT-07 | BE + FE | Una sola fuente de verdad para la sede activa (hoy sesión PHP y estado de React) | `resources/js/`, `SedeController` | 3–4h | 🟡 | Resto de M-07 (la caché ya está) |
| `[?]` | DT-08 | Decisión | Cargador de listas: ¿selección asistida (actual) o importación masiva real? | `ParseadorListaPersonas` | Decisión + 6–10h | 🟡 | Antes I-07. La propuesta comercial ofrece "carga masiva" |
| `[ ]` | DT-09 | BE | Validar `OpcionController` y `TributoController` | | 1–2h | 🟠 | Antes M-03 |
| `[ ]` | DT-10 | BE | País "170" y valores fijos (tipos de tarifa, vencimiento de 30 días) a constantes o configuración | `GuardarPersona`, `EscogerPrecioExamen`, `FacturaController:92` | 3–5h | 🟠 | Antes M-04 y M-05 |
| `[ ]` | DT-11 | BE | Transacciones en las operaciones masivas | `ListaPersonasController`, `ReprocesarController` | 2–4h | 🟠 | Antes M-06 |
| `[ ]` | DT-12 | BE | Validación con Form Requests en lugar de inline | Controllers | 4–8h | 🟠 | Antes M-08. Avanza con ED-02 |
| `[ ]` | DT-13 | BE | Lógica de controllers a services y unificar web/API (persona, orden, resultados) | 75 controllers | Continuo | 🟠 | Antes I-09 e I-10. Hacerlo al tocar cada módulo |
| `[ ]` | DT-15 | BE + FE | Migrar la autenticación de Breeze a Fortify | `routes/auth.php` | 8–12h | 🟠 | Antes M-10. Opcional |

---

## Decisiones pendientes

| ID | Decisión | Bloquea | Quién |
|---|---|---|---|
| D-01 | Modelo de perfiles, categorías y remisiones (propuesta en EX) | EX-01 a EX-08, IM-01 | Ambos + responsable funcional |
| D-02 | Pasos y mínimo del onboarding | OB-01 a OB-05 | Ambos |
| D-03 | Qué se notifica por correo y a quién | NT-03, NT-04 | Responsable funcional |
| D-04 | Política de auditoría y retención | AU-01, AU-02, AU-08 | Responsable funcional + legal |
| D-05 | ¿Se imprimen solo algunos exámenes de una orden? | IM-03 | Responsable funcional |
| D-06 | Valores válidos de `sexo` | ED-03 | BE |
| D-07 | Toasts: librería (necesita aprobación) o módulo propio (~100 líneas, sin dependencias). Recomendación: propio | NT-01 | Ambos |
| D-08 | Auditoría: `spatie/laravel-activitylog` (necesita aprobación) o tabla propia | AU-02 | Ambos |

---

## Reparto sugerido

`FE` y `BE` trabajan en paralelo en cada iteración; ⇄ marca los pares donde el `BE` entrega el contrato al inicio de la iteración.

**Iteración 1: lo que no espera decisiones**

| FE | BE |
|---|---|
| NT-01 toasts | ED-02 Form Request de persona (contrato para ED-01) |
| ED-01 edición de persona | ED-05 historial de resultados (contrato para ED-04) |
| ED-04 edición de resultados | EX-02 relaciones rotas · DT-02 · DT-03 · DT-14 |
| IM-02 plantilla del reporte con datos de ejemplo | IM-01 service y ruta del PDF (contrato `$reporte` el primer día) |
| | DT-01 y NT-02: spike de hosting y correo |

En paralelo, juntos: cerrar D-01, D-02, D-06, D-07 y D-08, y pedir al responsable funcional D-03, D-04 y D-05.

**Iteración 2: perfiles, PDF y onboarding**

| FE | BE |
|---|---|
| IM-03 botones de PDF e impresión | IM-04 tests del PDF |
| EX-05 perfiles en la orden | EX-03 y EX-04 perfiles |
| OB-04 asistente · OB-05 pantallas | OB-02 API de onboarding · OB-03 estado |

**Iteración 3: auditoría, correo y remisiones**

| FE | BE |
|---|---|
| AU-04 bitácora · AU-06 políticas | AU-02 y AU-03 auditoría |
| AU-05 aviso de datos (FE) | AU-05 aviso de datos (BE) |
| NT-05 plantilla de correo | NT-04 envío de correo |
| EX-08 seguimiento de remisiones | EX-07 remisiones |
| EX-06 CRUD de exámenes (FE) | EX-06 CRUD de exámenes (BE) · DT-06 test de extremo a extremo |

Después: el resto de DT y AU-07, AU-08.

---

## Archivo

### Cerrados

| ID | Issue | Cierre |
|---|---|---|
| C-01 | Retirar `GET /migracion` | 2026-11-03, `e9d2e1fd`. Queda el bloque comentado (DT-02) |
| C-03 | Sacar del repo y del historial los datos reales de pacientes | 2026-11-05, `git filter-repo` y force push; GitHub Support purgó `refs/pull/*`. Todo clon anterior al 2026-11-05 tiene los datos y no se debe usar |
| C-05 | Control de sede en `elegirSede()`, `store` de resultados (web y API) y órdenes | 2026-11-03/04, `ControlSede`, PR #52; reforzado en #56 |
| C-09 | Endpoints públicos de citas | 2026-11-03, módulo retirado; migración `drop_pre_registros_citas_table` |
| C-10 | Credenciales y datos de prestadores en `rips()` | 2026-11-05, métodos eliminados y contraseñas rotadas. Quedan cédulas en seeders (DT-03) |
| I-01 | CI en `main` | Hecho |
| I-02 | Fallas de tests (factories, `HasFactory`, header) | 2026-11-03 |
| I-03 | Matriz de roles y permisos | 2026-11-04, PR #52. `SedePolicy` sigue sin invocarse |
| I-06 | Edición web de persona (`edit`, `update`, `destroy`) | 2026-11-04, PR #52. Lo que falta pasa a ED-01 y ED-02 |
| M-01 | `Empleado->nombre()` sin `return` | 2026-11-03 |
| M-02 | `EmpresaPolicyTest` alineado con las rutas | 2026-11-04, PR #52 |
| M-12 | Tablas de referencia RIPS del merge `conciliador` | 2026-11-05: se borraron 12 tablas sin uso, quedan 28 |

### Omitidos y reabiertos

| ID | Issue | Estado |
|---|---|---|
| C-02 | `APP_DEBUG=false` en producción y verificación en `instancia:verificar` | Omitido |
| C-06 | Terminar la creación de facturas | Omitido para el MVP |
| C-07 | PDF del reporte en servidor | **Reabierto** como frente IM (2026-11-09) |
| C-08 | Notificaciones por correo | Pasa a NT-02 a NT-05 |
| I-04, M-09, M-11 | Auditoría, API Resources, cifrado | Pasan a AU-02, AU-07, AU-08 |

Al reabrir C-07 y agregar los frentes OB, EX, NT y AU cambian las horas y el alcance de la propuesta: actualizar §13 y §14 de [ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md](./ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md).

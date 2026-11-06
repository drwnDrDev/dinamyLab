# Checklist de deuda técnica

**Creado:** 2026-11-01
**Fuente:** [ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md](./ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md) (§6 infraestructura, §8 deuda técnica, §9 tests, §11 seguridad) y [MANUAL.md §7](./MANUAL.md#7-roles-permisos-y-sedes).

Cada fila es un issue. Se ordenan **por prioridad** (crítico → importante → menor) y, dentro de cada prioridad, **por viabilidad** (lo más rápido y sin bloqueos primero).

## Cómo usarlo

**Estado** (primera columna):

| Marca | Significado |
|---|---|
| `[ ]` | Pendiente |
| `[~]` | En curso |
| `[x]` | Hecho |
| `[D]` | A deprecar: la funcionalidad se va a quitar del proyecto |
| `[O]` | Omitido: no se hará (explicar el motivo en Comentarios) |

**Viabilidad:**

| Nivel | Criterio |
|---|---|
| **Alta** | ≤ 4h, sin dependencias ni decisiones pendientes |
| **Media** | 4–12h, o requiere una decisión de producto o hosting |
| **Baja** | > 12h, depende de un servicio externo, o es un refactor continuo |

**Área:** `BE` backend · `FE` frontend (Blade/React) · `Infra` hosting/CI · `Repo` repositorio. Mientras el alcance del equipo sea solo frontend (ver [CLAUDE.md](./CLAUDE.md)), los ítems `BE` son para el responsable backend.

**Comentarios:** decisiones, motivos para deprecar u omitir, enlaces a PRs.

---

## 🔴 Crítico — bloqueante para producción

| Estado | ID | Issue | Ubicación | Esfuerzo | Viabilidad | Área | Comentarios |
|---|---|---|---|---|---|---|---|
| `[x]` | C-01 | Retirar la ruta `GET /migracion`, que ejecuta `migrate:fresh --seed` y la puede llamar el rol `prestador` | `routes/web.php:28-35` | 0.5–1h | Alta | BE | **Hecho 2026-11-03** (`e9d2e1fd`): la ruta quedó comentada, ya no se registra. Queda como código muerto; conviene borrar el bloque. |
| `[O]` | C-02 | Dejar `APP_DEBUG=false` como valor de producción y hacer que `instancia:verificar` falle si está en `true` con `APP_ENV=production` | `.env.example`, `app/Console/Commands/VerificarInstancia.php` | 1–2h | Alta | BE | |
| `[x]` | C-03 | Sacar del repositorio los datos reales de pacientes y reescribir el historial de git | `resources/utils/rips_julio.json`, `bitacora.json`, `json_de_pueba.json`, `tablas/LogsBaseEntity.csv`; ruta `doc_sandra/` en `AdministracionController.php:66` | 2–4h + coordinación | Media | Repo | Detectado el 2026-11-01; no aparece en el doc de viabilidad. Requiere force push coordinado con el otro desarrollador. **Revisión 2026-11-05:** los archivos ya no están en el árbol, pero el historial **no** se ha reescrito: `rips_julio.json`, `bitacora.json` y `json_de_pueba.json` siguen en commits alcanzables desde `main` y `origin/main`. `AdministracionController.php:66` aún apunta a `doc_sandra/bitacora.csv`. El merge `conciliador` volvió a agregar `resources/utils/tablas/` (ver M-12); `LogsBaseEntity.csv` ahora solo trae una fila de ejemplo. Falta: reescribir historial y quitar la ruta `doc_sandra`. **Revisión 2026-11-05 (`823ef980`):** la ruta `doc_sandra` se cambió por `public/archivos/ejemplo_rips.csv`, pero ese archivo no existe, así que `GET /caja/ingresos` responde 404. El historial **sigue sin reescribir**: los tres JSON con datos de pacientes aparecen en 8 commits alcanzables desde `main` y `origin/main`. Queda pendiente el `git filter-repo` y el force push coordinado. **2026-11-05:** al revisar el historial completo aparecieron muchos más archivos con datos de pacientes y prestadores: bitácoras mensuales por doctora (`tablas_de_referencia/<mes>/doctora_*/`), salidas del validador del MSPS (`ResultadosMSPS__*.txt`), `prueba/*/rips_julio.json`, `usuarios (4).json`, `biotek_septiembre.json` y firmas escaneadas (`app_storage_utils/firmas/*.png`). La lista para purgar tiene 191 rutas de `resources/utils/` y `utils/` que ya no existen en el árbol. Se eliminó `rips()`, así que ya no hay ruta a `doc_sandra` ni a `ejemplo_rips.csv`. **Hecho 2026-11-05:** historial reescrito con `git filter-repo` (191 rutas de `resources/utils/` y `utils/`, las firmas de `public/storage/firmas/` y `storage/app/public/firmas/`, y las contraseñas reemplazadas por `***ELIMINADO***`). Force push de `main` y `feature/chahe-local` (que conserva los 11 commits de caché de catálogos); se borraron `conciliador`, `archivos-borrados` y `fix/sede-precios-roles`. Un clon nuevo confirma que no quedan rastros en las ramas. Las firmas quedan en `.gitignore` y hay que copiarlas a mano en cada instalación. **Pendiente fuera del repo:** GitHub conserva 39 `refs/pull/*` con el historial viejo, que solo puede purgar GitHub Support. Todo clon anterior al 2026-11-05 sigue teniendo los datos y hay que borrarlo y volver a clonar. |
| `[~]` | C-04 | Spike S0: confirmar si el hosting ejecuta Composer (si no, volver a versionar `vendor/`) y qué versión de PHP ofrece (si no es 8.5, bajar a `^8.3`) | `composer.json`, `.cpanel.yml`, `DESPLIEGUE_INSTANCIAS.md` | 2–4h | Media | Infra | |
| `[x]` | C-05 | Control de sede: `elegirSede()` debe verificar `empleado_sede`, y `store` de resultados (web y API) debe aplicar el mismo control que `update` | `SedeController::elegirSede()`, `ResultadosController::store()`, `Api/ResultadosController` | 6–10h | Media | BE | Pruebas de aceptación SEDE-01 a SEDE-08 en `MANUAL.md` §7.5. **Hecho 2026-11-03:** control centralizado en `App\Services\ControlSede` (también en `create`, que mostraba datos de otra sede). La API ahora guarda como `empleado_id` al empleado autenticado; antes guardaba el `user_id` o el valor enviado por el cliente. Tests en `ControlSedeTest`. **2026-11-04** (PR #52): se añadieron `autorizarOrden()` y `autorizarSedeActiva()` (usadas en órdenes, personas y reportes), y el rol `admin` queda exento del control de sede. |
| `[~]` | C-10 | Quitar del código las credenciales y los datos de prestadores: `rips()` tiene en texto plano dos perfiles con nombre, cédula (`usuario`) y contraseña (`clave`). Hay que rotar esas contraseñas, pasarlas a `.env` o a la BD (cifradas) y sacarlas del historial | `app/Http/Controllers/AdministracionController.php:71-96` | 1–2h + rotación | Alta | BE + Repo | Detectado el 2026-11-05; se introdujo en `19d992f4`. La ruta solo la puede usar `admin`, pero cualquiera con acceso al repositorio ve las claves. **Rotarlas aunque se limpie el historial.** Limpiar el historial en la misma operación que C-03. **2026-11-05:** `AdministracionController` queda solo con `caja()` y `setup()`; se eliminaron `rips()`, `json_rips()` e `index()` (ninguna tenía uso aparte de la ruta `/caja/ingresos`, que también se quitó). Las contraseñas ya se reemplazaron en el historial (ver C-03). **Falta rotarlas** en el servicio donde se usan; el ítem se cierra cuando estén cambiadas. Siguen cédulas de profesionales en `EmpresaSeeder` y como valores por defecto en `ProcedimientoController::json_rips` y `Api\ResultadosController::json_rips`; revisarlas aparte. |
| `[O]` | C-06 | Terminar la creación de facturas: quitar el `dd()`, guardar y probar | `FacturaController.php:95` | 6–10h | Media | BE | Decidir si la facturación entra en la propuesta comercial; si no, puede quedar como `[O]` para el MVP |
| `[O]` | C-07 | Generar el PDF del reporte en servidor con DomPDF, reutilizando la vista imprimible | `resources/views/resultados/show.blade.php` | 8–12h | Media | BE + FE | DomPDF ya está instalado. Requisito previo para enviar resultados por correo (C-08)/ omitimos por ahora, se desarrollara en el futuro |
| `[ ]` | C-08 | Notificaciones por email (WhatsApp como fase posterior) | `config/mail.php`, `MAIL_MAILER=log` | 15–20h | Baja | BE + Infra | El `TODO` actual está en el pre-registro público de citas (ver C-09). Si ese flujo se retira, redefinir qué se notifica (p. ej. resultados listos) |
| `[x]` | C-09 | Rate limiting en los endpoints públicos de citas | `routes/api.php:24-26` (`citas/pre-registrar`, `citas/pre-registrar-lista`, `citas/consultar/{codigo}`) | 4–6h | — | BE | **A deprecar:** los endpoints públicos se van a quitar del proyecto, así que no hace falta limitarlos. Al retirarlos, revisar también las rutas web públicas de `routes/web.php:35-38` (`/citas/registrar`, `/citas/confirmacion/{codigo}`, `/citas/confirmar/{codigo}`), los componentes `FormPreRegistroCita` y `RecepcionCitas`, `PreRegistroCitaController` (web y API), `PreRegistroCitaPolicy` y la tabla `pre_registros_citas` (no borrar migraciones; crear una nueva si se elimina la tabla). **Hecho 2026-11-03:** se retiró el módulo completo, incluidas las rutas internas `/citas/*` y `recepcion/pre-registros/*`; la tabla se elimina con la migración `2026_11_03_000000_drop_pre_registros_citas_table`. Antes de migrar en producción, exportar los pre-registros si se quieren conservar. |

**Subtotal (sin deprecados):** 42–65h

---

## 🟡 Importante

| Estado | ID | Issue | Ubicación | Esfuerzo | Viabilidad | Área | Comentarios |
|---|---|---|---|---|---|---|---|
| `[x]` | I-01 | Configurar el CI para que corra en `main` (también corre en `master` y `*.x`) | `.github/workflows/tests.yml` | 0.5–1h | Alta | Infra | |
| `[x]` | I-02 | Corregir las fallas de tests reportadas el 2026-10-29: `HasFactory` en `Convenio`, `tipo_documento_id` inexistente en la factory de `Empleado`, `->sedes` sobre `null` en `header.blade.php` | `app/Models/Convenio.php`, `database/factories/EmpleadoFactory.php`, `resources/views/layouts/header.blade.php` | 2–4h | Alta | BE + FE | **Hecho 2026-11-03.** La columna sí existía: la factory fallaba por FK con ids fijos (`tipo_documento_id=1`, `empresa_id=1`); ahora usa factories. Se agregó `ConvenioFactory`. Al correr, aparecieron I-11 y los fallos de M-02. |
| `[x]` | I-03 | Matriz de roles y permisos: dividir `can:eliminar_persona` del grupo admin, sembrar los permisos que faltan, crear `OrdenPolicy` y `ProcedimientoPolicy` e invocar `SedePolicy` | `routes/web.php`, `RolesYPermisosSeeder`, `app/Policies/` | 6–8h | Media | BE | Requiere que el responsable funcional apruebe la matriz. **2026-11-03:** propuesta en [MATRIZ_ROLES_PERMISOS.md](./MATRIZ_ROLES_PERMISOS.md), pendiente de aprobación. Incluye el mapa de rutas a permisos y hallazgos nuevos (`hasRole('administrador')` inexistente, `eliminar_orden_medica` sin sembrar). **Hecho 2026-11-04** (PR #52): matriz implementada en rutas web/API con `can:<permiso>` y alias de middleware Spatie; `RolesYPermisosSeeder` idempotente con permisos por rol; `eliminar_persona` ya es un permiso propio; los dos hallazgos ya no aparecen en el código. En lugar de `OrdenPolicy`/`ProcedimientoPolicy` se usan permisos de ruta + `ControlSede`. `SedePolicy` sigue sin invocarse. Tests en `Authorization/PermissionProfilesTest` (10 casos). |
| `[ ]` | I-04 | Auditoría HABEAS DATA: registrar accesos y cambios a datos de pacientes y órdenes con formato común (usuario, acción, ID) | `app/` (19 llamadas a `Log::`) | 6–10h | Media | BE | La edición de resultados ya se audita en `resultado_cambios` |
| `[ ]` | I-05 | Eliminar N+1 en el dashboard y en la lista de personas | `EmpleadoController::dashboard()`, `Api/ListaPersonasController` | 6–10h | Media | BE | |
| `[x]` | I-06 | Implementar la edición web de persona (`edit`, `update`, `destroy` están vacíos) | `PersonaController`, `resources/views/personas/` | 8–10h | Media | BE + FE | **Hecho 2026-11-04** (PR #52): `edit`, `update` y `destroy` implementados; el borrado es lógico (migración `add_soft_deletes_to_personas_table`). Observaciones: la vista se llama `personas/edidt.blade.php` (errata); la validación es inline (ver M-08); al cambiar el teléfono se crea un registro nuevo en vez de reemplazar el anterior. |
| `[ ]` | I-07 | Definir el alcance del cargador de listas: ¿selección asistida (actual) o importación masiva real? | `ParseadorListaPersonas`, `Api/ListaPersonasController` | Decisión + 6–10h si es importación | Media | BE + FE | La propuesta comercial ofrece "carga masiva" |
| `[ ]` | I-08 | Test de extremo a extremo del flujo orden → procedimiento → resultado → reporte, y tests de autorización | `tests/Feature/` | 10–14h | Media | BE | Incluir los casos de `MANUAL.md` §7.5. La parte de autorización avanzó con `PermissionProfilesTest` y `ControlSedeTest`; falta el flujo de extremo a extremo. |
| `[ ]` | I-09 | Mover a services la lógica que sigue en controllers de los módulos antiguos | 75 controllers / 11 services | Continuo | Baja | BE | Hacerlo a medida que se toque cada módulo, no como tarea aparte |
| `[ ]` | I-10 | Unificar la duplicación web/API (validación de persona, creación de órdenes, captura de resultados) | `PersonaController` / `Api/PersonaController`, `OrdenController` / `Api/OrdenController`, `ResultadosController` / `Api/ResultadosController` | 8–12h | Baja | BE | Depende de I-09 |
| `[~]` | I-11 | `EscogerPrecioExamen` filtra por `tarifas.sede_id`, que no existe (la relación sede–tarifa está en `sede_tarifa`): la consulta falla, el `catch` la oculta y siempre devuelve el precio base. Además, `Api\ExamenesController::obtenerPrecio` no tiene ruta en `routes/api.php` | `app/Services/EscogerPrecioExamen.php`, `app/Http/Controllers/Api/ExamenesController.php`, `tests/Unit/EscogerPrecioExamenTest.php`, `tests/Feature/ExamenesPrecioApiTest.php` | 2–4h | Alta | BE | Detectado el 2026-11-03. Hoy nadie usa el servicio (no hay impacto en producción). Los 14 tests están marcados como `skipped`; al corregir, quitar el `markTestSkipped` y vincular las tarifas por `sede_tarifa` en los tests. Ver la referencia en `Api\TarifaController`. Decidir si el endpoint se publica. **2026-11-03:** servicio corregido (relación `Tarifa::sedes()` y exclusión de tarifas de otros convenios); los tests unitarios pasan. Falta decidir si se publica el endpoint (`ExamenesPrecioApiTest` sigue en `skipped`). |

**Subtotal (sin I-09 continuo ni I-07):** 47–69h

---

## 🟠 Menor

| Estado | ID | Issue | Ubicación | Esfuerzo | Viabilidad | Área | Comentarios |
|---|---|---|---|---|---|---|---|
| `[x]` | M-01 | `Empleado->nombre()` no tiene `return` | `app/Models/Empleado.php:29-31` | 0.5h | Alta | BE | |
| `[x]` | M-02 | Alinear `EmpresaPolicyTest` con las rutas reales (`admin.sedes.*`) y agregar las aserciones que faltan | `tests/Feature/Policies/EmpresaPolicyTest.php` | 1–2h | Media | BE | 2026-11-03: 7 de los 9 tests fallan porque el test no siembra roles ni permisos (`admin`, `crear_sedes`, `editar_sedes`), usa rutas `sedes.*` inexistentes y algunas respuestas dan 403 donde espera 200. **Se resuelve junto con I-03.** **Hecho 2026-11-04** (PR #52): el test se reescribió; siembra `RolesYPermisosSeeder` y usa `/admin/sedes/create` y `empresa.show`. Quedó con 2 casos (antes 9); ambos pasan (verificado 2026-11-05). |
| `[ ]` | M-03 | Validar los endpoints sin validación | `OpcionController`, `TributoController` | 1–2h | Alta | BE | |
| `[ ]` | M-04 | Pasar el país "170" (más de 20 apariciones) a una constante o a configuración | Controllers, `GuardarPersona`, `GuardarContacto`, `Persona` | 2–3h | Alta | BE | |
| `[ ]` | M-05 | Pasar a constantes los valores fijos: tipos de tarifa y vencimiento de factura a 30 días | `EscogerPrecioExamen`, `FacturaController:92` | 1–2h | Alta | BE | |
| `[ ]` | M-06 | Envolver en transacciones las operaciones masivas | `ListaPersonasController`, `ReprocesarController` | 2–4h | Media | BE | |
| `[ ]` | M-07 | Estado del frontend: cachear `/api/static-data-for-frontend` y tener una sola fuente de verdad para la sede activa (hoy sesión PHP y React) | `resources/js/` | 4–6h | Media | FE | Lo puede tomar el equipo frontend |
| `[ ]` | M-08 | Unificar la validación en Form Requests en lugar de validar inline | Controllers | 4–8h | Media | BE | |
| `[ ]` | M-09 | API Resources para no devolver modelos Eloquent completos (también reduce la exposición de PII) | `app/Http/Controllers/Api/` | 8–12h | Baja | BE | |
| `[ ]` | M-10 | Migrar la autenticación web de Breeze a Fortify | `routes/auth.php`, `app/Http/Controllers/Auth/` | 8–12h | Baja | BE + FE | Opcional |
| `[ ]` | M-11 | Cifrar en reposo la PII de pacientes | `personas`, contactos polimórficos | Por estimar | Baja | BE | Evaluar junto con I-04 |
| `[x]` | M-12 | Revisar las tablas de referencia RIPS que entraron con el merge `conciliador` (~430 000 líneas de CSV en `resources/utils/tablas/`): renombrar el archivo `.csv` sin nombre (registro REPS con nombres y documentos de prestadores) y versionar solo las tablas que usan los seeders | `resources/utils/tablas/`, `CUPSeeder`, `RelacionesCupSeeder`, `FinalidadSeeder` | 1–2h | Alta | Repo + BE | Detectado el 2026-11-05. El REPS es un registro público, pero contiene datos personales; confirmar si hace falta en el repo. **2026-11-05 (`823ef980`):** se borró el `.csv` del REPS del árbol, aunque sigue en el historial (5 commits; se limpia junto con C-03). Siguen versionadas 32 tablas que ningún seeder ni el código usan, entre ellas `REF_CUMS` (3 MB), `REF_IUM` y `REF_DCI`. Falta decidir si se quitan o si se conservan para el RIPS futuro; si se conservan, anotar el motivo aquí y cerrar el ítem. **Hecho 2026-11-05:** se borraron 12 tablas que no se usan (`REF_CUMS`, `REF_IUM`, `REF_DCI`, `REF_REPS`, `LogsBaseEntity`, entre otras). Quedan 28: las que usan los seeders y las que se conservan para el RIPS futuro. El REPS sale del historial junto con C-03. |

**Subtotal (sin M-11):** 32.5–55.5h

---

## Resumen

| Prioridad | Ítems | Pendientes / en curso | Hechos | Deprecados / omitidos | Horas estimadas |
|---|---|---|---|---|---|
| 🔴 Crítico | 10 | 3 (C-04, C-08, C-10) | 4 (C-01, C-03, C-05, C-09) | 3 (C-02, C-06, C-07) | 42–65h |
| 🟡 Importante | 11 | 7 | 4 (I-01, I-02, I-03, I-06) | 0 | 47–69h + I-07, I-09, I-11 |
| 🟠 Menor | 12 | 9 | 3 (M-01, M-02, M-12) | 0 | 32.5–55.5h + M-11 |

> Las horas son las de la estimación original; no se han descontado los ítems hechos ni los omitidos.

**Orden sugerido para empezar** (máximo impacto con mínimo esfuerzo): C-01 → C-02 → I-02 → M-01. Son unas 4.5–7.5h en total y desbloquean un CI confiable y una base segura para el resto.

**Avance 2026-11-03:** I-02 y M-01 hechos; C-01 y C-02 omitidos. Suite: 88 pasan, 14 `skipped` (I-11) y 7 fallan (M-02, pendiente de I-03). Después de C-05 e I-11: 107 pasan, 8 `skipped` (endpoint de precios) y 7 fallan (M-02).

**Avance 2026-11-05** (desde el merge del PR #51): PR #52 (matriz de roles) cierra I-03, I-06 y M-02 y amplía C-05; `e9d2e1fd` cierra C-01; el merge `conciliador` y los commits posteriores reparan seeders de RIPS (CUPS, finalidades, vías de ingreso, diagnósticos predefinidos `Z017`/`Z320`) y amplían `SeedersTest`. C-03 vuelve a «en curso» porque el historial sigue sin reescribir. Suite en Docker (PHP 8.5): **118 pasan, 8 `skipped` (endpoint de precios, I-11) y 0 fallan** (330 aserciones). M-02 confirmado. Después de `823ef980` la suite sigue igual (118 pasan, 8 `skipped`). C-03 y M-12 quedan parciales y se agrega C-10 (credenciales en el código). Ese mismo día se cierran C-03 (historial reescrito) y M-12 (tablas depuradas); C-10 queda en curso hasta que se roten las contraseñas.

Al deprecar u omitir un ítem que afecta horas o alcance de la propuesta, actualizar también §13 y §14 de [ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md](./ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md).

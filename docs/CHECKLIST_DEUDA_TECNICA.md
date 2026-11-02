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
| `[O]` | C-01 | Retirar la ruta `GET /migracion`, que ejecuta `migrate:fresh --seed` y la puede llamar el rol `prestador` | `routes/web.php:115` | 0.5–1h | Alta | BE | |
| `[O]` | C-02 | Dejar `APP_DEBUG=false` como valor de producción y hacer que `instancia:verificar` falle si está en `true` con `APP_ENV=production` | `.env.example`, `app/Console/Commands/VerificarInstancia.php` | 1–2h | Alta | BE | |
| `[~]` | C-03 | Sacar del repositorio los datos reales de pacientes y reescribir el historial de git | `resources/utils/rips_julio.json`, `bitacora.json`, `json_de_pueba.json`, `tablas/LogsBaseEntity.csv`; ruta `doc_sandra/` en `AdministracionController.php:66` | 2–4h + coordinación | Media | Repo | Detectado el 2026-11-01; no aparece en el doc de viabilidad. Requiere force push coordinado con el otro desarrollador. |
| `[~]` | C-04 | Spike S0: confirmar si el hosting ejecuta Composer (si no, volver a versionar `vendor/`) y qué versión de PHP ofrece (si no es 8.5, bajar a `^8.3`) | `composer.json`, `.cpanel.yml`, `DESPLIEGUE_INSTANCIAS.md` | 2–4h | Media | Infra | |
| `[ ]` | C-05 | Control de sede: `elegirSede()` debe verificar `empleado_sede`, y `store` de resultados (web y API) debe aplicar el mismo control que `update` | `SedeController::elegirSede()`, `ResultadosController::store()`, `Api/ResultadosController` | 6–10h | Media | BE | Pruebas de aceptación SEDE-01 a SEDE-08 en `MANUAL.md` §7.5 |
| `[O]` | C-06 | Terminar la creación de facturas: quitar el `dd()`, guardar y probar | `FacturaController.php:95` | 6–10h | Media | BE | Decidir si la facturación entra en la propuesta comercial; si no, puede quedar como `[O]` para el MVP |
| `[O]` | C-07 | Generar el PDF del reporte en servidor con DomPDF, reutilizando la vista imprimible | `resources/views/resultados/show.blade.php` | 8–12h | Media | BE + FE | DomPDF ya está instalado. Requisito previo para enviar resultados por correo (C-08)/ omitimos por ahora, se desarrollara en el futuro |
| `[ ]` | C-08 | Notificaciones por email (WhatsApp como fase posterior) | `config/mail.php`, `MAIL_MAILER=log` | 15–20h | Baja | BE + Infra | El `TODO` actual está en el pre-registro público de citas (ver C-09). Si ese flujo se retira, redefinir qué se notifica (p. ej. resultados listos) |
| `[D]` | C-09 | Rate limiting en los endpoints públicos de citas | `routes/api.php:24-26` (`citas/pre-registrar`, `citas/pre-registrar-lista`, `citas/consultar/{codigo}`) | 4–6h | — | BE | **A deprecar:** los endpoints públicos se van a quitar del proyecto, así que no hace falta limitarlos. Al retirarlos, revisar también las rutas web públicas de `routes/web.php:35-38` (`/citas/registrar`, `/citas/confirmacion/{codigo}`, `/citas/confirmar/{codigo}`), los componentes `FormPreRegistroCita` y `RecepcionCitas`, `PreRegistroCitaController` (web y API), `PreRegistroCitaPolicy` y la tabla `pre_registros_citas` (no borrar migraciones; crear una nueva si se elimina la tabla). |

**Subtotal (sin deprecados):** 41–63h

---

## 🟡 Importante

| Estado | ID | Issue | Ubicación | Esfuerzo | Viabilidad | Área | Comentarios |
|---|---|---|---|---|---|---|---|
| `[ ]` | I-01 | Configurar el CI para que corra en `main` (hoy solo en `master` y `*.x`) | `.github/workflows/tests.yml` | 0.5–1h | Alta | Infra | |
| `[ ]` | I-02 | Corregir las fallas de tests reportadas el 2026-10-29: `HasFactory` en `Convenio`, `tipo_documento_id` inexistente en la factory de `Empleado`, `->sedes` sobre `null` en `header.blade.php` | `app/Models/Convenio.php`, `database/factories/EmpleadoFactory.php`, `resources/views/layouts/header.blade.php` | 2–4h | Alta | BE + FE | Verificar primero con `docker compose exec app php artisan test` |
| `[ ]` | I-03 | Matriz de roles y permisos: dividir `can:eliminar_persona` del grupo admin, sembrar los permisos que faltan, crear `OrdenPolicy` y `ProcedimientoPolicy` e invocar `SedePolicy` | `routes/web.php`, `RolesYPermisosSeeder`, `app/Policies/` | 6–8h | Media | BE | Requiere que el responsable funcional apruebe la matriz |
| `[ ]` | I-04 | Auditoría HABEAS DATA: registrar accesos y cambios a datos de pacientes y órdenes con formato común (usuario, acción, ID) | `app/` (19 llamadas a `Log::`) | 6–10h | Media | BE | La edición de resultados ya se audita en `resultado_cambios` |
| `[ ]` | I-05 | Eliminar N+1 en el dashboard y en la lista de personas | `EmpleadoController::dashboard()`, `Api/ListaPersonasController` | 6–10h | Media | BE | |
| `[ ]` | I-06 | Implementar la edición web de persona (`edit`, `update`, `destroy` están vacíos) | `PersonaController`, `resources/views/personas/` | 8–10h | Media | BE + FE | |
| `[ ]` | I-07 | Definir el alcance del cargador de listas: ¿selección asistida (actual) o importación masiva real? | `ParseadorListaPersonas`, `Api/ListaPersonasController` | Decisión + 6–10h si es importación | Media | BE + FE | La propuesta comercial ofrece "carga masiva" |
| `[ ]` | I-08 | Test de extremo a extremo del flujo orden → procedimiento → resultado → reporte, y tests de autorización | `tests/Feature/` | 10–14h | Media | BE | Incluir los casos de `MANUAL.md` §7.5 |
| `[ ]` | I-09 | Mover a services la lógica que sigue en controllers de los módulos antiguos | 75 controllers / 11 services | Continuo | Baja | BE | Hacerlo a medida que se toque cada módulo, no como tarea aparte |
| `[ ]` | I-10 | Unificar la duplicación web/API (validación de persona, creación de órdenes, captura de resultados) | `PersonaController` / `Api/PersonaController`, `OrdenController` / `Api/OrdenController`, `ResultadosController` / `Api/ResultadosController` | 8–12h | Baja | BE | Depende de I-09 |

**Subtotal (sin I-09 continuo ni I-07):** 47–69h

---

## 🟠 Menor

| Estado | ID | Issue | Ubicación | Esfuerzo | Viabilidad | Área | Comentarios |
|---|---|---|---|---|---|---|---|
| `[ ]` | M-01 | `Empleado->nombre()` no tiene `return` | `app/Models/Empleado.php:29-31` | 0.5h | Alta | BE | |
| `[ ]` | M-02 | Alinear `EmpresaPolicyTest` con las rutas reales (`admin.sedes.*`) y agregar las aserciones que faltan | `tests/Feature/Policies/EmpresaPolicyTest.php` | 1–2h | Alta | BE | |
| `[ ]` | M-03 | Validar los endpoints sin validación | `OpcionController`, `TributoController` | 1–2h | Alta | BE | |
| `[ ]` | M-04 | Pasar el país "170" (más de 20 apariciones) a una constante o a configuración | Controllers, `GuardarPersona`, `GuardarContacto`, `Persona` | 2–3h | Alta | BE | |
| `[ ]` | M-05 | Pasar a constantes los valores fijos: tipos de tarifa y vencimiento de factura a 30 días | `EscogerPrecioExamen`, `FacturaController:92` | 1–2h | Alta | BE | |
| `[ ]` | M-06 | Envolver en transacciones las operaciones masivas | `ListaPersonasController`, `ReprocesarController` | 2–4h | Media | BE | |
| `[ ]` | M-07 | Estado del frontend: cachear `/api/static-data-for-frontend` y tener una sola fuente de verdad para la sede activa (hoy sesión PHP y React) | `resources/js/` | 4–6h | Media | FE | Lo puede tomar el equipo frontend |
| `[ ]` | M-08 | Unificar la validación en Form Requests en lugar de validar inline | Controllers | 4–8h | Media | BE | |
| `[ ]` | M-09 | API Resources para no devolver modelos Eloquent completos (también reduce la exposición de PII) | `app/Http/Controllers/Api/` | 8–12h | Baja | BE | |
| `[ ]` | M-10 | Migrar la autenticación web de Breeze a Fortify | `routes/auth.php`, `app/Http/Controllers/Auth/` | 8–12h | Baja | BE + FE | Opcional |
| `[ ]` | M-11 | Cifrar en reposo la PII de pacientes | `personas`, contactos polimórficos | Por estimar | Baja | BE | Evaluar junto con I-04 |

**Subtotal (sin M-11):** 31.5–53.5h

---

## Resumen

| Prioridad | Ítems | Pendientes | Deprecados / omitidos | Horas estimadas |
|---|---|---|---|---|
| 🔴 Crítico | 9 | 8 | 1 (C-09) | 41–63h |
| 🟡 Importante | 10 | 10 | 0 | 47–69h + I-07, I-09 |
| 🟠 Menor | 11 | 11 | 0 | 31.5–53.5h + M-11 |

**Orden sugerido para empezar** (máximo impacto con mínimo esfuerzo): C-01 → C-02 → I-01 → I-02 → M-01. Son unas 5–8h en total y desbloquean un CI confiable y una base segura para el resto.

Al deprecar u omitir un ítem que afecta horas o alcance de la propuesta, actualizar también §13 y §14 de [ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md](./ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md).

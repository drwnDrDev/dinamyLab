# Matriz de roles y permisos — propuesta para aprobación

**Estado:** 🟡 Borrador, pendiente de aprobación del responsable funcional
**Creado:** 2026-11-03 · **Issue:** I-03 en [CHECKLIST_DEUDA_TECNICA.md](./CHECKLIST_DEUDA_TECNICA.md) · **Contexto técnico:** [MANUAL.md §7](./MANUAL.md#7-roles-permisos-y-sedes)

## Para quien aprueba

Hoy la aplicación tiene 5 roles, pero los permisos casi no se aplican:

- **Cualquier usuario que inicie sesión puede ver y modificar pacientes, órdenes y resultados**, sin importar su rol. Solo se controla que sea de la sede (C-05).
- Toda la administración (empleados, sedes, convenios, editar o eliminar pacientes) depende de **un solo permiso**, `eliminar_persona`, que también tiene el rol `prestador`. En la práctica, un prestador puede administrar el laboratorio.
- Nadie puede eliminar órdenes, ni siquiera el administrador, porque ese permiso nunca se creó.

Esta propuesta divide los permisos por acción y define qué puede hacer cada rol. **Solo hay que revisar la §2 (la matriz) y responder las preguntas de la §3.** La §4 es para el desarrollador backend.

---

## 1. Roles

| Rol (en el sistema) | A quién corresponde (propuesta) | Resumen |
|---|---|---|
| `admin` | Director o administrador del laboratorio | Todo |
| `prestador` | Profesional de laboratorio (bacteriólogo) | Trabajo clínico completo: órdenes, muestras, resultados |
| `agente` | Recepción / auxiliar | Registro de pacientes y órdenes; consulta y entrega de resultados |
| `contable` | Facturación / contabilidad | Facturas, convenios, caja y RIPS; sin resultados clínicos |
| `paciente` | Paciente (portal futuro) | Sin acceso por ahora |

> ❓ Confirmar que estos roles corresponden a los cargos reales del laboratorio (ver P1).

---

## 2. Matriz propuesta

✅ = permitido · — = no permitido · ❓ = a decidir (ver §3) · **negrita** = cambia respecto de hoy

### Clínico

| Permiso | Qué permite | admin | prestador | agente | contable | paciente |
|---|---|:-:|:-:|:-:|:-:|:-:|
| `ver_persona` | Buscar y ver pacientes | ✅ | ✅ | ✅ | **✅** | — |
| `crear_persona` | Registrar pacientes | ✅ | ✅ | ✅ | — | — |
| `editar_persona` | Corregir datos de un paciente | ✅ | ✅ | ✅ | — | — |
| `eliminar_persona` | Eliminar un paciente | ✅ | **—** | — | — | — |
| `ver_orden` | Ver órdenes | ✅ | ✅ | ✅ | **✅** | — |
| `crear_orden` *(nuevo)* | Crear órdenes | ✅ | ✅ | ✅ | — | — |
| `editar_orden` *(nuevo)* | Agregar o cambiar exámenes de una orden | ✅ | ✅ | ❓ P2 | — | — |
| `eliminar_orden` *(nuevo)* | Eliminar una orden | ✅ | — | — | — | — |
| `gestionar_procedimiento` *(nuevo)* | Cambiar el estado de la muestra (toma, proceso) | ✅ | ✅ | ❓ P3 | — | — |
| `ver_resultado` | Ver e imprimir resultados | ✅ | ✅ | ✅ | **—** P4 | — |
| `registrar_resultado` *(nuevo)* | Capturar resultados (uno a uno o por lote) | ✅ | ✅ | — | — | — |
| `editar_resultado` *(nuevo)* | Corregir un resultado ya guardado (queda auditado) | ✅ | ✅ | — | — | — |

### Operación y reportes

| Permiso | Qué permite | admin | prestador | agente | contable | paciente |
|---|---|:-:|:-:|:-:|:-:|:-:|
| `ver_examen` | Ver el catálogo de exámenes | ✅ | ✅ | ✅ | ✅ | — |
| `crear_examen` / `editar_examen` | Crear o modificar exámenes y sus parámetros | ✅ | **—** P5 | — | — | — |
| `ver_reportes` *(nuevo)* | Reportes de producción | ✅ | ✅ | — | ✅ | — |
| `exportar_rips` *(nuevo)* | Descargar los RIPS | ✅ | — | — | ✅ | — |
| `ver_caja` *(nuevo)* | Caja e ingresos | ✅ | — | ❓ P6 | ✅ | — |

### Facturación

| Permiso | Qué permite | admin | prestador | agente | contable | paciente |
|---|---|:-:|:-:|:-:|:-:|:-:|
| `ver_facturas` | Ver facturas | ✅ | **—** | — | ✅ | — |
| `crear_factura` *(nuevo)* | Emitir facturas | ✅ | — | — | ✅ | — |
| `gestionar_resoluciones` *(nuevo)* | Resoluciones de facturación DIAN | ✅ | — | — | ✅ | — |
| `ver_convenios` *(nuevo)* | Ver convenios | ✅ | — | ✅ | ✅ | — |
| `gestionar_convenios` *(nuevo)* | Crear, editar o eliminar convenios | ✅ | — | — | ❓ P7 | — |
| `gestionar_tarifas` *(nuevo)* | Precios por sede y por convenio | ✅ | — | — | ❓ P7 | — |

### Administración

| Permiso | Qué permite | admin | prestador | agente | contable | paciente |
|---|---|:-:|:-:|:-:|:-:|:-:|
| `ver_empresa` | Ver los datos de la empresa | ✅ | ✅ | — | — | — |
| `editar_empresa` | Modificar los datos de la empresa | ✅ | — | — | — | — |
| `gestionar_empleados` *(nuevo)* | Crear empleados y asignarles sedes | ✅ | **—** | — | — | — |
| `crear_sedes` / `editar_sedes` / `eliminar_sedes` | Administrar sedes | ✅ | **—** | — | — | — |
| `ver_catalogos` *(nuevo)* | Consultar CUPS y CIE-10 | ✅ | ✅ | ✅ | ✅ | — |
| `ver_dashboard_global` *(nuevo)* | Ver el dashboard de todas las sedes | ✅ | — | — | — | — |

**Todas las acciones clínicas se limitan además a las sedes del empleado** (control C-05, ya implementado): tener el permiso no da acceso a otra sede.

---

## 3. Preguntas para decidir

| # | Pregunta | Propuesta por defecto |
|---|---|---|
| P1 | ¿Los roles de la §1 corresponden a los cargos reales? ¿Falta alguno (p. ej. coordinador de sede)? | Mantener los 5 |
| P2 | ¿Recepción puede agregar o quitar exámenes de una orden ya creada? | Sí, mientras la muestra no se haya tomado |
| P3 | ¿Recepción registra la toma de muestra? | Sí |
| P4 | ¿Facturación necesita ver resultados clínicos? Por Habeas Data, conviene que solo vea lo necesario para facturar | No |
| P5 | ¿El bacteriólogo puede crear o modificar exámenes y valores de referencia, o solo el administrador? | Solo el administrador |
| P6 | ¿Recepción maneja caja? | Sí, si cobra en ventanilla |
| P7 | ¿Facturación administra convenios y tarifas, o solo el administrador? | Solo el administrador |
| P8 | Los permisos `ver_cuentas`, `crear_cuenta` y `editar_cuenta` existen pero ninguna pantalla los usa. ¿Se eliminan? | Eliminar |
| P9 | ¿El rol `paciente` se mantiene para un portal futuro o se elimina? | Mantenerlo sin permisos |

**Aprobado por:** ____________________ **Fecha:** ____________ **Respuestas / cambios:** ____________________

---

## 4. Para backend: implementación

### 4.1 Hallazgos que corrige esta matriz

| Hallazgo | Ubicación | Efecto hoy |
|---|---|---|
| 🔴 `GET /migracion` está **fuera de todo middleware** (commit `1a3e6dc1`) | `routes/web.php` | Cualquier visitante, sin iniciar sesión, ejecuta `migrate:fresh --seed` y borra la base de datos. Ver C-01 |
| 🔴 `reprocesar.*` está fuera de `auth` | `routes/web.php` | Carga y exportación de datos sin iniciar sesión |
| El grupo administrativo usa `can:eliminar_persona` | `routes/web.php` (grupo `admin/*`) | `prestador` administra empleados, sedes y convenios |
| Las rutas clínicas solo exigen `auth` | `routes/web.php` (grupo `auth`) y casi toda la API | Cualquier rol, incluido `paciente`, opera pacientes, órdenes y resultados |
| `hasRole('administrador')`: ese rol no existe (es `admin`) | `EmpleadoController::dashboard()`, `Api\DashboardController::index()` | El admin nunca ve el dashboard global |
| `eliminar_orden_medica` no se siembra | `OrdenController::destroy()` | Nadie puede eliminar órdenes |
| `crear_sedes`, `editar_sedes`, `eliminar_sedes` y `editar_empresa` no se siembran | `SedePolicy`, `EmpresaPolicy` | Solo funcionan por `hasRole('admin')` |
| `SedePolicy` y `EmpresaPolicy` están registradas, pero `SedeController` no las invoca | `SedeController` | Las policies no protegen nada |
| `EmpresaPolicyTest` falla (7 de 9) | `tests/Feature/Policies/` | Ver M-02 |

### 4.2 Mapa de rutas a permisos

| Rutas | Permiso |
|---|---|
| `personas` (index, show), `search`, `GET api/personas*` | `ver_persona` |
| `personas.create`, `personas.store`, `POST api/personas`, `api/personas/parsear-lista` | `crear_persona` |
| `personas.edit`, `personas.update`, `PUT api/personas/{id}` | `editar_persona` |
| `personas.destroy` | `eliminar_persona` |
| `ordenes`, `ordenes.show` | `ver_orden` |
| `ordenes.create`, `ordenes.store`, `POST api/ordenes` | `crear_orden` |
| `ordenes.edit`, `ordenes.update`, `PATCH api/ordenes-medicas/{orden}` | `editar_orden` |
| `ordenes.destroy` | `eliminar_orden` (reemplaza `eliminar_orden_medica`) |
| `procedimientos*`, `procedimientos.observaciones` | `ver_orden` / `gestionar_procedimiento` |
| `resultados`, `resultados.show`, `resultados.historia*` | `ver_resultado` |
| `resultados.create`, `resultados.store`, `examenes.lote*`, `POST api/resultados/{id}` | `registrar_resultado` |
| `resultados.edit`, `resultados.update` | `editar_resultado` |
| `examenes`, `examenes.show`, `parametros.index` | `ver_examen` |
| `PATCH api/examenes/{id}/update` y edición de parámetros | `editar_examen` |
| `reportes` | `ver_reportes` |
| `descargar.rips`, `reprocesar.*` | `exportar_rips` |
| `caja`, `rips` (`/caja/ingresos`) | `ver_caja` |
| `facturas`, `facturas.show` | `ver_facturas` |
| `facturas.create`, `facturas.store` | `crear_factura` |
| `resoluciones.*` | `gestionar_resoluciones` |
| `convenios.index`, `convenios.show` | `ver_convenios` |
| `convenios.create/store/edit/update/destroy` | `gestionar_convenios` |
| `api/tarifas*` | `gestionar_tarifas` |
| `empresa.show` | `ver_empresa` |
| `admin/empleados*`, `empleados.index` | `gestionar_empleados` |
| `admin/sedes*` | `crear_sedes` / `editar_sedes` / `eliminar_sedes` (vía `SedePolicy`) |
| `cups.*`, `cie10.*` | `ver_catalogos` |
| `admin.dashboard` y la rama global de `EmpleadoController::dashboard()` | `ver_dashboard_global` |

> Las rutas `reprocesar.*` hoy están **fuera de `auth`** (`routes/web.php`, antes del grupo `auth`). Moverlas dentro del grupo cuando se aplique esta matriz.

### 4.3 Pasos sugeridos

1. Sembrar todos los permisos de la §2 en `RolesYPermisosSeeder` y asignarlos por rol. Es idempotente; la nueva versión debe usar `syncPermissions` para retirar los que sobran.
2. Reemplazar `can:eliminar_persona` por el permiso de cada grupo de rutas, y `hasRole('administrador')` por `can('ver_dashboard_global')`.
3. Invocar `SedePolicy` en `SedeController` (`authorize`) y crear `OrdenPolicy` y `ProcedimientoPolicy`.
4. Tests: alinear `EmpresaPolicyTest` (M-02) y agregar los casos PERM-01 a PERM-05 de `MANUAL.md` §7.5. Para cada rol, probar al menos una ruta permitida y una denegada por sección de la matriz.
5. Comunicar a los clientes el cambio de alcance de `prestador` antes de desplegar.

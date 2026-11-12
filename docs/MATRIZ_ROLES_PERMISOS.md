# Matriz de roles y permisos

**Estado:** Implementada en rutas web/API y `RolesYPermisosSeeder`.
**Última actualización:** 2026-11-11

Al desplegar este cambio en una instancia existente, aplicar la nueva matriz ejecutando:
`php artisan db:seed --class=RolesYPermisosSeeder --force`.

## Criterio de autorización

El acceso se evalúa en dos capas:

1. **Permiso por acción** mediante middleware `can:<permiso>` en la ruta. Los permisos se asignan con Spatie y el seeder es idempotente.
2. **Sede activa** para órdenes, procedimientos y resultados. Agentes y prestadores deben pertenecer a la sede elegida y los recursos operativos deben ser de esa sede. El rol `admin` puede operar globalmente, incluso consultar historias de varias sedes sin elegir una sede. El registro de pacientes es compartido entre sedes.

Las rutas de configuración/mantenimiento de catálogos, sedes, empleados, convenios, tarifas, facturación y reprocesamiento quedan reservadas a `admin`. Los catálogos de lectura necesarios para la labor clínica no conceden por sí mismos permiso para modificarlos.

## Perfiles

| Acción | `admin` | `prestador` | `agente` | `contable` | `paciente` |
|---|:-:|:-:|:-:|:-:|:-:|
| Listar/ver pacientes y buscar | Sí | Sí | Sí | No | No |
| Crear/editar/borrar lógicamente pacientes | Sí | Sí | No | No | No |
| Ver/listar órdenes | Sí | Sí | Sí | No | No |
| Crear/editar/borrar órdenes | Sí | Sí | Crear/editar | No | No |
| Ver/listar procedimientos | Sí | Sí | Sí | No | No |
| Gestionar estado y borrar lógicamente procedimientos | Sí | Sí | No | No | No |
| Ver/listar/imprimir/buscar resultados | Sí | Sí | Sí | No | No |
| Registrar/corregir resultados | No | Sí | No | No | No |
| Consultar exámenes | Sí | Sí | Sí | No | No |
| Modificar descripción de exámenes | Sí | Sí | No | No | No |
| Ver facturas de la sede activa | Sí | No | No | Sí | No |
| Reportes clínicos y descarga RIPS de la sede activa | Sí | Sí | Sí | No | No |
| Configuración del sistema | Sí | No | No | No | No |

`admin` puede consultar globalmente, pero no recibe permisos ni acceso a las rutas de captura o corrección de resultados. `prestador` es el único que puede crear o editar resultados, únicamente en una sede a la que pertenece. `agente` puede crear/editar personas y órdenes, consultar procedimientos, exámenes, resultados y reportes/RIPS de una sede en la que trabaja; no puede modificar exámenes ni resultados. `contable` conserva únicamente lectura de facturas. `paciente` conserva el rol sin permisos.

La descripción del examen vive en el catálogo global compartido. Se confirmó que el cambio hecho por un prestador es global para todas las sedes; la petición requiere que el prestador pertenezca a la sede activa antes de permitirla.

## Permisos que usan las rutas

| Permiso | Acciones |
|---|---|
| `ver_persona`, `crear_persona`, `editar_persona`, `eliminar_persona` | Lectura, alta, cambio y borrado lógico de pacientes; API equivalente |
| `ver_orden`, `crear_orden`, `editar_orden`, `eliminar_orden` | Consulta, alta, cambio y borrado lógico de órdenes; `editar_orden` incluye añadir exámenes |
| `ver_procedimiento`, `gestionar_procedimiento`, `eliminar_procedimiento` | Consulta/listado, cambio de estado y borrado lógico de procedimientos |
| `ver_resultado`, `registrar_resultado`, `editar_resultado` | Consulta/impresión/historia para admin, prestador y agente; captura y corrección exclusivamente para prestador |
| `ver_examen`, `editar_descripcion_examen` | Consulta de exámenes y edición limitada a `descripcion` por API |
| `ver_facturas` | Listado y lectura de factura de la sede (admin puede acceder globalmente) |
| `ver_reportes` | Reporte de procedimientos y descarga RIPS; `admin` también puede acceder globalmente por rol |

La orden se marca como completada cuando tiene al menos un procedimiento y todos están en estado `terminado`, `entregado` o `anulado`. Si se añade o reabre un procedimiento, vuelve a pendiente.

Además hay permisos heredados de áreas administrativas que se asignan solo al administrador (por ejemplo, permisos de empresa, sedes y cuentas). Los endpoints de mutación de catálogos usan rol `admin` de forma explícita.

## Borrado lógico

Pacientes, órdenes y procedimientos se eliminan con `deleted_at`; no se borran físicamente. El borrado de una orden también marca sus procedimientos como eliminados. Las consultas Eloquent normales excluyen registros eliminados.

## Pendientes funcionales detectados

- Los métodos de creación/edición/borrado de procedimientos independientes no existían como CRUD completo. La creación de procedimientos ocurre al crear una orden; se protege el cambio de estado y se implementa el borrado lógico. No se inventó un flujo de edición de sus demás campos.
- La edición de una orden permite modificar observaciones y abono; la ruta existente para añadir exámenes también queda protegida con `editar_orden`.
- `EmpleadoController` y algunos controladores de facturación/convenios contienen otras acciones vacías o bloqueadas por código previo. No se consideran permisos concedidos por el hecho de definir el perfil.
- `GET /static-data-for-frontend` se conserva público porque entrega catálogos estáticos; no permite mutaciones ni datos de pacientes.

## Pruebas

`tests/Feature/Authorization/PermissionProfilesTest.php` valida capacidades por perfil, edición restringida a descripción, aislamiento operativo por sede y borrado lógico.

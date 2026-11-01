# Manual de DinamyLAB

**Estado documentado:** 2026-11-01 (rama `maloca`, Laravel 13.38, PHP 8.5)
**Propósito:** describir la aplicación que existe hoy: sus flujos principales, la operación, la autorización y los límites conocidos. No certifica que esté lista para producción ni reemplaza la validación clínica, legal o de despliegue.

Documentos relacionados:
- Estado técnico-comercial, costos y riesgos: [ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md](./ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md)
- Entorno local: [ENTORNO_DOCKER_WSL.md](./ENTORNO_DOCKER_WSL.md)
- Despliegue por cliente: [DESPLIEGUE_INSTANCIAS.md](./DESPLIEGUE_INSTANCIAS.md)

---

## 1. Qué es la aplicación

DinamyLAB es una aplicación web para la operación de un laboratorio clínico. Permite administrar personas, registrar órdenes, convertirlas en procedimientos, capturar resultados y consultar su historial. También incluye catálogos (CUPS, CIE-10, exámenes), sedes, pre-registro de citas, convenios, facturación y RIPS.

Está construida con Laravel y vistas Blade, con componentes React montados como islas en páginas concretas; no es una SPA. La interfaz está en español y modela el dominio colombiano, pero tener campos, reportes o formatos no implica certificación regulatoria.

## 2. Arquitectura y aislamiento

- [compose.yaml](../compose.yaml) define los servicios `web` (nginx), `app` (PHP-FPM 8.5), `queue` (worker), `db` (MySQL 8.4) y `vite` (Node 22 + pnpm).
- El modelo de operación es **una instalación y una base de datos por cliente**, cada una con su `.env`, su `storage/` y sus sesiones. No es multi-tenancy dentro de una base compartida.
- El aislamiento entre instancias depende de configurar bien `APP_INSTANCE`, `APP_HOSTS`, `SESSION_COOKIE` y las credenciales de BD. El middleware `EnsureInstance` rechaza con 503 las peticiones a dominios ajenos. No compartir `SESSION_DOMAIN` entre clientes.
- Dentro de una instancia puede haber varias empresas y sedes, pero el control por sede **no es uniforme** (ver §7). No usar una sola instancia para organizaciones distintas.

Diseño de referencia: [superpowers/specs/2026-10-30-multitenant-design.md](./superpowers/specs/2026-10-30-multitenant-design.md).

## 3. Flujo operativo principal

### 3.1 Personas

Persona registra a pacientes, pagadores y acompañantes. Los formularios capturan identificación y contacto, que luego se relacionan con las órdenes.

- El listado web está paginado (25) y tiene búsqueda y filtros. El listado de la API no tiene el mismo comportamiento.
- **La edición web está incompleta:** `edit`, `update` y `destroy` de `PersonaController` son métodos vacíos. Existe una actualización por API, pero no completa el flujo web.

### 3.2 Cargador de listas

El cargador interpreta una lista de personas, muestra las coincidencias y deja escoger una para llenar el formulario. Facilita la captura; **no** es una importación masiva que cree todas las personas.

### 3.3 Órdenes y procedimientos

Una orden reúne al paciente, un acompañante opcional y los exámenes solicitados. El flujo valida los campos, comprueba los exámenes, calcula valores según tarifas (convenio+sede → sede → precio base) y genera un procedimiento por examen.

El número de orden es texto. `SugerirNumeroOrden` propone el siguiente por sede y respeta prefijos y ceros a la izquierda.

### 3.4 Resultados e historial

- **Captura por procedimiento** y **por lotes**: `GuardarResultadosLote` y la isla React `ResultadosLote` capturan un examen para varios procedimientos a la vez.
- **Edición auditada**: `ActualizarResultados` valida por tipo de dato (número, fecha, opción), bloquea la fila y guarda en `resultado_cambios` el valor anterior, el valor nuevo, el usuario y una observación obligatoria.
- **Reporte imprimible**: `resultados/show` tiene formato de impresión (encabezado, paciente, parámetros, pie). Se imprime o se guarda como PDF desde el navegador. DomPDF está instalado, pero **no existe un flujo de PDF en servidor**.
- La captura normal y la API todavía usan lógica distinta de la de los servicios nuevos.

Como son datos clínicos, confirmar la persona y la orden antes de guardar o entregar resultados. Los valores, unidades y rangos de referencia deben validarlos los responsables del laboratorio.

## 4. Otros módulos

| Módulo | Estado |
|---|---|
| **Citas y pre-registro** | Formulario público, código de confirmación, recepción. El correo de confirmación está pendiente (`TODO` en `PreRegistroCitaController`). Las rutas públicas no tienen rate limiting. |
| **Convenios** | Formulario React montado vía Vite; usa `localStorage` para autocompletar. Probar el envío y la persistencia antes de depender de él. |
| **Facturación** | Hay listado, detalle, medios de pago y resoluciones DIAN. **La creación está bloqueada**: `FacturaController.php:95` tiene un `dd()` antes de guardar. No presentarla como funcional. |
| **RIPS** | Exportación JSON/CSV. Conserva valores de prueba en algunos puntos; validarla antes de un uso regulatorio. |
| **Catálogos CUPS / CIE-10** | Listados con búsqueda y paginación. |
| **Sedes y usuarios** | Administración y selección de sede, roles con Spatie (ver §7). |

## 5. Entorno de desarrollo

La guía completa está en [ENTORNO_DOCKER_WSL.md](./ENTORNO_DOCKER_WSL.md). En resumen:

```bash
cp .env.example .env                     # definir DB_PASSWORD y DB_ROOT_PASSWORD
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
docker compose restart queue
```

Abrir `http://localhost:8000`. Vite usa el puerto `5173` y MySQL el `3307`.

- `migrate --seed` carga datos de **demostración**; no usarlo para inicializar producción.
- Los tests usan SQLite en memoria. Lo que dependa de MySQL hay que probarlo también en el contenedor.

## 6. Despliegue

Modelo cPanel: una instalación por cliente con el dominio apuntando a su `public/`, y BD, `.env`, clave y `storage/` propios. Seguir [DESPLIEGUE_INSTANCIAS.md](./DESPLIEGUE_INSTANCIAS.md) y validar el aislamiento antes de cargar datos reales.

Bloqueos antes de dar el despliegue por automatizado:
- `vendor/` no está versionado y [.cpanel.yml](../.cpanel.yml) no instala dependencias. Falta decidir si el hosting ejecuta Composer o si se vuelve a versionar `vendor/`.
- `composer.json` exige PHP `^8.5`. Si el hosting no lo ofrece, bajar a `^8.3`.
- Las migraciones se corren a mano, de forma explícita.
- `.env.example` trae `APP_DEBUG=true`; en producción debe ser `false`.

## 7. Roles, permisos y sedes

### 7.1 Modelo

La aplicación usa `spatie/laravel-permission` sobre `User` (sin `teams`), y además la relación `Empleado`–`Sede` en `empleado_sede`. Son controles distintos:

- Un **permiso** dice qué operación puede ejecutar el usuario.
- La **membresía de sede** dice en qué sede puede operar.
- La **sede activa** guardada en sesión no demuestra por sí sola que el usuario pertenezca a esa sede.

### 7.2 Roles del seeder

[RolesYPermisosSeeder](../database/seeders/RolesYPermisosSeeder.php):

| Rol | Permisos | Observación |
|---|---|---|
| `admin` | Todos los existentes al ejecutar el seeder | Amplio |
| `prestador` | `ver_resultado`, `crear_persona`, `editar_persona`, `ver_persona`, `ver_orden`, `eliminar_persona`, `ver_cuentas`, `crear_cuenta`, `editar_cuenta`, `ver_examen`, `crear_examen`, `editar_examen`, `ver_empresa`, `ver_facturas` | `eliminar_persona` le da acceso a todo el grupo administrativo |
| `agente` | `ver_resultado`, `crear_persona`, `editar_persona`, `ver_persona`, `ver_orden` | Sin facturas ni administración |
| `contable` | `ver_facturas` | — |
| `paciente` | Ninguno | El rol existe sin permisos |

`ClienteSeeder` asigna `admin` al administrador del cliente y `DemoSeeder` asigna roles de demostración.

Hay permisos que se consultan en el código pero que el seeder no crea ni asigna: `crear_sedes`, `editar_sedes`, `eliminar_sedes`, `editar_empresa`, `gestionar_citas`, `eliminar_orden_medica`.

### 7.3 Dónde se aplica autorización hoy

1. La mayoría de las rutas clínicas (personas, órdenes, procedimientos, resultados) solo exigen `auth`.
2. Facturas y convenios exigen `auth`, `verified` y `can:ver_facturas`.
3. Todo el grupo administrativo usa `can:eliminar_persona`: empresas, empleados, sedes, catálogos y `/migracion`.
4. `EmpresaPolicy`, `SedePolicy` y `PreRegistroCitaPolicy` están registradas, pero `SedeController` no las invoca.
5. `SedeController::elegirSede()` guarda en sesión cualquier sede, sin verificar membresía.
6. `ResultadosController` (web) llama a `autorizarSede()` en `show`, `edit` y `update`, pero no en `store`.
7. La escritura de resultados por API no verifica membresía ni un permiso específico.
8. `SedePolicy` compara la empresa del empleado, no `empleado_sede`.

### 7.4 Hallazgos priorizados (para backend)

| Prioridad | Hallazgo | Acción esperada |
|---|---|---|
| **Crítico** | `GET /migracion` ejecuta `migrate:fresh --seed` (`routes/web.php:115`) y el rol `prestador` cumple su middleware | Retirar la ruta o moverla a un mecanismo de mantenimiento seguro |
| Alto | Se puede elegir una sede sin pertenecer a ella | Responder 403 sin cambiar la sesión |
| Alto | Las escrituras de resultados (`store` web y API) no aplican el control de sede | Aplicar el mismo control que `update` |
| Alto | `can:eliminar_persona` protege todo el grupo administrativo | Dividir por acción y aprobar una matriz de roles y permisos |
| Medio | `EmpresaPolicyTest` usa nombres de ruta que no existen (`sedes.*` en lugar de `admin.sedes.*`) y un test no tiene aserciones | Corregir la suite |

### 7.5 Batería de pruebas propuesta

Son criterios de aceptación para backend. Cada caso debe comprobar el código de respuesta **y** que no cambiaron ni la base de datos ni la sesión.

| ID | Caso | Resultado esperado |
|---|---|---|
| AUTH-01 | Visitante en una ruta autenticada | Login en web; 401 en API |
| AUTH-02 | Usuario sin verificar en una ruta `verified` | Flujo de verificación |
| PERM-01 | Usuario sin `ver_facturas` pide facturas | 403 |
| PERM-02 | `contable` accede a facturas | Permitido, sin acceso administrativo |
| PERM-03 | `agente` pide facturas | 403 |
| PERM-04 | Usuario sin autorización en una operación administrativa | 403 |
| PERM-05 | `prestador` accede a `/migracion` | La ruta no existe en producción y nunca migra |
| SEDE-01 | Empleado de la sede A elige la sede B | 403; la sesión no cambia. **Hoy falla** |
| SEDE-02 | Usuario sin empleado elige una sede | 403. **Hoy falla** |
| SEDE-03 | Empleado de la sede A elige la sede A | Éxito; A queda en sesión |
| SEDE-04 | Sede activa A, procedimiento de la sede B | 403; sin cambios |
| SEDE-05 | Usuario no miembro actualiza un resultado | 403; valor e historial iguales |
| SEDE-06 | Sin sede activa al modificar resultados | 403 o redirección controlada |
| SEDE-07 | Usuario no miembro guarda un resultado por POST web | 403. **Hoy falla** |
| SEDE-08 | Usuario no miembro guarda un resultado por API | 403. **Hoy falla** |
| DATA-01 | Usuario de otra empresa pide un ID directo | Sin acceso ni mutación |
| DATA-02 | Petición denegada con payload | `assertDatabaseHas` con el valor anterior y sin auditoría falsa |

Patrón de ejemplo en Pest (ya se usa en `ResultadosEdicionTest` y `ResultadosLoteTest`):

```php
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;

beforeEach(fn () => $this->seed(RolesYPermisosSeeder::class));

it('deniega facturas a un usuario sin el permiso', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('agente');

    $this->actingAs($user)->get(route('facturas'))->assertForbidden();
});
```

Al negar una escritura de resultados:

```php
$response->assertForbidden();
$this->assertDatabaseHas('resultados', ['id' => $resultado->id, 'resultado' => $valorAnterior]);
$this->assertDatabaseCount('resultado_cambios', 0);
```

Orden sugerido:
1. Retirar `/migracion`.
2. Aprobar la matriz de permisos y sembrar todos los permisos.
3. Escribir las pruebas de sede y de escritura de resultados.
4. Hacer que la selección de sede y las escrituras comprueben `empleado_sede`.
5. Corregir `EmpresaPolicyTest`.
6. Ejecutar la regresión en el contenedor.

## 8. Seguridad y datos

- Aislamiento por instalación y BD, más la guarda de hostname. No confundirlo con aislamiento multiempresa dentro de una base compartida.
- No poner datos personales ni clínicos en URLs, logs, ejemplos o capturas.
- Secretos fuera del repositorio; `APP_DEBUG=false` en producción.
- Endpoints públicos de citas sin `throttle`.
- PII en texto plano; las APIs devuelven modelos completos.
- HABEAS DATA: se auditan las ediciones de resultados, pero no los accesos a datos de pacientes.

## 9. Qué está avanzado y qué no

| Área | Estado |
|---|---|
| Personas, órdenes, procedimientos | Flujo principal operativo; faltan pruebas de permisos y errores de extremo a extremo |
| Resultados: captura, lotes, edición auditada | Operativo y con tests; captura web y API sin unificar |
| Reporte de resultados | Imprimible desde el navegador; sin PDF en servidor |
| Búsqueda y paginación | En todos los listados web principales |
| Aislamiento de clientes | Una instancia y una BD por cliente |
| Edición web de personas | Incompleta (métodos vacíos) |
| Importación de listas | Selección asistida, no creación masiva |
| Facturas | Creación bloqueada por `dd()` |
| Email / notificaciones | Pendiente |
| Autorización por sede | Parcial; `/migracion` es un riesgo crítico |
| RIPS | Exporta; falta validación normativa |
| Despliegue cPanel | Runbook listo; falta resolver `vendor/` y la versión de PHP del hosting |

## 10. Ejemplos y prototipos

Los archivos listados en [INDEX.md](./INDEX.md#ejemplos-fuera-de-docs) son material ilustrativo. No son rutas de ejecución, contratos API ni código aprobado.

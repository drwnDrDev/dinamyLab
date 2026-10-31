# Diagnóstico y pruebas de roles, permisos y sedes

**Revisión del código:** 31-oct-2026
**Alcance:** diagnóstico y especificación de pruebas. No se modificaron
controladores, policies, rutas, modelos, seeders ni pruebas ejecutables.

## Resumen ejecutivo

El proyecto usa `spatie/laravel-permission` para asignar roles y permisos a
`User`. También existe una relación `Empleado`–`Sede` mediante
`empleado_sede`. Son controles distintos:

- Un **permiso** responde qué operación puede ejecutar el usuario.
- La **membresía de sede** responde en qué sede puede operar.
- La **sede activa** guardada en sesión no demuestra por sí sola que el usuario
  pertenezca a esa sede.

La aplicación tiene algunos controles explícitos, pero no una política uniforme
de autorización: gran parte de las rutas clínicas solo pide autenticación. La
selección de sede no verifica pertenencia; las policies de sede están
registradas, pero el controlador de sedes no las invoca. En cambio, algunos
flujos web de resultados comprueban tanto sede activa como membresía.

**Prioridad inmediata para backend:** retirar o asegurar la ruta `GET /migracion`.
Está dentro de un grupo autorizado con `can:eliminar_persona` y ejecuta
`migrate:fresh --seed`, una operación que puede borrar los datos de la base
actual. El seeder asigna `eliminar_persona` al rol `prestador`, por lo que dicho
rol satisface hoy ese middleware. Una petición GET no debe poder causar un
borrado de datos.

## Cómo están definidos los roles y permisos

### Modelo

- [User](../app/Models/User.php) incorpora `HasRoles`.
- [Empleado](../app/Models/Empleado.php) relaciona el usuario con una empresa y
  con una o varias sedes.
- [Sede](../app/Models/Sede.php) mantiene la relación muchos-a-muchos con
  empleados mediante `empleado_sede`.
- La configuración de Spatie usa los roles/permisos estándar y no activa
  `teams`; el rol no queda particionado por empresa o sede dentro de una misma
  base de datos.

### Roles y permisos del seeder

[RolesYPermisosSeeder](../database/seeders/RolesYPermisosSeeder.php) crea estos
roles: `admin`, `agente`, `prestador`, `paciente` y `contable`.

| Rol | Permisos asignados por el seeder | Observación |
|---|---|---|
| `admin` | Todos los permisos existentes en ese momento | Amplio; incorpora también permisos creados por otros componentes si existen al ejecutar el seeder. |
| `prestador` | `ver_resultado`, `crear_persona`, `editar_persona`, `ver_persona`, `ver_orden`, `eliminar_persona`, `ver_cuentas`, `crear_cuenta`, `editar_cuenta`, `ver_examen`, `crear_examen`, `editar_examen`, `ver_empresa`, `ver_facturas` | Recibe `eliminar_persona`, que además protege rutas administrativas y la ruta destructiva de migración. |
| `agente` | `ver_resultado`, `crear_persona`, `editar_persona`, `ver_persona`, `ver_orden` | No tiene `ver_facturas` ni `eliminar_persona` en el seeder. |
| `contable` | `ver_facturas` | Es el único rol no administrador al que el seeder asigna este permiso. |
| `paciente` | Ninguno | El rol existe, pero el seeder no le asigna permisos. |

El seeder no asigna roles a todos los usuarios automáticamente. Por ejemplo,
`ClienteSeeder` asigna `admin` al usuario administrador; `DemoSeeder` asigna
roles de demostración.

### Permisos usados fuera del seeder

Las policies y controladores también consultan nombres que no aparecen en la
lista base del seeder: `crear_sedes`, `editar_sedes`, `eliminar_sedes`,
`editar_empresa`, `gestionar_citas` y `eliminar_orden_medica`. No hay una
asignación declarada de esos permisos a los roles en
[RolesYPermisosSeeder](../database/seeders/RolesYPermisosSeeder.php). Debe
acordarse qué rol puede recibirlos y sembrarlos de forma consistente antes de
depender de ellos.

## Dónde se aplica autorización hoy

1. **Rutas generales autenticadas.** La mayoría de las rutas web —personas,
   órdenes, procedimientos y resultados— están bajo `auth`, sin middleware
   `can` por operación.
2. **Facturas y convenios.** El grupo requiere `auth`, `verified` y
   `can:ver_facturas`.
3. **Administración.** Otro grupo completo está protegido con
   `can:eliminar_persona`. Esto incluye empresas, empleados, sedes, catálogos,
   acciones de personas/órdenes y `/migracion`; el nombre del permiso no
   representa el alcance real de ese grupo.
4. **Policies registradas.** `EmpresaPolicy`, `SedePolicy` y
   `PreRegistroCitaPolicy` están registradas en [AppServiceProvider](../app/Providers/AppServiceProvider.php).
   Registrar una policy no basta: la ruta/controlador debe invocarla.
5. **Sedes.** `SedeController::elegirSede()` guarda en sesión cualquier sede
   resuelta por route model binding; no verifica que el empleado pertenezca a
   ella. `show()` y `edit()` tampoco invocan la policy de sede.
6. **Resultados web.** `ResultadosController::show()`, `edit()` y `update()`
   llaman a `autorizarSede()`. Comprueba que haya sede activa, que el
   procedimiento sea de esa sede y que el empleado esté asociado a ella.
   `store()` no llama a esa comprobación.
7. **Resultados API.** El endpoint de escritura tiene autenticación Sanctum
   por el grupo API, pero su controlador no verifica membresía de sede ni un
   permiso específico antes de guardar.
8. **Policy de sede.** `SedePolicy` compara pertenencia a la **empresa** del
   empleado con `empresa_id` de la sede; no verifica la relación
   `empleado_sede`. Aun conectada a rutas, no bastaría para responder “este
   usuario pertenece a esta sede”.

Referencias: [web.php](../routes/web.php),
[SedeController](../app/Http/Controllers/SedeController.php),
[SedePolicy](../app/Policies/SedePolicy.php),
[ResultadosController web](../app/Http/Controllers/ResultadosController.php) y
[ResultadosController API](../app/Http/Controllers/Api/ResultadosController.php).

## Hallazgos priorizados

### Crítico — endpoint destructivo protegido por permiso inadecuado

`GET /migracion` llama a `migrate:fresh --seed`. Está dentro del grupo
`can:eliminar_persona`; `prestador` recibe dicho permiso en el seeder. La ruta
debe retirarse de producción o trasladarse a un mecanismo de mantenimiento
seguro, no exponerse como acción GET autorizada con un permiso de negocio.
Backend debe corregirlo; no se implementó en esta tarea.

### Alto — se puede seleccionar una sede sin pertenecer a ella

`GET /administracion/sede/{sede}` requiere autenticación, pero
`SedeController::elegirSede()` no consulta `Empleado::sedes()`. La sesión puede
quedar apuntando a una sede ajena. Debe rechazarse con 403 (o la respuesta
estándar definida por backend) y no cambiar la sesión.

### Alto — controles de sede distintos entre operaciones

La lectura/edición web de resultados verifica membresía, pero el guardado web
normal y el guardado API no aplican el mismo control. Hace falta cubrir todas
las escrituras con pruebas cruzadas de sede y verificar que no haya cambios en
BD tras un 403.

### Alto — middleware administrativo demasiado amplio

`can:eliminar_persona` da acceso al conjunto de rutas administrativas, no solo
a eliminar personas. La autorización debe descomponerse por acciones concretas
y el mapa de roles/permisos debe aprobarse por responsable funcional.

### Medio — pruebas de policies no representan rutas actuales

[EmpresaPolicyTest](../tests/Feature/Policies/EmpresaPolicyTest.php) usa nombres
como `sedes.show`, `sedes.store` y `sedes.index`, mientras que el recurso se
registra bajo `/admin/sedes` (nombres `admin.sedes.*`, y excluye `index`).
Además, el test de admin no contiene una aserción. Esa suite necesita
actualización antes de considerarla cobertura válida del comportamiento web.

## Batería propuesta

Los siguientes casos son criterios de aceptación para backend. Los casos con
“pendiente” no deben considerarse aprobados solo porque una ruta responde: deben
comprobar la respuesta y la ausencia de cambios en base de datos/sesión.

| ID | Caso | Resultado esperado |
|---|---|---|
| AUTH-01 | Visitante accede a una ruta autenticada | Redirección a login en web; 401 en API. |
| AUTH-02 | Usuario autenticado pero no verificado visita una ruta que requiere verificación | Redirección al flujo de verificación. |
| PERM-01 | Usuario autenticado sin `ver_facturas` solicita facturas | 403. |
| PERM-02 | Rol `contable` asignado por el seeder accede a facturas | Acceso permitido, sin conceder permisos administrativos. |
| PERM-03 | Rol `agente` solicita facturas | 403 y sin acceso a los datos. |
| PERM-04 | Usuario autenticado sin autorización intenta una operación administrativa | 403. |
| PERM-05 | Rol `prestador` intenta acceder a `/migracion` | El endpoint no debe estar disponible en producción; nunca debe ejecutar migraciones destructivas. |
| SEDE-01 | Empleado asociado a sede A intenta seleccionar sede B de su misma empresa, sin membresía B | 403; sesión no cambia. **Pendiente: falla con el controlador actual.** |
| SEDE-02 | Usuario sin empleado intenta seleccionar cualquier sede | 403; sesión no cambia. **Pendiente.** |
| SEDE-03 | Empleado miembro de sede A selecciona sede A | Redirección exitosa y sede A queda en sesión. |
| SEDE-04 | Empleado con sede activa A solicita procedimiento de sede B | 403; no se modifica resultado/procedimiento. |
| SEDE-05 | Usuario no miembro de la sede activa intenta actualizar resultado | 403; valor e historial permanecen iguales. |
| SEDE-06 | Falta la sede activa en sesión al modificar resultados | 403 o redirección controlada; sin cambios. |
| SEDE-07 | Usuario no miembro intenta guardar resultado por POST web | 403; sin cambios. **Pendiente: `store()` no llama a `autorizarSede()`.** |
| SEDE-08 | Usuario no miembro intenta guardar resultado por API autenticada | 403; sin cambios. **Pendiente: API sin control de membresía.** |
| DATA-01 | Usuario pertenece a otra empresa/instancia | No obtiene registros ni puede mutarlos por ID directo. |
| DATA-02 | Solicitud denegada con payload de modificación | Verificar además `assertDatabaseHas` con el valor anterior y que no se creó auditoría falsa. |

### Ejemplos habituales en Pest/Laravel

En Feature tests se acostumbra usar `RefreshDatabase`, factories y `actingAs()`;
después se asertan tanto el código de respuesta como la persistencia. El
proyecto ya usa esos patrones en [ResultadosEdicionTest](../tests/Feature/ResultadosEdicionTest.php)
y [ResultadosLoteTest](../tests/Feature/ResultadosLoteTest.php).

#### Permiso concedido y denegado en la ruta de facturas

Ejemplo base para añadir a una suite Feature (los permisos/roles deben sembrarse
antes de asignarlos):

```php
use App\Models\User;
use Database\Seeders\RolesYPermisosSeeder;

beforeEach(function () {
    $this->seed(RolesYPermisosSeeder::class);
});

it('deniega facturas a un usuario autenticado sin el permiso', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('agente');

    $this->actingAs($user)
        ->get(route('facturas'))
        ->assertForbidden();
});

it('permite facturas al rol contable', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);
    $user->assignRole('contable');

    $this->actingAs($user)
        ->get(route('facturas'))
        ->assertSuccessful();
});
```

#### Rechazar selección de una sede sin membresía

Este caso expresa el comportamiento requerido y **debe fallar con el
controlador actual**, que hoy solo escribe la sede en sesión:

```php
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\Sede;
use App\Models\User;
use Database\Seeders\TipoDocumentoSeeder;

it('no permite seleccionar una sede donde el empleado no está vinculado', function () {
    $this->seed(TipoDocumentoSeeder::class);
    $empresa = Empresa::factory()->create();
    $sedeA = Sede::factory()->create(['empresa_id' => $empresa->id]);
    $sedeB = Sede::factory()->create(['empresa_id' => $empresa->id]);
    $user = User::factory()->create();

    $empleado = Empleado::factory()->create([
        'user_id' => $user->id,
        'empresa_id' => $empresa->id,
        'tipo_documento_id' => 1,
    ]);
    $empleado->sedes()->attach($sedeA->id);

    $this->actingAs($user)
        ->get(route('elegir.sede', $sedeB))
        ->assertForbidden()
        ->assertSessionMissing('sede');
});
```

En un caso de “usuario sin permiso suficiente”, se usa el mismo patrón:
autenticar un usuario con un rol válido que **no** tenga el permiso, invocar la
ruta protegida, afirmar 403 y comprobar que la entidad no cambió. No basta con
probar `hasRole()` o esconder el botón en la interfaz.

#### No mutar resultados cuando falla autorización de sede

Usar la preparación de datos de `ResultadosEdicionTest` y cambiar la sede activa
o la sede del procedimiento para que la relación no coincida. El patrón de
aserción debe incluir:

```php
$response->assertForbidden();
$this->assertDatabaseHas('resultados', [
    'id' => $resultado->id,
    'resultado' => $valorAnterior,
]);
$this->assertDatabaseCount('resultado_cambios', 0);
```

Repetir el caso para el POST web de captura y el endpoint API. Deben ser pruebas
separadas porque actualmente cada ruta atraviesa un controlador distinto.

## Orden sugerido de ejecución

1. Backend corrige/deshabilita la ruta destructiva y ajusta permisos
   administrativos.
2. Acordar matriz de permisos por rol y declarar todos los permisos del sistema
   en seeders.
3. Añadir pruebas Feature de autorización en rutas, empezando por selección de
   sede y escrituras de resultados web/API.
4. Hacer que la selección de sede y las escrituras comprueben
   `empleado_sede`, además de validar empresa/instancia.
5. Corregir `EmpresaPolicyTest` para usar rutas y resultados reales; agregar
   aserciones explícitas en cada test.
6. Ejecutar las Feature tests en el entorno de CI/contenedor con SQLite PDO
   disponible y completar una pasada de regresión.

## Validación de esta revisión

Se intentó ejecutar `php artisan test tests/Feature/Policies/EmpresaPolicyTest.php`.
No fue posible evaluar los resultados funcionales: el PHP local no tiene el
driver PDO SQLite (`could not find driver`). Además, la suite existente contiene
referencias a nombres de rutas que no coinciden con el resource actual; por eso
se requiere corregir/ejecutar la batería en el entorno de pruebas apropiado.

Los casos de esta guía son una especificación de pruebas y no se añadieron como
Feature tests ejecutables: las correcciones requeridas afectan backend y el
alcance vigente de trabajo se limita a frontend/documentación.

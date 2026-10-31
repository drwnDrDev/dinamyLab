# Manual de DinamyLAB

**Estado documentado:** 31-oct-2026
**Propósito:** describir la aplicación que existe en esta rama, sus flujos
principales y sus límites conocidos. No es una certificación de preparación para
producción ni reemplaza la validación clínica, legal o de despliegue.

## 1. Qué es la aplicación

DinamyLAB es una aplicación web para apoyar la operación de un laboratorio
clínico: administrar personas, registrar órdenes, convertirlas en procedimientos,
capturar resultados y consultar su historial. Incluye además administración de
catálogos y sedes, pre-registro de citas, Convenios y algunos componentes
administrativos.

La aplicación está construida con Laravel, vistas Blade y componentes React
montados en páginas concretas. React complementa Blade; no es una SPA completa.
La interfaz está en español y el sistema contempla conceptos del dominio
colombiano, pero la existencia de campos, reportes o formatos no implica
certificación regulatoria.

## 2. Arquitectura y aislamiento

- El código web y sus componentes se sirven desde Laravel y Blade.
- Los componentes React se compilan con Vite y se montan en vistas Blade
  específicas.
- La configuración en [compose.yaml](../compose.yaml) contiene servicios
  `web` (Nginx), `app`
  (PHP-FPM/Laravel), `queue` (trabajos en segundo plano), `db` (MySQL 8.4) y
  `vite` (Node 22/pnpm).
- El diseño de operación actual es **una instalación y una base de datos por
  cliente**, con `.env`, almacenamiento y sesiones separados. No se debe
  interpretar como aislamiento automático de varias organizaciones dentro de
  una misma base compartida.
- El aislamiento entre instancias depende también de configurar correctamente
  dominios, variables `APP_INSTANCE`/`APP_HOSTS`, cookies y credenciales de base
  de datos. No compartir `SESSION_DOMAIN` entre clientes.
- Dentro de una instancia puede haber empresas/sedes y existen controles de
  selección y autorización, pero los controles por sede y recurso no son
  uniformes en todos los flujos. No usar una sola instancia compartida para
  organizaciones distintas sin un diseño y pruebas adicionales.

La guía de [despliegue por instancia](./DESPLIEGUE_INSTANCIAS.md) y el diseño de
[multicliente](./superpowers/specs/2026-10-30-multitenant-design.md) describen
esta estrategia con más detalle.

## 3. Flujo operativo principal

### 3.1 Personas

La entidad Persona sirve para registrar a las personas relacionadas con la
operación, como pacientes y acompañantes. Los formularios permiten ingresar
datos de identificación y contacto que luego se relacionan con las órdenes.

El listado web de personas está paginado. La consulta del API no tiene el mismo
comportamiento y puede recuperar el conjunto completo; no asumir que todos los
listados están paginados.

La edición web de personas está incompleta: las acciones web de edición,
actualización y eliminación son stubs. Existe una ruta de actualización por
API, pero eso no completa por sí solo el flujo web.

### 3.2 Cargador de listas

El cargador interpreta una lista de personas, muestra las coincidencias que
encuentra y permite seleccionar una para cargarla en el formulario. Su función
es facilitar la captura; **no** es una importación masiva que cree
automáticamente todas las personas. Ver [MODULO_CARGADOR_LISTA_PERSONAS.md](./MODULO_CARGADOR_LISTA_PERSONAS.md)
y la implementación descrita en el [índice de ejemplos](./INDEX.md).

### 3.3 Órdenes y procedimientos

Una orden relaciona al paciente, puede registrar un acompañante y reúne los
exámenes solicitados. El flujo valida campos de la orden, comprueba los
exámenes seleccionados, calcula valores y genera procedimientos para su
seguimiento.

Los procedimientos representan el trabajo asociado a la orden y tienen estado
y datos de ejecución. La autorización y el control de sede deben verificarse
para cada ruta; no todas las superficies tienen una cobertura homogénea.

### 3.4 Resultados e historial

Hay flujos para registrar resultados y consultar resultados asociados a
procedimientos. La edición y el procesamiento por lotes cuentan con servicios
dedicados y pruebas; no toda la captura normal está centralizada de la misma
forma y existen rutas web/API con lógica diferente.

El historial permite consultar resultados y ofrece una vista imprimible desde
el navegador en algunos recorridos. No se encontró un flujo de generación de
PDF implementado en la aplicación: tener una dependencia de PDF no equivale a
tener la función completa.

Por tratarse de datos clínicos, confirmar la persona y la orden antes de
guardar o entregar resultados. Los valores, unidades y rangos de referencia
deben validarse con responsables del laboratorio; los ejemplos de datos no
sustituyen catálogos aprobados.

## 4. Otros módulos presentes

### Citas y pre-registro

Existe un formulario público de pre-registro de citas y rutas relacionadas para
recibir la solicitud. El correo de confirmación aparece como pendiente en el
código; los ejemplos de notificación push, analítica o estadísticas no prueban
que esos servicios estén conectados.

### Convenios

La vista de creación de Convenios monta un formulario React mediante Vite y
Laravel suministra parte de sus datos. El formulario utiliza listas auxiliares
del navegador (`localStorage`) para autocompletar. La documentación antigua
propone inicializaciones y rutas que pueden estar desactualizadas; comprobar
las claves actuales y probar el envío y la persistencia antes de depender de
ese flujo.

### Facturación

Hay estructura y pantallas relacionadas con facturas/resoluciones, y existe un
ejemplo de generación UBL. Sin embargo,
[FacturaController](../app/Http/Controllers/FacturaController.php) contiene un
`dd()` antes de persistir: **la creación de factura está bloqueada y no se debe
presentar como funcional**.

### RIPS

Hay reportes y exportación JSON relacionados con RIPS. La implementación
conserva valores o perfiles de prueba en algunos puntos; no asumir conformidad
con requisitos de reporte o envío vigentes sin revisión clínica/técnica y
validación con el responsable normativo.

### Sedes y usuarios

La aplicación dispone de administración y selección de sedes y de usuarios
autenticados. El alcance real de cada rol depende de las rutas, middleware y
políticas aplicados a cada operación. La presencia de una policy o de una
pantalla administrativa no demuestra cobertura completa.

## 5. Cómo iniciar el entorno de desarrollo

La guía detallada es [ENTORNO_DOCKER_WSL.md](./ENTORNO_DOCKER_WSL.md). Resumen
de la secuencia local:

1. Tener Docker con Compose, clonar el repositorio dentro del sistema de
   archivos Linux si se trabaja desde WSL y copiar `.env.example` a `.env`.
2. Definir las credenciales locales de base de datos en `.env`; no compartir ni
   versionar secretos.
3. Levantar servicios:

   ```bash
   docker compose up -d --build
   docker compose exec app composer install
   docker compose exec app php artisan key:generate
   docker compose exec app php artisan migrate --seed
   docker compose restart queue
   ```

4. Abrir `http://localhost:8000`. Vite expone el puerto `5173`; MySQL se expone
   por el puerto local configurado en `.env` (por defecto, `3307`).
5. Comandos habituales:

   ```bash
   docker compose ps
   docker compose logs -f app
   docker compose exec app php artisan test
   docker compose exec vite corepack pnpm build
   docker compose down
   ```

`docker compose down` conserva el volumen de base de datos. En desarrollo,
`migrate --seed` carga datos de demostración; **no usar esos seeders como
inicialización de producción**. Los tests del proyecto usan SQLite en memoria,
por lo que los cambios que dependan de MySQL también deben probarse en el
entorno MySQL.

La guía Docker contiene referencias a Laravel 12 y afirmaciones de seguridad
históricas que ya no representan esta rama. El proyecto actual requiere PHP
`^8.5` y usa Laravel 13, según [composer.json](../composer.json).

## 6. Despliegue

El modelo documentado para cPanel es mantener una instalación separada por
cliente, apuntar el dominio al directorio `public/` de esa instalación y asignar
una base de datos, `.env`, clave de aplicación y `storage/` propios. Seguir el
runbook [DESPLIEGUE_INSTANCIAS.md](./DESPLIEGUE_INSTANCIAS.md) y validar el
dominio y el aislamiento antes de habilitar datos reales.

Hay un bloqueo operativo que debe resolverse antes de considerar automatizado
el despliegue:

- `vendor/` no está versionado y está excluido por [.gitignore](../.gitignore).
- [.cpanel.yml](../.cpanel.yml) solo ejecuta `config:cache`, `route:cache` y
  `view:cache`; no instala dependencias.
- El runbook deja como decisión pendiente si el host puede ejecutar Composer
  o si se usará otro mecanismo para disponer de `vendor/`.
- Las migraciones no se ejecutan automáticamente; el runbook las trata como
  paso manual y explícito.

Por tanto, el archivo `.cpanel.yml` no basta por sí solo para hacer un clone
desplegable. Resolver la estrategia de dependencias y probar el procedimiento
en una instancia no productiva antes de un release.

## 7. Seguridad, autorización y datos

- La arquitectura busca separar clientes mediante instalaciones y bases de
  datos independientes. El catálogo de exámenes puede ser común dentro de una
  instalación y eso, por sí mismo, no demuestra una fuga entre clientes.
- No confundir aislamiento por despliegue con aislamiento multiempresa dentro
  de una base de datos compartida.
- Hay policies y middleware, pero deben revisarse por recurso y acción. La
  documentación de policies contiene ejemplos y propuestas que no equivalen a
  controles ya instalados en todas las rutas.
- El control de sede no es uniforme en todos los flujos de resultados; validar
  permisos y sede activa para cualquier operación sensible.
- Evitar datos personales o clínicos en URLs, logs, ejemplos públicos y
  capturas. Los archivos de muestra se indexan por nombre y propósito, no como
  fuente de datos clínicos.
- Para producción, `APP_DEBUG` debe estar desactivado y los secretos deben
  residir fuera del repositorio.

## 8. Qué está avanzado y qué no debe darse por terminado

| Área | Estado que se puede sostener |
|---|---|
| Registro de personas, órdenes y procedimientos | El flujo principal está representado en la aplicación; deben probarse permisos y casos de error de extremo a extremo. |
| Edición/procesamiento de resultados | Hay servicios y pruebas para edición y procesamiento por lotes; no toda la captura está unificada. |
| Aislamiento de clientes | El diseño vigente es una instancia y base de datos por cliente. No se acredita aislamiento completo entre organizaciones que compartan una base. |
| Refactor de controladores | El número de controladores o servicios no basta para decidir calidad. Parte del manejo de resultados ya usa servicios; quedan flujos con lógica en controladores. |
| Edición web de personas | Incompleta; algunas acciones son stubs. |
| Importación de listas | Parseo y selección asistida; no creación masiva persistente. |
| Facturas | La creación no está terminada, bloqueada por un `dd()` en el controlador. |
| Reportes PDF | No se encontró generación PDF conectada al flujo real. |
| Email de pre-registro | Pendiente; existe un TODO explícito. |
| RIPS | Hay exportación/reportes, pero requieren validación antes de uso regulatorio. |
| Despliegue cPanel | Runbook presente, pero falta resolver la instalación de `vendor/` y probar la automatización. |

## 9. Relectura de diagnósticos anteriores

Los documentos de arquitectura y propuesta comercial son históricos. Sus
porcentajes de avance, plazos, costos, versiones, conteos y afirmaciones como
“listo para producción” no deben reutilizarse sin una nueva medición.

- **Aislamiento de organización:** la preocupación es pertinente si varias
  organizaciones van a compartir base de datos. El diseño actual evita ese
  escenario mediante instancias separadas; no se debe describir como
  multi-tenancy compartido ya resuelto.
- **Lógica de negocio en controladores:** sigue siendo una línea válida de
  mejora, pero el conteo bruto de controladores frente a servicios no mide por
  sí solo el problema. El flujo de resultados tiene avances con servicios y
  pruebas; la captura ordinaria aún no está centralizada completamente.
- **Producción:** los bloqueos concretos identificados en este corte son más
  útiles que el porcentaje comercial previo: factura con `dd()`, ausencia de
  flujo PDF confirmado, correo pendiente, RIPS por validar y estrategia de
  Composer para cPanel sin resolver.

## 10. Documentación relacionada

El [índice de documentos y ejemplos](./INDEX.md) identifica qué textos son
runbooks, diseños, propuestas históricas o ejemplos de código. Cuando una
instrucción de un documento antiguo contradiga este manual o el código, validar
la implementación actual y actualizar ambos documentos en la siguiente tarea
documental.

# Monitor de eventos

El menú **Monitor de eventos**, disponible para el rol `admin`, abre `/admin/actividad`. Permite filtrar por usuario, actividad y fechas, pagina de 50 en 50 y ofrece un botón para actualizar la consulta. La hora se presenta en `America/Bogota`; se almacena en UTC. Es una consulta de historial, sin actualización automática en tiempo real.

Se registra desde la instalación de la migración; no reconstruye actividad anterior.

## Actividades

- Inicio y cierre de sesión; registro de nuevos usuarios por el flujo de registro.
- Creación de pacientes y órdenes mediante eventos de los modelos, tanto desde web como API.
- Actualización de pacientes desde web y API, incluidos cambios de contacto sin modificar los datos principales.
- Creación de cada resultado por parámetro, corrección de resultados y resumen del procesamiento de un lote.
- Consulta del reporte RIPS y generación/descarga del JSON: sede, intervalo y cantidades, sin copiar su contenido.
- Acceso a ficha/historia del paciente desde `personas.show`, consulta individual API y vistas de historia clínica. Se conserva el ID del paciente y el origen del acceso.
- Entrega de un PDF de resultados desde el servidor.
- Solicitud de impresión en el reporte actual, incluido el atajo del navegador, mediante `beforeprint`.

Cada fila conserva usuario y nombre en el momento del evento, actividad, fecha/hora, tipo e ID del recurso, sede/empresa disponibles y detalles técnicos limitados a IDs, campos y cantidades. No se copian valores clínicos, documentos, contraseñas ni el cuerpo completo de las peticiones. Para resultados la referencia principal es el procedimiento; el detalle contiene el ID del resultado y del parámetro. Para un lote se conservan los procedimientos incluidos y su cantidad.

Las escrituras efectuadas dentro de una transacción comparten su rollback con los eventos. Las operaciones rechazadas antes de guardar no generan una actividad completada. Las acciones sin usuario autenticado, como los seeders de consola, no se incluyen. El registro no ofrece endpoints de edición o eliminación; no sustituye una protección contra modificaciones directas de la base de datos.

## Alcance y límites

La impresión registra una señal del navegador, no confirma la impresión física: puede cancelarse y requiere que llegue la petición de registro al servidor. El token cifrado identifica los procedimientos del reporte, está ligado al usuario y vence a las ocho horas; el servidor vuelve a comprobar permiso y acceso a la sede. No se registra la apertura de la vista como si fuera una impresión. No se puede detectar una impresión posterior de un PDF ya descargado fuera de la aplicación. Referencia: [evento beforeprint](https://developer.mozilla.org/en-US/docs/Web/API/Window/beforeprint_event).

La descarga registra la respuesta PDF generada; no confirma que el archivo termine de guardarse en el dispositivo.

El monitor sigue la administración global actual: los administradores pueden consultar los eventos de la instancia. Los campos empresa/sede aportan contexto, pero no implementan el aislamiento multiempresa propuesto en el roadmap de Fortify/Teams. Si no puede determinarse el contexto, queda vacío; no se asigna una empresa por defecto.

## Instalación y operación

En cada entorno, aplicar la migración antes de reabrir tráfico con el código nuevo:

```sh
php artisan migrate --path=database/migrations/2026_11_17_000000_create_eventos_actividad_table.php
```

No requiere paquetes adicionales ni cambios a los roles existentes. En despliegues con workers persistentes, reiniciarlos con el procedimiento habitual. Si se usa caché de rutas/vistas, regenerarla tras desplegar. Respaldar la tabla `eventos_actividad` junto con la base de datos. La retención queda a cargo de la operación; no hay borrado automático.

Pruebas específicas: `tests/Feature/MonitorActividadTest.php`, más las aserciones de auditoría en las pruebas de lotes y descarga de reportes. El monitor registra operaciones persistidas y no es un inventario de intentos fallidos de acceso.

# Índice de documentación

**Actualizado:** 2026-11-01. Documentos que coinciden con el código de la rama `maloca`.

## Documentos vigentes

| Archivo | Para qué sirve |
|---|---|
| [MANUAL.md](./MANUAL.md) | **Empezar aquí.** Funcionamiento de la aplicación, flujos, roles y permisos, hallazgos de autorización y estado por módulo. |
| [ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md](./ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md) | Estado técnico, deuda, costos, pricing (incluye el borrador del Plan Emprendedor), tiempos y riesgos para propuestas comerciales. |
| [ENTORNO_DOCKER_WSL.md](./ENTORNO_DOCKER_WSL.md) | Montar el entorno local con WSL 2 + Docker Compose. |
| [DESPLIEGUE_INSTANCIAS.md](./DESPLIEGUE_INSTANCIAS.md) | Runbook de cPanel: una instancia por cliente, migraciones, operación. |
| [CLAUDE.md](./CLAUDE.md) | Instrucciones internas para Claude Code; no es documentación de usuario. |

## Diseño y planes

| Archivo | Para qué sirve |
|---|---|
| [superpowers/specs/2026-10-30-multitenant-design.md](./superpowers/specs/2026-10-30-multitenant-design.md) | Diseño del aislamiento y del despliegue por instancia. |
| [superpowers/plans/2026-10-30-despliegue-multicliente.md](./superpowers/plans/2026-10-30-despliegue-multicliente.md) | Plan de trabajo del despliegue multicliente. Que una tarea esté listada no significa que esté terminada. |

## Ejemplos fuera de `docs/`

Material ilustrativo o prototipos. No son rutas de ejecución, contratos API ni código aprobado para producción.

| Archivo | Advertencia |
|---|---|
| [EjemploSistemaCitas.jsx](../resources/js/components/EjemploSistemaCitas.jsx) | Interfaz de ejemplo de pre-registro; las notificaciones y la analítica son ilustrativas. |
| [CrearOrdenComponentMejorado.jsx](../resources/js/components/ejemplos/CrearOrdenComponentMejorado.jsx) | Propuesta de interfaz; no reemplaza el flujo montado en Blade. |
| [ejemplo-inicializar-paises.js](../resources/js/ejemplos/ejemplo-inicializar-paises.js) | Sus claves de `localStorage` no coinciden del todo con el formulario de Convenios. |
| [ejemplo15.json](../resources/utils/ejemplo15.json) | Estructura de muestra de un examen; no son datos clínicos reales. |
| [ejemploCrearFromulario.js](../resources/utils/ejemploCrearFromulario.js), [ejemploStore.php](../resources/utils/ejemploStore.php) | Código legado o de muestra. |
| [factura_ubl_ejemplo.php](../resources/utils/factura_ubl_ejemplo.php), [factura_ejemplo.xml](../storage/app/factura_ejemplo.xml) | Muestras UBL; no prueban que la facturación funcione. |

## Mantener este índice

- Un documento nuevo en `docs/` se agrega aquí con una línea que explique para qué sirve.
- Si un documento deja de coincidir con el código, se corrige o se borra; no se conserva como "histórico".
- Las cifras (horas, costos, versiones) viven solo en el documento de viabilidad, para no duplicarlas.

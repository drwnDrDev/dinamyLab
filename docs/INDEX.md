# Índice de documentación y ejemplos

Este índice reúne los documentos Markdown de `docs/` y los ejemplos relevantes del
repositorio. El estado se contrastó con el código disponible en la rama al
31-oct-2026. La documentación fechada o de ejemplo sirve como contexto, no como
prueba de que una función esté lista para producción.

## Guía principal

- [Manual de la aplicación](./MANUAL_DEFINITIVO.md): guía consolidada de los
  módulos, flujos, operación y límites que se pueden confirmar en el código.
- [INDEX.md](./INDEX.md): este índice de documentos y ejemplos.

## Documentación de `docs/`

### Operación y arquitectura

| Archivo | Tema y vigencia |
|---|---|
| [DESPLIEGUE_INSTANCIAS.md](./DESPLIEGUE_INSTANCIAS.md) | Runbook de cPanel para desplegar una instancia completa por cliente. Es la referencia operativa más actual, pero deja pendiente resolver cómo se instalará `vendor/` en el servidor. |
| [ENTORNO_DOCKER_WSL.md](./ENTORNO_DOCKER_WSL.md) | Instalación y trabajo local con Docker Compose y WSL. Los comandos y servicios coinciden con `compose.yaml`; la afirmación de que el proyecto usa Laravel 12 está desactualizada: el código actual usa Laravel 13. |
| [superpowers/specs/2026-10-30-multitenant-design.md](./superpowers/specs/2026-10-30-multitenant-design.md) | Especificación del aislamiento y el despliegue por instancia. Contrastar decisiones de diseño con el runbook y la configuración real. |
| [superpowers/plans/2026-10-30-despliegue-multicliente.md](./superpowers/plans/2026-10-30-despliegue-multicliente.md) | Plan de trabajo para el despliegue multicliente; una lista de tareas no certifica que todo esté terminado. |
| [ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md](./ARQUITECTURA_Y_VIABILIDAD_COMERCIAL.md) | Diagnóstico técnico/comercial fechado el 14-oct-2026. Sus métricas, porcentajes de avance, estimaciones, versiones y roadmap son una fotografía histórica, no el estado actual. |
| [openCode_comercial.md](./openCode_comercial.md) | Propuesta comercial y de alcance. No es especificación funcional ni evidencia de que las características y precios estén aprobados. |
| [RESUMEN_EJECUTIVO.md](./RESUMEN_EJECUTIVO.md) | Resumen ejecutivo histórico relacionado con la evaluación comercial; verificar cada afirmación frente al código. |
| [ARCHIVO_RESUMEN.md](./ARCHIVO_RESUMEN.md) | Resumen histórico del componente de Convenios. Revisar junto con el código actual. |

### Políticas y aislamiento

| Archivo | Tema y vigencia |
|---|---|
| [PRUEBAS_ROLES_PERMISOS.md](./PRUEBAS_ROLES_PERMISOS.md) | Diagnóstico actualizado de roles, permisos y membresía de sede; incluye matriz de casos y ejemplos Pest propuestos. Los bloqueos de backend quedan señalados, no implementados. |
| [SISTEMA_POLICIES_EMPRESA.md](./SISTEMA_POLICIES_EMPRESA.md) | Diseño de autorización y separación de datos por empresa. Distinguir las políticas implementadas de las propuestas. |
| [README_POLICIES.md](./README_POLICIES.md) | Guía y estado del trabajo de policies; no implica que todos los recursos estén cubiertos. |
| [EJEMPLOS_IMPLEMENTACION_POLICIES.md](./EJEMPLOS_IMPLEMENTACION_POLICIES.md) | Ejemplos de autorización. Son referencias; no asumir que sus rutas, firmas o reglas coinciden con el sistema actual. |

### Componentes y flujos

| Archivo | Tema y vigencia |
|---|---|
| [MODULO_CARGADOR_LISTA_PERSONAS.md](./MODULO_CARGADOR_LISTA_PERSONAS.md) | Explica el cargador que parsea una lista y permite escoger personas para el formulario. No equivale a una importación masiva que persista todos los registros. |
| [COMPONENTE_CONVENIO_REACT.md](./COMPONENTE_CONVENIO_REACT.md) | Diseño e integración del formulario React de Convenios. Algunas instrucciones de integración quedaron superadas por su montaje en la vista actual. |
| [INICIO_RAPIDO_CONVENIO_REACT.md](./INICIO_RAPIDO_CONVENIO_REACT.md) | Inicio rápido del componente de Convenios; revisar las claves de `localStorage` antes de seguir sus instrucciones. |
| [EJEMPLOS_VISUALES_CONVENIO.md](./EJEMPLOS_VISUALES_CONVENIO.md) | Variantes visuales de Convenios, no especificación del flujo actual. |
| [EJEMPLO_INTEGRACION_LARAVEL.php](./EJEMPLO_INTEGRACION_LARAVEL.php) | Fragmentos ilustrativos de integración Laravel. No copiar directamente: incluye propuestas de rutas y métodos que pueden no existir. |
| [CLAUDE.md](./CLAUDE.md) | Instrucciones internas de trabajo del proyecto; no es una guía para usuarios finales. |

## Ejemplos fuera de `docs/`

Los siguientes archivos son material ilustrativo o prototipos. No se deben tratar
como rutas de ejecución, contratos API ni código aprobado para producción.

| Archivo | Contenido / advertencia |
|---|---|
| [EjemploSistemaCitas.jsx](../resources/js/components/EjemploSistemaCitas.jsx) | Ejemplo de interfaz de pre-registro de citas. Algunas integraciones descritas en ejemplos relacionados (notificaciones y analítica) son ilustrativas. |
| [CrearOrdenComponentMejorado.jsx](../resources/js/components/ejemplos/CrearOrdenComponentMejorado.jsx) | Propuesta de interfaz de creación de órdenes; no sustituye el flujo realmente montado en Blade. |
| [ejemplo-inicializar-paises.js](../resources/js/ejemplos/ejemplo-inicializar-paises.js) | Ejemplo para inicializar datos de países en el navegador. Sus claves y opciones no están alineadas completamente con el uso actual del formulario de Convenios. |
| [EJEMPLOS_USO.md](../resources/utils/EJEMPLOS_USO.md) | Ejemplos de uso del pre-registro, estadísticas y notificaciones. Incluye escenarios que requieren desarrollo adicional en el servidor. |
| [ejemplo15.json](../resources/utils/ejemplo15.json) | Estructura de muestra para parámetros y resultados de un examen; no representa datos clínicos reales ni un catálogo normativo. |
| [ejemploCrearFromulario.js](../resources/utils/ejemploCrearFromulario.js) | Script de ejemplo/legado para un formulario. No es el punto de entrada React actual. |
| [ejemploStore.php](../resources/utils/ejemploStore.php) | Fragmento PHP de ejemplo de creación de órdenes; contiene lógica de muestra y no es una guía de arquitectura vigente. |
| [factura_ubl_ejemplo.php](../resources/utils/factura_ubl_ejemplo.php) | Ejemplo para generar una estructura de factura UBL; no prueba que la facturación esté operativa en la aplicación. |
| [factura_ejemplo.xml](../storage/app/factura_ejemplo.xml) | XML de muestra almacenado en `storage/`; no es salida generada por un flujo productivo. Evitar publicar o reutilizar datos de cualquier archivo de muestra sin verificarlos. |

## Lectura recomendada

1. Empezar por el [Manual de la aplicación](./MANUAL_DEFINITIVO.md).
2. Para levantar el entorno local, seguir [ENTORNO_DOCKER_WSL.md](./ENTORNO_DOCKER_WSL.md)
   y tratar la versión Laravel 12 indicada allí como obsoleta.
3. Para despliegues, revisar [DESPLIEGUE_INSTANCIAS.md](./DESPLIEGUE_INSTANCIAS.md)
   junto con `.cpanel.yml`; no considerar listo el deploy hasta resolver Composer
   en cPanel.
4. Consultar documentos de Convenios, policies y ejemplos como antecedentes, y
   validar cada paso contra las vistas, rutas y componentes actuales.

## Criterio para actualizar este índice

Al completar una integración, mover el documento asociado a la sección
correspondiente y anotar si es guía vigente, diseño o ejemplo. No conservar como
hecho actual estimaciones, versiones, endpoints o estados de tareas tomados de un
documento histórico.

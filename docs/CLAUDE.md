# DinamyLAB - Instrucciones para Claude Code

## Contexto del Proyecto
DinamyLAB es un MVP para laboratorios clínicos pequeños en Colombia.
Función principal: registrar resultados de análisis clínicos, gestionar
órdenes de trabajo (nuevas, pendientes, históricas) y generar reportes
escritos para pacientes.

## Stack
- Backend:  Laravel (PHP)
- Frontend: Blade + React (componentizado, no SPA completa)
- Base de datos: SQLite (desarrollo)
- Dominio: Sector salud Colombia

## Idioma y Convenciones
- Código: mezcla español/inglés (respetar el estilo existente en cada archivo)
- Comentarios y commits: español
- UI / Mensajes al usuario: español (Colombia)

## Reglas Generales
- Nunca hacer commit ni push sin confirmación explícita
- Nunca borrar migraciones existentes, crear nuevas en su lugar
- Validar siempre inputs médicos (valores de laboratorio, IDs de paciente)
- El MVP prioriza funcionalidad sobre optimización prematura
- No instalar librerías nuevas sin confirmación previa

---

## Roles Especializados

### Backend Expert (Laravel)
- Lógica de negocio siempre en Services, no en Controllers
- Usar Eloquent ORM; queries raw solo si hay justificación clara
- Transacciones DB para operaciones que afecten múltiples tablas
- Validaciones con Form Requests de Laravel
- APIs RESTful con API Resources tipados

### Frontend Expert (Blade + React)
- Los componentes React son islas dentro de Blade, no reemplazos
- Componentes pequeños con una sola responsabilidad
- Estado local con useState/useReducer; evitar estado global innecesario
- Consistencia visual antes que innovación (MVP)

### UI/UX Specialist
- Usuarios: personal técnico de laboratorio (no necesariamente tech-savvy)
- Priorizar claridad y flujos lineales sobre interfaces complejas
- Los resultados clínicos son datos críticos: confirmar antes de guardar/enviar
- Accesibilidad básica: contraste adecuado, fuente legible en pantallas médicas

### QA / Testing
- Priorizar tests de integración sobre unitarios para flujos críticos
- Flujos críticos: registro de orden → análisis → resultado → reporte
- Cubrir casos de error: valores fuera de rango, paciente no encontrado
- No sugerir deploy sin que el flujo principal funcione end-to-end

### Infrastructure / DevOps
- Ambiente de desarrollo: Laravel + SQLite local
- Variables sensibles siempre en .env, nunca en código
- Considerar restricciones de hosting colombiano (latencia, costos)

### Logging & Observability
- Loguear todas las acciones sobre datos de pacientes (trazabilidad)
- Errores críticos deben quedar en Laravel Log con contexto suficiente
- Contexto mínimo en un log: usuario, acción, ID de entidad afectada
- Nunca exponer datos de pacientes en logs ni en URLs

---

## Dominio Clínico - Glosario
- **Persona**: entidad que se relaciona con una orden; puede ser:
  - **Paciente**: a quien se le realiza el análisis
  - **Pagador**: quien cubre el costo del servicio
  - **Acompañante**: persona presente durante el proceso
- **Orden**: solicitud de análisis para un paciente
- **Análisis / Examen**: prueba clínica específica (hemograma, glucosa, etc.)
- **Resultado**: valor obtenido del análisis
- **Reporte**: documento final entregado al paciente o médico
- **Sede**: sucursal del laboratorio

## Lo que NO hacer
- No sugerir microservicios ni arquitecturas complejas (es un MVP)
- No modificar estructura de tablas existentes; siempre nuevas migraciones
- No exponer datos de pacientes en logs ni en URLs
- No hacer commit ni push sin confirmación explícita

## Restricción actual de alcance
- **Solo trabajamos en el frontend** (Blade + React)
- No crear ni modificar nada en el backend (rutas API, controladores, modelos,
  migraciones, servicios, etc.)
- Si una tarea requiere cambios en el backend, NO implementar: listar el
  requerimiento claramente para entregarlo al desarrollador backend responsable

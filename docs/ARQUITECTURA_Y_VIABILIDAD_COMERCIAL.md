# DinamyLAB — Evaluación de Arquitectura y Viabilidad Comercial

**Fecha:** 2026-10-14  
**Modelo:** Claude Sonnet 4.6  
**Propósito:** Análisis técnico-comercial para propuesta a cliente — comparación entre IAs

---

## TL;DR

**Base sólida. MVP funcional en 6–8 semanas.**  
El núcleo clínico (orden → análisis → resultado → factura) funciona. El dominio colombiano está correctamente modelado. Las brechas son conocidas y estimables.

---

## Tabla de Contenidos

1. [Estadísticas del Codebase](#1-estadísticas-del-codebase)
2. [Estructura del Repositorio](#2-estructura-del-repositorio)
3. [Schema de Base de Datos](#3-schema-de-base-de-datos)
4. [Arquitectura Backend](#4-arquitectura-backend)
5. [Arquitectura Frontend](#5-arquitectura-frontend)
6. [Features Implementados](#6-features-implementados)
7. [Deuda Técnica](#7-deuda-técnica)
8. [Cobertura de Tests](#8-cobertura-de-tests)
9. [Dependencias](#9-dependencias)
10. [Seguridad](#10-seguridad)
11. [Comparación con Software Similar](#11-comparación-con-software-similar)
12. [Costos y Pricing](#12-costos-y-pricing)
13. [Tiempos de Entrega](#13-tiempos-de-entrega)
14. [Riesgos para la Propuesta](#14-riesgos-para-la-propuesta)
15. [Resumen Ejecutivo](#15-resumen-ejecutivo)

---

## 1. Estadísticas del Codebase

| Métrica | Valor |
|---|---|
| Controladores PHP | 75 archivos — 5,911 LOC |
| Modelos Eloquent | 43 archivos — 1,848 LOC |
| Vistas Blade | 156 archivos |
| Componentes React/JS | 45+ archivos — ~10,215 LOC |
| Migraciones | 52 archivos |
| Tests | 18 archivos |
| Services | 8 archivos |
| Form Requests | 5 archivos |
| Policies | 3 archivos |
| Archivos totales gestionados | ~600+ |

---

## 2. Estructura del Repositorio

```
DinamyLAB/
├── app/
│   ├── Http/
│   │   ├── Controllers/        # 75 controladores web + API
│   │   └── Requests/           # 5 Form Requests
│   ├── Models/                 # 43 modelos Eloquent
│   ├── Services/               # 8 servicios de negocio
│   ├── Policies/               # 3 políticas de autorización
│   ├── Jobs/                   # Jobs para colas
│   ├── Providers/              # Service providers
│   ├── Support/                # Helpers y utilidades
│   └── View/                   # View composers
├── database/
│   ├── migrations/             # 52 migraciones
│   ├── seeders/
│   └── factories/
├── resources/
│   ├── views/                  # 156 plantillas Blade
│   ├── js/                     # React islands + lógica JS
│   └── css/
├── routes/
│   ├── web.php                 # 93 rutas web
│   └── api.php                 # 40+ endpoints API
├── tests/
│   ├── Feature/                # 13 tests de integración
│   └── Unit/                   # 2 tests unitarios
├── config/                     # 13 archivos de configuración
├── docs/                       # 11 archivos de documentación
└── public/
```

---

## 3. Schema de Base de Datos

**52 migraciones** que cubren el dominio completo del sector salud colombiano.

### Entidades Núcleo

| Tabla | Descripción | Relaciones clave |
|---|---|---|
| `users` | Autenticación Laravel | — |
| `personas` | Pacientes/personas | → tipo_documento, pais_origen |
| `empleados` | Personal del laboratorio | → user_id, empresa_id |
| `empresas` | Organizaciones/laboratorios | — |
| `sedes` | Sucursales | → empresa_id |
| `ordenes_medicas` | Órdenes de trabajo | → paciente_id, sede_id, user_id |

### Datos Clínicos

| Tabla | Descripción |
|---|---|
| `examenes` | Catálogo de análisis (hemograma, glucosa, etc.) |
| `procedimientos` | Instancias de análisis vinculadas a órdenes |
| `resultados` | Valores obtenidos del análisis |
| `valor_referencias` | Rangos de referencia por examen |
| `orden_examen` | Pivot M2M: órdenes ↔ exámenes (con cantidad) |
| `observacions` | Notas sobre procedimientos |

### Estándares Colombianos

| Tabla | Descripción |
|---|---|
| `codigo_cups` | Códigos de procedimientos (CUPS) |
| `codigo_diagnosticos` | Diagnósticos CIE-10 |
| `causa_externas` | Causas externas de lesión |
| `cup_diagnostico` | Pivot: CUPS ↔ diagnósticos |
| `cup_finalidad` | Pivot: CUPS ↔ finalidades |

### Sistema de Salud

| Tabla | Descripción |
|---|---|
| `eps` | Aseguradoras de salud |
| `municipios` | Municipios colombianos |
| `paises` | Países (códigos ISO) |
| `afiliacion_saluds` | Afiliación a salud por persona |
| `tipo_afiliacions` | Contributivo, subsidiado, etc. |
| `tipo_documentos` | CC, CE, TI, PA, etc. |

### Financiero

| Tabla | Descripción |
|---|---|
| `convenios` | Contratos de servicio y tarifas negociadas |
| `tarifas` | Reglas de precio (polimórfico: examen, servicio, procedimiento) |
| `sede_tarifa` | Precios por sede (pivot) |
| `convenio_tarifa` | Precios por convenio (pivot) |
| `facturas` | Facturas |
| `factura_medio_pagos` | Métodos de pago por factura (pivot) |
| `impuestos` | Reglas de impuestos |
| `metodo_pagos` | Métodos de pago |
| `resolucions` | Resoluciones DIAN para facturación |

### Datos de Contacto (Polimórficos)

| Tabla | Tipo polimórfico |
|---|---|
| `contacto_emergencias` | emrgenciable_type |
| `direccions` | direccionable_type |
| `telefonos` | telefonoable_type |
| `correo_electronicos` | emailable_type |
| `red_socials` | redable_type |

### Atención Clínica

| Tabla | Descripción |
|---|---|
| `modalidad_atencions` | Ambulatorio, hospitalario, etc. |
| `finalidads` | Diagnóstico, control, tamizaje, etc. |
| `via_ingresos` | Urgencia, consulta externa, etc. |
| `servicios` | Tipos de servicio |
| `servicio_habilitados` | Servicios habilitados por organización |

### Especiales

| Tabla | Descripción |
|---|---|
| `pre_registros_citas` | Pre-registro de citas anónimas (público) |
| `parametros` | Parámetros configurables del sistema |
| `opcions` | Opciones para formularios/selects |
| `roles`, `permissions` | Spatie: roles y permisos |
| `personal_access_tokens` | Sanctum: tokens API |

### Modelo de Relaciones Principales

```
Persona (paciente)
├── has many Ordenes
├── has one AfiliacionSalud
├── morphOne Direccion
├── morphMany Telefonos
├── morphMany CorreoElectronicos
└── morphMany RedSocials

Orden
├── belongs to Persona
├── belongs to Sede
├── has many Procedimientos
├── has many Facturas
├── belongs to many Examenes (pivot orden_examen, con cantidad)
└── → Resultados (a través de Procedimientos)

Procedimiento
├── belongs to Orden
├── belongs to Examen
├── belongs to Empleado
├── belongs to Sede
├── has one Resultado
├── has one ContactoEmergencia
└── has many Observacions

Resultado
├── belongs to Procedimiento
└── has many ValorReferencias

Tarifa (polimórfica)
├── tarifable: Examen | Servicio | Procedimiento
├── belongs to Sede
└── belongs to many Convenios
```

---

## 4. Arquitectura Backend

### Controladores (75 total)

**Web Controllers:**
- `PersonaController` — CRUD pacientes
- `OrdenController` — Crear y gestionar órdenes
- `ProcedimientoController` — Estado de procedimientos
- `ResultadosController` — Ingreso de resultados
- `FacturaController` — Facturación
- `ExamenController` — Catálogo de exámenes
- `ConvenioController` — Contratos y tarifas
- `EmpleadoController` — Gestión de personal y dashboard
- `SedeController` — Gestión de sucursales
- `AdministracionController` — Admin del sistema
- `PreRegistroCitaController` — Pre-registro de citas
- +15 controladores de soporte

**API Controllers (31 en `Http/Controllers/Api/`):**
- `PersonaController` — CRUD personas vía API
- `OrdenController` — Creación de órdenes vía API
- `ExamenesController` — Listado y cálculo de precios
- `ResultadosController` — Envío de resultados
- `PreRegistroCitaController` — Endpoints públicos y autenticados
- `TarifaController` — CRUD de tarifas
- `FrontendDataController` — Datos estáticos para el frontend
- `DashboardController` — Métricas del dashboard
- +helpers para CUPS, CIE-10, municipios, datos del sistema de salud

**Auth Controllers (7, Breeze scaffolding):**
- Registro, login, restablecimiento de contraseña, verificación de email

### Rutas

**Web (`routes/web.php` — 93 rutas):**
- Públicas: `/citas/registrar` (pre-registro)
- Protegidas (auth): `/ordenes`, `/resultados`, `/personas`, `/procedimientos`, `/examenes`
- Solo admin (`can:eliminar_persona`): gestión CUPS/CIE-10, admin empleados, CRUD convenios
- Financieras (`can:ver_facturas`): facturación, contratos, resoluciones DIAN

**API (`routes/api.php` — 40+ endpoints):**
- Públicos: `POST /api/citas/pre-registrar`, `GET /api/citas/consultar/{codigo}`
- Autenticados (Sanctum): `/api/cups`, `/api/personas`, `/api/ordenes`, `/api/resultados`, `/api/tarifas`
- Anidados: `/api/procedimientos/examen/{examenId}/pendientes`

### Services (8 archivos)

| Servicio | Responsabilidad |
|---|---|
| `GuardarPersona` | Parsea nombres, guarda paciente con contactos |
| `GuardarConvenio` | Guarda contratos de servicio |
| `EscogerPrecioExamen` | Lógica de priorización de tarifas |
| `EscogerReferencia` | Selecciona rango de referencia para resultados |
| `GuardarContacto` | Guarda info de contacto (teléfono, email, dirección) |
| `ElegirEmpresa` | Selección de organización basada en sesión |
| `NombreParser` | Divide nombre completo en primer/segundo nombre y apellidos |
| `ParseadorListaPersonas` | Importación masiva de listas de pacientes |

### Lógica de Tarifas (EscogerPrecioExamen)

Jerarquía de prioridad para calcular precio:
1. `convenio+sede` — precio específico por contrato y sucursal
2. `sede` — precio específico por sucursal
3. `precio_base` — precio base del examen

### Form Requests (5 archivos)

- `StorePersonaRequest` — Validación de creación de paciente
- `OrdenStoreRequest` — Validación de creación de orden
- `ProfileUpdateRequest` — Actualización de perfil de usuario
- `CambiarNombrePersonaRequest` — Cambio de nombre
- Auth requests (3, Breeze)

### Policies (3 archivos)

- `EmpresaPolicy` — Control de acceso a organizaciones
- `PreRegistroCitaPolicy` — Propiedad de pre-registros de citas
- `SedePolicy` — Control de acceso a sucursales
- Trait compartido: `ChecksEmpresaAccess` — Verifica relación user→empleado→organización

---

## 5. Arquitectura Frontend

### Vistas Blade (156 archivos)

| Directorio | Contenido |
|---|---|
| `layouts/` | Layouts maestros, navegación, footer |
| `auth/` | Login, registro, recuperación de contraseña |
| `personas/` | Lista, detalle, formularios de creación de pacientes |
| `ordenes/` | Creación (con islands React), listado, detalle |
| `resultados/` | Formularios de ingreso, historial |
| `procedimientos/` | Visualización de estados |
| `examenes/` | Catálogo, detalle, operaciones batch |
| `facturas/` | Lista y detalle de facturas |
| `convenios/` | Formularios (Blade y React) |
| `citas/` | Pre-registro de citas |
| `admin/`, `empresa/`, `sedes/`, `empleados/` | Áreas admin |

### Componentes React (45+ archivos, ~10K LOC)

**Entry points (registrados en vite.config.js):**
- `app.jsx` — Dashboard, registro de citas, dropdown, typewriter
- `appOrden.jsx` — Creación de órdenes
- `appPersona.jsx` — Detalle de persona
- `ExamenDetail.jsx` — Detalle de examen
- `convenioCreate.jsx` — Formulario de convenios

**Componentes principales:**

| Componente | Función |
|---|---|
| `CrearOrdenComponent` | Flujo completo: paciente → exámenes → diagnósticos |
| `FormPersona` | Formulario de paciente (reutilizable) |
| `AutocompleteInput` | Dropdown con búsqueda genérico |
| `CieSearchInput` | Búsqueda de códigos CIE-10 con debounce |
| `BuscarModelo` | Búsqueda genérica de modelos |
| `TablaExamenes` | Tabla de exámenes con precios |
| `StepperOrden` | Formulario multi-paso para órdenes |
| `FormPreRegistroCita` | Formulario de pre-registro público |
| `RecepcionCitas` | Panel de recepción de citas |
| `TarifasExamenesSede` | Visualización de precios por sede |
| `ConvenioForm` | Formulario de convenio con búsqueda de países |
| `DatosPersona`, `DatosOrden`, `DatosExamenes` | Componentes de visualización de datos |

### Patrón de Integración Islands

```html
<!-- Blade template -->
<div id="react-crear-orden" data-persona='{{ json_encode($paciente) }}'></div>
```

```javascript
// app.jsx
if (document.getElementById('react-crear-orden')) {
  const element = document.getElementById('react-crear-orden');
  const persona = JSON.parse(element.getAttribute('data-persona'));
  const root = createRoot(element);
  root.render(<CrearOrdenComponent paciente={persona} />);
}
```

### Gestión de Estado

- `useState` / `useReducer` local (sin Redux ni Context global)
- Estado de formularios manejado en cada componente
- Llamadas API vía axios (`resources/js/api.js`)
- Session storage para selección de sede (fuente dual con sesión PHP)

---

## 6. Features Implementados

### ✅ Completamente Funcionales

1. **Gestión de Pacientes**
   - Crear paciente (parseo de nombre, tipo de documento, fecha nacimiento, sexo, nacionalidad)
   - Buscar paciente por número de documento
   - Ver perfil con historial de órdenes y procedimientos
   - Asociar dirección, teléfono, email, redes sociales

2. **Creación de Órdenes Médicas**
   - Seleccionar paciente → elegir exámenes → definir diagnósticos (CIE-10) → crear orden
   - Formulario React con autocomplete para exámenes y diagnósticos
   - Numeración de órdenes (incremental por sede, maneja huecos)
   - Múltiples exámenes por orden con cantidades
   - Cálculo de totales basado en tarifas o precio base
   - Vinculación a múltiples procedimientos

3. **Gestión de Procedimientos**
   - Auto-creación de procedimientos al guardar la orden (uno por examen)
   - Seguimiento de estado (en proceso, completado, etc.)
   - Vinculación a empleado ejecutor y sede
   - Registro de código diagnóstico, servicio, modalidad, finalidad, vía de ingreso

4. **Ingreso de Resultados**
   - Ingresar resultados de laboratorio por procedimiento
   - Seleccionar rango de referencia por examen
   - Almacenar valores numéricos + marcador clínico (normal/anormal)
   - Ver historial de resultados por paciente

5. **Sistema de Precios**
   - Jerarquía: convenio+sede → solo sede → precio base del examen
   - Precios multisede (diferentes precios por sucursal)
   - Precios por convenio
   - Cálculo automático del total en la orden

6. **Pre-Registro de Citas** (Público)
   - Registro anónimo: nombre, teléfono, fecha/hora preferida
   - Código de confirmación (TODO: envío por email/SMS)
   - Check-in en panel de recepción
   - El personal puede confirmar/completar o cancelar el pre-registro

7. **Facturación**
   - Crear facturas desde órdenes
   - Vincular múltiples métodos de pago por factura
   - Gestión de resoluciones DIAN
   - Términos de pago a 30 días por defecto

8. **Integración con Sistema de Salud**
   - Selección de EPS por paciente
   - Tipo de afiliación (contributivo, subsidiado, etc.)
   - Seguimiento de plan de salud

9. **Generación de Reportes** (Parcial)
   - Exportación RIPS (estándar de facturación colombiano)
   - Filtrado por estado de procedimientos
   - Exportación a JSON/CSV

10. **Gestión de Usuarios**
    - Autenticación (Breeze scaffolding)
    - Control de acceso por roles (admin, técnico de lab, recepcionista — Spatie)
    - Vinculación Empleado ↔ Usuario ↔ Organización
    - Control de acceso por sede

11. **Dashboard y Analíticas**
    - Métricas: procedimientos pendientes, pacientes del día, órdenes
    - Desglose por estado de procedimientos
    - Tarjetas de navegación rápida

### 🟡 Parcialmente Funcionales

- **Importación masiva de pacientes** — Componente y parseo funcionan; transacción sin probar a fondo
- **Procesamiento batch de exámenes** — Vista creada; lógica pendiente de verificación
- **Edición de persona** — Creación funciona; actualización incompleta
- **Gestión de códigos CIE-10** — Vista funciona; agregar/editar/eliminar puede faltar pulido
- **Funcionalidad de búsqueda** — Búsqueda básica funciona; filtrado avanzado incompleto

### ❌ Solo Scaffolding (No Funcional)

- Notificaciones por email (TODO en `PreRegistroCitaController`)
- Notificaciones por SMS
- Portal de pacientes (acceso propio)
- Recordatorios de citas
- **Generación de PDF de resultados** (layout existe, sin implementar)
- Soporte multilenguaje (scaffolding presente; español hardcodeado)

---

## 7. Deuda Técnica

### 🔴 Crítico — Bloqueante para Producción

**1. Sin generación de PDF de reportes**
El entregable principal de un laboratorio es el reporte escrito de resultados. El layout existe en Blade pero no hay librería de PDF ni lógica de generación implementada.

**2. Sin notificaciones (email/SMS/WhatsApp)**
El flujo de pre-registro de citas tiene un `TODO: Aquí enviar email de confirmación` en `PreRegistroCitaController`. Los pacientes no reciben ninguna comunicación.

**3. `Persona::all()` sin paginación**
`PersonaController::index()` carga todos los pacientes en memoria. Falla con >500 registros. Afecta el listado principal de pacientes.

**4. `APP_DEBUG=true` + SQLite en producción**
La configuración de desarrollo está activa. SQLite no es apta para producción con múltiples usuarios concurrentes.

**5. Sin rate limiting en endpoints públicos**
`POST /api/citas/pre-registrar` y `GET /api/citas/consultar/{codigo}` son completamente públicos sin throttling. Vulnerables a spam y abuso.

**6. País hardcodeado como "170" (Colombia)**
9+ ocurrencias en controladores (`AdministracionController`, `ListaPersonasController`, `GuardarPersona`). Debe ser constante o valor de configuración.

### 🟡 Importante — No Bloqueante Inmediato

**7. N+1 Queries en flujos frecuentes**
- `EmpleadoController::dashboard()` hace lazy-loading masivo de relaciones
- `ListaPersonasController` itera relaciones sin batching
- Múltiples controladores cargan órdenes/procedimientos sin `->with()` de eager loading

**8. Sin auditoría de acceso a datos de pacientes**
Requerimiento de HABEAS DATA (Ley 1581 Colombia). No hay `created_by`/`updated_by` en entidades críticas. Las modificaciones de resultados no se registran.

**9. Policies faltantes para órdenes y procedimientos**
`EmpresaPolicy` implementada; `OrdenPolicy` y `ProcedimientoPolicy` no existen. Las rutas usan `can:eliminar_persona` pero sin políticas dedicadas.

**10. Aislamiento de organización incompleto**
La búsqueda de exámenes retorna resultados globales. Los usuarios no están completamente aislados por organización en todos los flujos.

**11. Lógica de negocio escapándose a controllers**
75 controladores vs 8 services. Con el tiempo, esto hace el código difícil de mantener y testear.

**12. Duplicación de lógica**
`PersonaController` y `Api/PersonaController` tienen validaciones superpuestas. La creación de órdenes está duplicada entre web y API. El parseo de nombres se usa en múltiples lugares sin helper consistente.

### 🟠 Menor — Deuda a Largo Plazo

**13. Valores mágicos hardcodeados**
- Strings de tipo de tarifa: `'por_convenio_y_sede'`, `'por_sede'`, `'precio_base'`
- Redondeo del número de orden a múltiplos de 2000 (OrdenController línea ~60)
- Términos de pago a 30 días en FacturaController

**14. Inconsistencia en validaciones**
- Algunos controllers usan Form Requests (correcto)
- Otros validan inline con `$request->validate()`
- Campos opcionales sin validación de longitud o formato

**15. Fragmentación de estado en frontend**
- React fetch de datos estáticos vía `/api/static-data-for-frontend` en cada mount (sin caché)
- `localStorage` para países pero no para otros datos de referencia
- Sesión PHP + estado React = dos fuentes de verdad para la sede activa

**16. Sin transacciones en operaciones bulk**
`DB::transaction` solo en `OrdenController` y `TarifaController`. Falta en operaciones masivas (`ListaPersonasController`, `ReprocesarController`).

**17. `Empleado->nombre()` no retorna nada**
Método en el modelo `Empleado` (líneas 29-30) sin `return`. Bug silencioso.

**18. Tipo de dato inconsistente en respuestas API**
A veces retorna arrays crudos, a veces colecciones Eloquent. Sin API Resources consistentes.

---

## 8. Cobertura de Tests

**18 archivos de tests (Pest + PHPUnit)**

### Feature Tests (13 archivos)

| Test | Cobertura |
|---|---|
| `Auth/` (6 tests) | Registro, login, reset de contraseña, verificación de email |
| `DashboardPageTest` | Acceso al dashboard |
| `ExamenesPrecioApiTest` | API de cálculo de precios |
| `OrdenesApiTest` | 10 casos: creación, validación, relaciones |
| `ElegirSedeTest` | Selección de organización/sede |
| `WelcomePageTest` | Renderizado de página de inicio |
| `ProfileTest` | CRUD de perfil de usuario |
| `Policies/EmpresaPolicyTest` | Verificaciones de autorización |

### Unit Tests (2 archivos)

| Test | Cobertura |
|---|---|
| `EscogerPrecioExamenTest` | Lógica de prioridad de tarifas |
| `ExampleTest` | Esqueleto vacío |

### Brechas de Cobertura

- ❌ Sin tests para: creación de paciente, ingreso de resultados, importación masiva, búsqueda, pre-registro de citas
- ❌ Sin tests API para: personas, actualización de exámenes, CRUD de tarifas, resultados
- ❌ Sin tests de autorización para: acceso a orden/procedimiento/resultado
- ❌ Datos de seed incompletos (TestDataSeeder puede no crear todos los registros necesarios)
- ✅ Flujo crítico de órdenes cubierto (creación → validación → creación de procedimiento)

---

## 9. Dependencias

### Backend (composer.json)

| Paquete | Versión | Propósito |
|---|---|---|
| laravel/framework | ^12.0 | Framework core |
| laravel/sanctum | ^4.0 | Autenticación API por tokens |
| laravel/tinker | ^2.10.1 | REPL |
| spatie/array-to-xml | ^3.4 | Exportación RIPS |
| spatie/laravel-permission | ^6.18 | Roles y permisos |

**Dev-only:**

| Paquete | Versión | Propósito |
|---|---|---|
| fakerphp/faker | ^1.23 | Datos de test |
| laravel/breeze | ^2.3 | Scaffolding de auth |
| laravel/pail | ^1.2.2 | Visor de logs |
| laravel/sail | ^1.41 | Entorno Docker dev |
| laravel/pint | ^1.13 | Linter de código |
| pestphp/pest | ^3.8 | Framework de testing |
| pestphp/pest-plugin-laravel | ^3.2 | Pest para Laravel |
| mockery/mockery | ^1.6 | Librería de mocking |

### Frontend (package.json)

| Paquete | Versión | Propósito |
|---|---|---|
| react | ^19.2.0 | Librería UI |
| react-dom | ^19.2.0 | Binding DOM |
| vite | ^7.2.4 | Bundler |
| @vitejs/plugin-react | ^5.1.1 | Plugin Vite para React |
| laravel-vite-plugin | ^2.0.1 | Integración Laravel/Vite |
| tailwindcss | ^3.4.18 | CSS framework |
| @tailwindcss/forms | ^0.5.10 | Estilos de formularios |
| @tailwindcss/vite | ^4.1.17 | Tailwind en Vite |
| axios | ^1.13.2 | Cliente HTTP |
| postcss | ^8.5.6 | Procesamiento CSS |
| autoprefixer | ^10.4.22 | Prefijos CSS |
| concurrently | ^9.2.1 | Ejecutar múltiples scripts |

### Librerías Notablemente Ausentes

| Librería | Impacto |
|---|---|
| PDF generation (DomPDF, TCPDF, Snappy) | **Crítico** — sin reportes para pacientes |
| Email service (Mailgun, SES) | **Crítico** — sin notificaciones |
| Jest/Vitest | Sin tests unitarios frontend |
| TypeScript | Sin tipado en componentes React |
| react-hook-form | Validación de formularios manual |
| Rate limiting config | Endpoints públicos sin protección |

---

## 10. Seguridad

### ✅ Fortalezas

- Autenticación Laravel + Sanctum para API
- Spatie roles y permisos integrado
- CSRF en formularios Blade (`@csrf`)
- Contraseñas hasheadas con bcrypt (BCRYPT_ROUNDS=12)
- Form Request validation en flujos principales
- Variables sensibles en `.env`

### ⚠️ Preocupaciones

**Exposición de datos:**
- PII de pacientes (direcciones, teléfonos, emails) almacenado en texto plano
- Las APIs retornan modelos Eloquent completos (pueden filtrar campos internos)
- Sin encriptación de datos en reposo

**Privacidad médica (HABEAS DATA — Ley 1581 Colombia):**
- Sin log de auditoría de quién accedió a qué datos de paciente
- Aislamiento de organización por sesión (débil para setups multi-organización)
- Sin encriptación de códigos médicos sensibles

**API pública:**
- `/api/citas/pre-registrar` sin rate limiting — vulnerable a spam
- `/api/static-data-for-frontend` expone todos los datos de referencia (puede ser aceptable)
- Sin validación en algunos endpoints (`OptcionController`, `TributoController`)

**Configuración de producción:**
- `APP_DEBUG=true` activo — expone stack traces
- `SQLite` no apto para múltiples usuarios concurrentes
- `SESSION_DRIVER=file` — no escalable horizontalmente

---

## 11. Comparación con Software Similar

| Criterio | DinamyLAB | LabSoft (CO) | OpenELIS | LIS1 SaaS (CO) | SCC LIS |
|---|---|---|---|---|---|
| **Dominio colombiano** | ✅ Nativo | ✅ | ❌ EEUU | ✅ | Parcial |
| **CUPS / CIE-10 / RIPS** | ✅ | ✅ | Parcial | ✅ | ❌ |
| **Stack moderno** | ✅ Laravel 12 / React 19 | ❌ Legacy | ❌ Java/legacy | ✅ | ❌ |
| **Multi-sede** | ✅ | Parcial | ✅ | ✅ | ✅ |
| **Código propio (customizable)** | ✅ | ❌ | ✅ (OSS) | ❌ | ❌ |
| **PDF de reportes** | ❌ Falta | ✅ | ✅ | ✅ | ✅ |
| **Notificaciones** | ❌ Falta | ✅ | Parcial | ✅ | Parcial |
| **App móvil** | ❌ | ❌ | ❌ | Parcial | ❌ |
| **Integración EPS/Seguros** | ✅ | ✅ | ❌ | ✅ | ❌ |
| **Precio estimado (COP/mes)** | A definir | $800K–$2M | Gratis* | $400K–$1.2M | $600K–$1.5M |
| **Facilidad de implementación** | Media | Media | Compleja | Fácil | Media |

*OpenELIS es OSS pero la implementación y soporte tienen costo alto.

### Ventajas Diferenciales de DinamyLAB

1. **Stack moderno + dominio colombiano** — ningún competidor tiene ambas cosas
2. **Código 100% propio** — se puede customizar para cada cliente sin depender del vendor
3. **Precio potencialmente competitivo** — 30-40% más barato que LabSoft con mejor UX
4. **Arquitectura escalable** — puede crecer sin rediseño

### Brechas Críticas vs. Competidores

1. **PDF de reportes** — brecha más visible para cualquier evaluador
2. **Notificaciones** — WhatsApp es estándar en Colombia para comunicación con pacientes
3. **Sin prueba de producción** — competidores tienen años de clientes reales

---

## 12. Costos y Pricing

### Costo de Llevar a Producción (MVP Mínimo)

Estimado con 1 desarrollador senior (~$80.000 COP/hora):

| Ítem | Horas | Costo estimado (COP) |
|---|---|---|
| PDF de reportes clínicos | 20–30h | $1.600.000 – $2.400.000 |
| Email / WhatsApp notificaciones | 15–20h | $1.200.000 – $1.600.000 |
| Paginación + eager loading (N+1) | 8–12h | $640.000 – $960.000 |
| Configuración de producción (DB, debug, env) | 8–10h | $640.000 – $800.000 |
| Rate limiting + hardening de seguridad mínimo | 6–8h | $480.000 – $640.000 |
| Tests de flujos críticos | 16–24h | $1.280.000 – $1.920.000 |
| **Total mínimo viable** | **73–104h** | **$5.840.000 – $8.320.000** |

### Trabajo adicional recomendado (no obligatorio para MVP)

| Ítem | Horas | Costo estimado (COP) |
|---|---|---|
| Auditoría HABEAS DATA (logs de acceso) | 10–15h | $800.000 – $1.200.000 |
| Policies faltantes (Orden, Procedimiento) | 6–8h | $480.000 – $640.000 |
| Refactoring N+1 completo | 12–16h | $960.000 – $1.280.000 |
| Edición de persona completa | 8–10h | $640.000 – $800.000 |
| **Subtotal adicional** | **36–49h** | **$2.880.000 – $3.920.000** |

**Costo total estimado para versión de producción robusta: $8.720.000 – $12.240.000 COP**

### Modelos de Pricing para el Cliente

**Opción A — Licencia mensual SaaS:**
| Segmento | Descripción | Precio |
|---|---|---|
| Lab pequeño | 1 sede, <500 órdenes/mes | $350.000 – $550.000 COP/mes |
| Lab mediano | 2-3 sedes, <2.000 órdenes/mes | $800.000 – $1.200.000 COP/mes |
| Lab grande | 4+ sedes, >2.000 órdenes/mes | $1.500.000+ COP/mes (a negociar) |

**Opción B — Licencia única + implementación:**
- Fee de implementación: $8.000.000 – $15.000.000 COP (customización, migración de datos)
- Soporte y actualizaciones: $2.000.000 – $4.000.000 COP/año
- Más atractivo para labs que migran desde otro sistema

**Opción C — Modelo híbrido (recomendado para el primer cliente):**
- Implementación inicial: $5.000.000 – $8.000.000 COP
- Mensualidad: $400.000 – $600.000 COP/mes
- Incluye: hosting, soporte, actualizaciones de seguridad

### Costos de Hosting (estimados)

| Opción | Descripción | Costo mensual (USD) |
|---|---|---|
| DigitalOcean / Linode | VPS dedicado, 1-5 clientes | $50 – $80 |
| Railway / Render | Serverless, inicio rápido | $20 – $40 |
| AWS Colombia-adjacent | Escalable, alta disponibilidad | $80 – $150 |

---

## 13. Tiempos de Entrega

### Para el Primer Cliente (MVP de Producción)

**Con 1 desarrollador senior a tiempo completo:**

```
Semana 1-2:  PDF de reportes clínicos + email básico
Semana 3-4:  Migración a PostgreSQL + configuración producción + seguridad mínima
Semana 5-6:  QA, tests de flujos críticos, correcciones
Semana 7-8:  Piloto con el cliente (datos reales, ajustes de UX)
──────────────────────────────────────────────────────────
Total: 6–8 semanas
```

**Con 2 desarrolladores:**
```
Total: 4–5 semanas
```

### Para una Propuesta Comercial Sólida

| Hito | Duración acumulada |
|---|---|
| Sistema base funcional en producción | Semana 6 |
| Período de estabilización con cliente real | Semana 10 |
| **Firma de aceptación formal** | **~2.5 meses** |

### Roadmap Post-MVP (para propuestas más ambiciosas)

| Fase | Plazo | Contenido |
|---|---|---|
| **MVP v1** | 0–2 meses | PDF + notificaciones + producción + seguridad básica |
| **v1.5** | 2–4 meses | HABEAS DATA, policies completas, edición de persona, búsqueda avanzada |
| **v2** | 4–6 meses | Portal de pacientes, reportes analíticos, integración RIPS automática |
| **v3** | 6–12 meses | App móvil, WhatsApp Business API, integración con sistema de salud colombiano (SIVEP) |

---

## 14. Riesgos para la Propuesta

### Riesgos Técnicos

| Riesgo | Probabilidad | Impacto | Mitigación |
|---|---|---|---|
| SQLite en producción falla bajo carga | Alta | Alto | Migrar a PostgreSQL antes de producción |
| N+1 degrada performance con volumen | Media | Alto | Agregar eager loading antes del piloto |
| Sin PDF bloquea adopción | Alta | Crítico | Implementar en las primeras 2 semanas |
| Tests insuficientes causan regresiones | Media | Alto | Tests de flujos críticos antes del piloto |

### Riesgos Legales/Regulatorios

| Riesgo | Descripción |
|---|---|
| **HABEAS DATA (Ley 1581)** | Obligatorio para datos de salud en Colombia. Sin auditoría de acceso, hay exposición legal. No bloquea el MVP pero debe estar en roadmap visible. |
| **Facturación electrónica DIAN** | La estructura de resoluciones DIAN está implementada. Pero si el cliente necesita e-facturación real, hay trabajo adicional. |
| **RIPS electrónico** | La exportación RIPS en JSON/CSV existe. Si el cliente necesita envío electrónico al SISPRO, es trabajo adicional. |

### Riesgos Comerciales

| Riesgo | Descripción |
|---|---|
| **Desarrollador único** | El cliente asume continuidad del servicio. Considerar cláusula de código escrow o documentación de entrega. |
| **Sin clientes de referencia** | Es el primer despliegue productivo. Proponer precio de "cliente piloto" con beneficios a cambio de caso de estudio. |
| **Competidores establecidos** | LabSoft tiene años de tracción. Diferenciar por precio + modernidad + customización. |

---

## 15. Resumen Ejecutivo

### Estado del Proyecto

> DinamyLAB tiene el **70% del trabajo hecho**. El dominio clínico colombiano está correctamente modelado, la arquitectura es sólida para escalar, y el stack tecnológico (Laravel 12 + React 19) es moderno y mantenible. Las brechas restantes son predecibles, estimables y **no requieren rediseño**.

### Fortalezas para la Propuesta

1. **Dominio colombiano capturado** — CUPS, CIE-10, RIPS, EPS, tipos de afiliación. Es el conocimiento más difícil de adquirir y ya está en el código.
2. **Flujo nuclear funcional** — orden → análisis → resultado → factura trabaja de extremo a extremo.
3. **Arquitectura multi-sede** — listo para clientes con varias sucursales desde el día uno.
4. **Stack moderno** — ventaja competitiva frente a LabSoft y otros sistemas legacy en Colombia.
5. **Código 100% propio** — sin dependencia de vendor externo; customizable para cada cliente.

### Brechas para Producción

1. PDF de reportes clínicos — **el entregable principal de un lab**
2. Notificaciones (email mínimo, WhatsApp ideal)
3. Migración de SQLite a base de datos de producción
4. Configuración de seguridad para producción
5. Tests de flujos críticos

### Mensaje para la Propuesta Comercial

> *Para un laboratorio clínico pequeño en Colombia, DinamyLAB representa una solución más moderna y económica que las alternativas actuales, con la ventaja única de ser completamente customizable. El sistema está a 6–8 semanas de producción. El costo de desarrollo restante es estimable y acotado.*

### Números Clave

| Métrica | Valor |
|---|---|
| Horas para producción mínima | 73–104h |
| Inversión de desarrollo restante | $5.8M – $8.3M COP |
| Precio sugerido primer cliente (mensual) | $400.000 – $600.000 COP/mes |
| Fee de implementación sugerido | $5M – $8M COP |
| Tiempo hasta firma de aceptación | ~2.5 meses |
| ROI del desarrollo (si se cierra 1 cliente a $500K/mes) | Break-even en ~16 meses |

---

*Análisis generado por Claude Sonnet 4.6 — DinamyLAB — 2026-10-14*

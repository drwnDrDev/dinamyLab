# Sistema de Pre-registro de Citas

Sistema completo de pre-registro de citas médicas con flujo de trabajo en dos etapas: pre-registro público y confirmación en recepción.

## 📋 Descripción General

Este sistema permite que usuarios finales (sin conocimientos médicos/técnicos) puedan pre-registrar citas de manera simple, proporcionando solo datos básicos. Posteriormente, el personal capacitado de recepción verifica los datos, completa la información faltante y crea el registro formal de la persona.

### Problema que resuelve

Los usuarios finales:
- No saben qué datos son importantes para el sistema médico
- No tienen toda la información a mano (EPS, tipo de afiliación, etc.)
- Solo necesitan "apartar" una cita con datos básicos

El sistema permite este flujo casual mientras mantiene la calidad de datos al delegar la completitud al personal capacitado.

## 🏗️ Arquitectura

### Flujo de trabajo

```
USUARIO FINAL (Sin autenticación)
    ↓
Pre-registro simple (nombres, opcional: documento, teléfono)
    ↓
Código de confirmación generado
    ↓
[Usuario guarda código]
    ↓
RECEPCIÓN (Con autenticación)
    ↓
Buscar pre-registro (por código o documento)
    ↓
Verificar datos con paciente
    ↓
Completar FormPersona con todos los datos requeridos
    ↓
Registro formal creado + Estado: confirmado
```

## 📁 Estructura de archivos

### Backend (Laravel)

```
app/
├── Models/
│   └── PreRegistroCita.php          # Modelo de pre-registro
├── Http/Controllers/Api/
│   └── PreRegistroCitaController.php # 7 endpoints para gestión
└── Services/
    └── ParseadorListaPersonas.php    # Parser para listas múltiples

database/migrations/
└── 2026_01_13_000001_create_pre_registros_citas_table.php

routes/
└── api.php  # Rutas públicas + autenticadas
```

### Frontend (React)

```
resources/js/components/
├── FormPreRegistroCita.jsx           # Pre-registro individual (público)
├── PreRegistroListaCitas.jsx         # Pre-registro múltiple (público)
├── ConsultarCita.jsx                 # Consulta de estado (público)
├── RecepcionPreRegistros.jsx         # Lista de pendientes (recepción)
├── ConfirmarPreRegistro.jsx          # Confirmar y completar (recepción)
├── RecepcionCitas.jsx                # Integrador de recepción
└── EjemploSistemaCitas.jsx           # Demo completa del sistema
```

## 🗄️ Base de datos

### Tabla: `pre_registros_citas`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID primario |
| nombres_completos | string | Nombre como lo escribió el usuario |
| numero_documento | string(nullable) | Documento si lo proporcionó |
| telefono_contacto | string(nullable) | Teléfono de contacto |
| email | string(nullable) | Email de contacto |
| fecha_deseada | date(nullable) | Fecha que prefiere |
| hora_deseada | time(nullable) | Hora que prefiere |
| motivo | text(nullable) | Razón de la cita |
| estado | enum | pendiente/confirmado/cancelado/atendido |
| codigo_confirmacion | string(unique) | Código único de 8 caracteres |
| datos_parseados | json(nullable) | Nombres parseados automáticamente |
| persona_id | bigint(nullable) | FK a personas (tras confirmar) |
| orden_id | bigint(nullable) | FK a órdenes (si se genera) |
| confirmado_por | bigint(nullable) | FK al usuario que confirmó |
| timestamps | - | created_at, updated_at |
| deleted_at | timestamp(nullable) | Soft delete |

### Índices

- `codigo_confirmacion` (unique)
- `numero_documento`
- `estado`
- `fecha_deseada`

## 🔌 API Endpoints

### Públicos (sin autenticación)

#### POST `/api/citas/pre-registrar`
Pre-registra una sola cita.

**Request:**
```json
{
  "nombres_completos": "Carlos Ramirez",
  "numero_documento": "1012555321",
  "telefono_contacto": "3001234567",
  "email": "carlos@example.com",
  "fecha_deseada": "2024-02-15",
  "hora_deseada": "10:00",
  "motivo": "Consulta general"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Pre-registro exitoso",
  "data": {
    "id": 1,
    "nombres_completos": "Carlos Ramirez",
    "codigo_confirmacion": "ABC12345",
    "estado": "pendiente",
    ...
  }
}
```

#### POST `/api/citas/pre-registrar-lista`
Pre-registra múltiples personas desde una lista.

**Request:**
```json
{
  "contenido": "Carlos Ramirez, 1012555321\nLuiz Alberto Diaz, 10101010\nJuan Perez",
  "fecha_deseada": "2024-02-15",
  "motivo": "Exámenes de laboratorio"
}
```

**Response:**
```json
{
  "success": true,
  "message": "3 personas pre-registradas",
  "data": [
    {
      "id": 1,
      "nombres_completos": "Carlos Ramirez",
      "numero_documento": "1012555321",
      "codigo_confirmacion": "ABC12345",
      "datos_parseados": {
        "primer_nombre": "Carlos",
        "primer_apellido": "Ramirez"
      }
    },
    ...
  ]
}
```

#### GET `/api/citas/consultar/{codigo_o_documento}`
Consulta el estado de pre-registros.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "nombres_completos": "Carlos Ramirez",
      "codigo_confirmacion": "ABC12345",
      "estado": "pendiente",
      "fecha_deseada": "2024-02-15",
      ...
    }
  ]
}
```

### Autenticados (recepción)

#### GET `/api/recepcion/pre-registros/pendientes`
Lista pre-registros pendientes.

**Query params:**
- `estado`: filtrar por estado (opcional)
- `fecha`: filtrar por fecha_deseada (opcional)

#### PUT `/api/recepcion/pre-registros/{id}/confirmar`
Confirma un pre-registro y crea el registro formal.

**Request:**
```json
{
  "datos_persona": {
    "primer_nombre": "Carlos",
    "segundo_nombre": "",
    "primer_apellido": "Ramirez",
    "segundo_apellido": "",
    "tipo_documento_id": 1,
    "numero_documento": "1012555321",
    "fecha_nacimiento": "1990-05-15",
    "sexo": "M",
    "tipo_edad_id": 1,
    "edad": 34,
    ...
  }
}
```

**Response:**
```json
{
  "success": true,
  "message": "Pre-registro confirmado y persona creada",
  "data": {
    "id": 1,
    "codigo_confirmacion": "ABC12345",
    "estado": "confirmado",
    "persona_id": 123,
    "confirmado_por": 5
  }
}
```

#### PUT `/api/recepcion/pre-registros/{id}/cancelar`
Cancela un pre-registro.

#### GET `/api/recepcion/pre-registros/buscar`
Busca pre-registros por nombre, documento o código.

**Query params:**
- `q`: término de búsqueda

## 🎨 Componentes Frontend

### 1. FormPreRegistroCita (Público)

Pre-registro individual. Campos mínimos.

**Props:**
- `onSuccess`: callback al completar (opcional)

**Uso:**
```jsx
import FormPreRegistroCita from './components/FormPreRegistroCita';

<FormPreRegistroCita 
  onSuccess={(data) => console.log('Código:', data.data.codigo_confirmacion)}
/>
```

### 2. PreRegistroListaCitas (Público)

Pre-registro múltiple desde lista de texto.

**Props:**
- `onSuccess`: callback al completar (opcional)

**Uso:**
```jsx
import PreRegistroListaCitas from './components/PreRegistroListaCitas';

<PreRegistroListaCitas 
  onSuccess={(data) => console.log('Registrados:', data.data.length)}
/>
```

### 3. ConsultarCita (Público)

Consulta de estado por código o documento.

**Uso:**
```jsx
import ConsultarCita from './components/ConsultarCita';

<ConsultarCita />
```

### 4. RecepcionCitas (Autenticado)

Interfaz completa de recepción. Integra lista de pendientes y confirmación.

**Props:**
- `FormPersona` (requerido): Componente de formulario para registro completo

**Uso:**
```jsx
import RecepcionCitas from './components/RecepcionCitas';
import FormPersona from './components/FormPersona'; // Tu componente existente

<RecepcionCitas FormPersona={FormPersona} />
```

### 5. EjemploSistemaCitas (Demo)

Componente de demostración que muestra todo el sistema.

**Props:**
- `FormPersona` (requerido)
- `esRecepcion`: boolean (default: false)

**Uso:**
```jsx
import EjemploSistemaCitas from './components/EjemploSistemaCitas';
import FormPersona from './components/FormPersona';

// Vista pública
<EjemploSistemaCitas FormPersona={FormPersona} />

// Vista de recepción
<EjemploSistemaCitas FormPersona={FormPersona} esRecepcion={true} />
```

## 🚀 Instalación

### 1. Ejecutar migración

```bash
php artisan migrate
```

### 2. Configurar rutas

Las rutas ya están definidas en `routes/api.php`. Asegúrate de que el middleware de autenticación esté configurado correctamente.

### 3. Registrar componentes

En tu archivo de entrada JS (ej. `app.js`):

```javascript
import FormPreRegistroCita from './components/FormPreRegistroCita';
import PreRegistroListaCitas from './components/PreRegistroListaCitas';
import ConsultarCita from './components/ConsultarCita';
import RecepcionCitas from './components/RecepcionCitas';
import EjemploSistemaCitas from './components/EjemploSistemaCitas';

// Exportar o usar según tu configuración
```

### 4. CSRF Token

Asegúrate de configurar Axios con el token CSRF:

```javascript
import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// En Blade, incluye:
<meta name="csrf-token" content="{{ csrf_token() }}">
```

## 🎯 Casos de uso

### Usuario final pre-registra familia

```jsx
// Página pública de pre-registro
<PreRegistroListaCitas 
  onSuccess={(data) => {
    // data.data contiene array con los códigos
    alert(`Registrados: ${data.data.length} personas`);
  }}
/>
```

Usuario ingresa:
```
Carlos Ramirez, 1012555321
Zonia Fierro, 10101010
Juan Perez
```

Recibe 3 códigos de confirmación únicos.

### Recepción confirma pre-registro

```jsx
// Página de recepción (autenticada)
import FormPersona from './FormPersona'; // Tu componente existente
import RecepcionCitas from './components/RecepcionCitas';

<RecepcionCitas FormPersona={FormPersona} />
```

Flujo:
1. Busca por código o documento
2. Ve los datos básicos pre-registrados
3. Confirma y completa el FormPersona
4. Sistema crea registro formal en tabla `personas`
5. Actualiza estado a "confirmado"

### Usuario consulta estado

```jsx
// Página de consulta pública
<ConsultarCita />
```

Usuario ingresa código `ABC12345` o documento `1012555321` y ve:
- Estado actual (pendiente/confirmado/etc.)
- Datos registrados
- Instrucciones según estado

## 🔐 Seguridad

### Endpoints públicos
- No requieren autenticación
- Rate limiting recomendado
- Validación de entrada

### Endpoints de recepción
- Requieren autenticación (Sanctum)
- Solo accesible por personal autorizado
- Auditoría de cambios (columna `confirmado_por`)

## 🧪 Testing

### Probar pre-registro individual

```bash
curl -X POST http://localhost/api/citas/pre-registrar \
  -H "Content-Type: application/json" \
  -d '{
    "nombres_completos": "Juan Test",
    "numero_documento": "123456789"
  }'
```

### Probar pre-registro múltiple

```bash
curl -X POST http://localhost/api/citas/pre-registrar-lista \
  -H "Content-Type: application/json" \
  -d '{
    "contenido": "Juan Perez, 111\nMaria Lopez, 222",
    "fecha_deseada": "2024-02-15"
  }'
```

### Probar consulta

```bash
curl http://localhost/api/citas/consultar/ABC12345
```

## 📊 Estados del pre-registro

| Estado | Descripción |
|--------|-------------|
| `pendiente` | Recién creado, esperando confirmación en recepción |
| `confirmado` | Verificado y completado por recepción, persona creada |
| `cancelado` | Cancelado por recepción o usuario |
| `atendido` | Paciente fue atendido (opcional, para tracking) |

## 🔄 Integración con sistema existente

### ParseadorListaPersonas

El sistema reutiliza el servicio `ParseadorListaPersonas` existente para analizar nombres automáticamente:

```php
// En PreRegistroCitaController
use App\Services\ParseadorListaPersonas;

$parseador = new ParseadorListaPersonas();
$personasParseadas = $parseador->parsear($request->contenido);

foreach ($personasParseadas as $persona) {
    PreRegistroCita::create([
        'nombres_completos' => $persona['nombres_completos'],
        'numero_documento' => $persona['numero_documento'],
        'datos_parseados' => $persona, // Guarda análisis automático
        ...
    ]);
}
```

### GuardarPersona Service

Al confirmar en recepción, usa el servicio existente:

```php
use App\Services\GuardarPersona;

$persona = (new GuardarPersona())->ejecutar($datosPersona);

$preRegistro->update([
    'estado' => 'confirmado',
    'persona_id' => $persona->id,
    'confirmado_por' => auth()->id()
]);
```

## 🎨 Personalización

### Campos adicionales

Para agregar campos al pre-registro:

1. **Migración**: agregar columna
```php
$table->string('campo_nuevo')->nullable();
```

2. **Modelo**: agregar a `$fillable`
```php
protected $fillable = [..., 'campo_nuevo'];
```

3. **Controlador**: validar y guardar
```php
$validated = $request->validate([
    ...
    'campo_nuevo' => 'nullable|string'
]);
```

4. **Componente**: agregar input
```jsx
<input name="campo_nuevo" ... />
```

### Personalizar códigos

En `PreRegistroCita::generarCodigoConfirmacion()`:

```php
public static function generarCodigoConfirmacion()
{
    do {
        // Personaliza formato: 8 caracteres alfanuméricos
        $codigo = strtoupper(Str::random(8));
    } while (self::where('codigo_confirmacion', $codigo)->exists());
    
    return $codigo;
}
```

## 📝 Notas importantes

1. **Soft Deletes**: Los pre-registros usan soft deletes para mantener historial
2. **Datos parseados**: Se guardan como JSON para referencia, pero no son el dato definitivo
3. **FormPersona**: Debe ser tu componente existente para registro completo de personas
4. **Validaciones**: Ajusta según tus necesidades de negocio
5. **Notificaciones**: Considera enviar SMS/email con código de confirmación

## 🐛 Troubleshooting

### No se crean registros

- Verifica que la migración se ejecutó
- Revisa logs: `storage/logs/laravel.log`
- Verifica CSRF token en peticiones

### Errores 401 en recepción

- Verifica que el usuario está autenticado
- Revisa middleware de rutas
- Verifica token Sanctum

### FormPersona no se muestra

- Asegúrate de pasar el componente como prop
- Verifica que FormPersona acepta props: `datosIniciales`, `onSubmit`, `textoBoton`, `loading`

## 📚 Recursos adicionales

- Documentación original del parser: Ver archivo `INSTALACION.md` (módulo anterior)
- Laravel Sanctum: https://laravel.com/docs/sanctum
- React Hooks: https://react.dev/reference/react

## 🤝 Contribuir

Para mejorar el sistema:

1. Agrega validaciones según tu caso de uso
2. Implementa notificaciones (SMS/Email)
3. Agrega dashboard de estadísticas
4. Implementa sistema de prioridades
5. Agrega exportación de reportes

---

**Versión**: 2.0  
**Fecha**: Enero 2024  
**Mantenedor**: [Tu nombre/equipo]

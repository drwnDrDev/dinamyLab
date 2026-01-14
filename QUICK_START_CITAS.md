# Guía Rápida de Integración - Sistema de Citas

## ⚡ Setup en 5 minutos

### 1. Ejecutar migración
```bash
php artisan migrate
```

### 2. Verificar rutas
Las rutas ya están en `routes/api.php`. Solo asegúrate de que el middleware de autenticación funcione.

### 3. Usar en tu aplicación

#### Opción A: Demo completa (recomendado para testing)

```jsx
// En tu archivo de rutas React o página principal
import EjemploSistemaCitas from './components/EjemploSistemaCitas';
import FormPersona from './components/FormPersona'; // TU componente existente

// Vista pública (usuarios finales)
function PaginaPublica() {
    return <EjemploSistemaCitas FormPersona={FormPersona} />;
}

// Vista de recepción (personal autenticado)
function PaginaRecepcion() {
    return <EjemploSistemaCitas FormPersona={FormPersona} esRecepcion={true} />;
}
```

#### Opción B: Componentes individuales

```jsx
// Página pública - Pre-registro individual
import FormPreRegistroCita from './components/FormPreRegistroCita';

function PreRegistro() {
    return (
        <FormPreRegistroCita 
            onSuccess={(data) => {
                console.log('Código generado:', data.data.codigo_confirmacion);
                alert('¡Registro exitoso! Tu código es: ' + data.data.codigo_confirmacion);
            }}
        />
    );
}

// Página pública - Pre-registro múltiple
import PreRegistroListaCitas from './components/PreRegistroListaCitas';

function PreRegistroGrupal() {
    return <PreRegistroListaCitas />;
}

// Página pública - Consultar estado
import ConsultarCita from './components/ConsultarCita';

function ConsultarEstado() {
    return <ConsultarCita />;
}

// Página recepción (requiere auth)
import RecepcionCitas from './components/RecepcionCitas';
import FormPersona from './components/FormPersona';

function Recepcion() {
    return <RecepcionCitas FormPersona={FormPersona} />;
}
```

## 🎯 Flujo típico de usuario

### Usuario final (público):

1. **Pre-registra** usando `FormPreRegistroCita` o `PreRegistroListaCitas`
2. **Recibe** código de confirmación (ej: `ABC12345`)
3. **Guarda** el código (captura de pantalla, nota, etc.)
4. **Consulta** (opcional) el estado con `ConsultarCita`
5. **Acude** a la clínica con el código

### Personal de recepción (autenticado):

1. **Busca** el pre-registro por código o documento
2. **Verifica** datos básicos con el paciente
3. **Completa** el formulario usando `FormPersona`
4. **Sistema** crea registro formal y actualiza estado a "confirmado"

## 📋 Requisitos de FormPersona

Tu componente `FormPersona` existente debe aceptar estas props:

```jsx
<FormPersona
    datosIniciales={{
        primer_nombre: "Carlos",
        segundo_nombre: "",
        primer_apellido: "Ramirez",
        segundo_apellido: "",
        numero_documento: "1012555321",
        telefonos: [{ numero: "3001234567" }],
        correos: [{ correo: "carlos@example.com" }]
    }}
    onSubmit={(datosCompletos) => {
        // Recibe los datos completos del formulario
        console.log(datosCompletos);
    }}
    textoBoton="✅ Confirmar y guardar registro"
    loading={false}
/>
```

Si tu `FormPersona` tiene una estructura diferente, puedes:

### Opción 1: Adaptar tu FormPersona

```jsx
function FormPersona({ datosIniciales, onSubmit, textoBoton, loading }) {
    // Tu implementación existente
    // Usar datosIniciales para pre-cargar campos
    // Llamar onSubmit cuando se envíe
}
```

### Opción 2: Crear wrapper

```jsx
function FormPersonaWrapper({ datosIniciales, onSubmit, textoBoton, loading }) {
    return (
        <TuFormPersonaExistente
            initialData={datosIniciales}
            onFormSubmit={onSubmit}
            submitLabel={textoBoton}
            isLoading={loading}
        />
    );
}

// Usar el wrapper
<RecepcionCitas FormPersona={FormPersonaWrapper} />
```

## 🔧 Configuración de Axios

Asegúrate de tener configurado el CSRF token:

```javascript
// En tu bootstrap.js o app.js
import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').content;
```

Y en tu layout Blade:

```html
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
```

## 🧪 Testing rápido

### 1. Test desde terminal (backend)

```bash
# Pre-registrar una persona
curl -X POST http://localhost/api/citas/pre-registrar \
  -H "Content-Type: application/json" \
  -H "X-CSRF-TOKEN: tu-token" \
  -d '{"nombres_completos": "Test User"}'

# Consultar por código (reemplaza ABC12345 con el código recibido)
curl http://localhost/api/citas/consultar/ABC12345
```

### 2. Test desde navegador (frontend)

```javascript
// Abre la consola del navegador en tu página pública
axios.post('/api/citas/pre-registrar', {
    nombres_completos: 'Test User',
    numero_documento: '123456789',
    telefono_contacto: '3001234567'
})
.then(response => {
    console.log('Código generado:', response.data.data.codigo_confirmacion);
    alert('Código: ' + response.data.data.codigo_confirmacion);
})
.catch(error => console.error(error));
```

## 🎨 Personalización rápida

### Cambiar colores

Los componentes usan Tailwind CSS. Busca y reemplaza:

- `bg-blue-600` → tu color primario
- `bg-green-600` → tu color de éxito
- `bg-red-600` → tu color de error
- `bg-yellow-100` → tu color de advertencia

### Agregar campo al pre-registro

```php
// 1. Migración
Schema::table('pre_registros_citas', function (Blueprint $table) {
    $table->string('nuevo_campo')->nullable();
});

// 2. Modelo (agregar a $fillable)
protected $fillable = [..., 'nuevo_campo'];

// 3. Validación en controller
$request->validate([
    'nuevo_campo' => 'nullable|string|max:255'
]);

// 4. Frontend
<input
    name="nuevo_campo"
    value={form.nuevo_campo}
    onChange={handleChange}
    ...
/>
```

## 🚨 Solución de problemas comunes

### Error 419 (CSRF Token Mismatch)
```javascript
// Verificar que tienes:
<meta name="csrf-token" content="{{ csrf_token() }}">

// Y en axios:
axios.defaults.headers.common['X-CSRF-TOKEN'] = 
    document.querySelector('meta[name="csrf-token"]').content;
```

### Error 401 (Unauthorized) en rutas de recepción
```php
// Verificar que el usuario está autenticado
Route::middleware(['auth:sanctum'])->group(function () {
    // Tus rutas de recepción
});
```

### FormPersona no se muestra
```jsx
// Verificar que pasas el componente, NO una instancia
<RecepcionCitas FormPersona={FormPersona} /> // ✅ Correcto
<RecepcionCitas FormPersona={<FormPersona />} /> // ❌ Incorrecto
```

### Los datos no se pre-cargan en FormPersona
```jsx
// Verificar que FormPersona acepta datosIniciales
function FormPersona({ datosIniciales, ...props }) {
    const [form, setForm] = useState(datosIniciales || {});
    // ...
}
```

## 📱 Responsive design

Los componentes son responsive por defecto con Tailwind:
- Mobile first
- Grid adapta de 1 a 2-4 columnas según viewport
- Botones apilados en móvil, horizontales en desktop

## 🔐 Seguridad

### Rate limiting (recomendado)

```php
// En RouteServiceProvider o routes/api.php
Route::middleware(['throttle:10,1'])->group(function () {
    Route::post('/citas/pre-registrar', ...);
    Route::post('/citas/pre-registrar-lista', ...);
});
```

### Validación de datos

Ya incluida en el controller, pero puedes personalizar:

```php
// PreRegistroCitaController::preRegistrar()
$validated = $request->validate([
    'nombres_completos' => 'required|string|max:255',
    'numero_documento' => 'nullable|string|max:50',
    // Agregar tus reglas
]);
```

## 🎁 Bonus: Notificaciones

Para enviar el código por SMS/Email después de pre-registrar:

```php
// En PreRegistroCitaController::preRegistrar()
$preRegistro = PreRegistroCita::create($validated);

// Opción 1: Email
Mail::to($preRegistro->email)->send(
    new PreRegistroCreado($preRegistro)
);

// Opción 2: SMS (usar servicio como Twilio)
SMS::send($preRegistro->telefono_contacto, 
    "Tu código de confirmación es: {$preRegistro->codigo_confirmacion}"
);

return response()->json([...]);
```

## 📊 Estadísticas básicas

```php
// Obtener estadísticas
$stats = [
    'pendientes' => PreRegistroCita::where('estado', 'pendiente')->count(),
    'confirmados_hoy' => PreRegistroCita::where('estado', 'confirmado')
        ->whereDate('updated_at', today())->count(),
    'total_mes' => PreRegistroCita::whereMonth('created_at', now()->month)->count()
];
```

## ✅ Checklist de integración

- [ ] Migración ejecutada
- [ ] Rutas verificadas en `routes/api.php`
- [ ] CSRF token configurado
- [ ] FormPersona acepta props requeridas
- [ ] Componentes importados en tu app
- [ ] Testing básico completado
- [ ] Rate limiting configurado (opcional)
- [ ] Notificaciones configuradas (opcional)

## 🆘 Necesitas ayuda?

Revisa:
1. `SISTEMA_CITAS.md` - Documentación completa
2. `storage/logs/laravel.log` - Errores del backend
3. Consola del navegador - Errores del frontend

---

**¡Listo!** Tu sistema de pre-registro de citas está funcionando.

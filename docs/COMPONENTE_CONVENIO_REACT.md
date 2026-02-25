# Componente de Formulario de Convenios en React

## Descripción

Se ha creado un nuevo componente React que reemplaza el formulario Blade tradicional de convenios con las siguientes características:

### ✨ Características Principales

1. **Buscador Reactivo para Países**
   - Se activa automáticamente al escribir 3 o más letras
   - Búsqueda en tiempo real contra la lista de países
   - Integrado con `AutocompleteInput` existente
   - Los datos se obtienen del localStorage

2. **Gestión de Estado**
   - Manejo completo del estado del formulario con React Hooks
   - Validación básica de campos requeridos

3. **Integración con Backend**
   - Compatible con Laravel/PHP
   - Envía datos en formato FormData
   - Soporta CSRF token automáticamente

4. **Datos Persistentes**
   - Los países se almacenan en localStorage
   - Inicialización automática si no existen datos

## Archivos Creados

### 1. `resources/js/components/ConvenioForm.jsx`
Componente principal que contiene:
- Gestión del estado del formulario
- Todos los campos del formulario de convenios
- Integración del autocomplete para países
- Campos de redes sociales

### 2. `resources/js/utils/paisesLocalStorage.js`
Utilidades para gestionar países en localStorage:
- **`inicializarPaisesLocalStorage()`** - Inicializa la lista si no existe
- **`obtenerPaisesLocalStorage()`** - Obtiene países del localStorage
- **`actualizarPaisesLocalStorage(paises)`** - Actualiza la lista
- **`filtrarPaises(busqueda, paises)`** - Filtra por término de búsqueda

### 3. `resources/js/convenioCreate.jsx`
Entry point que:
- Monta el componente React en el DOM
- Inicializa los datos de países
- Obtiene documentos desde el HTML embebido

### 4. `resources/views/convenios/create-react.blade.php`
Versión modificada del formulario Blade que:
- Incluye el contenedor para React
- Pasa documentos como JSON embebido
- Carga el script de Vite

## Instalación y Setup

### Opción 1: Reemplazar el formulario existente

1. **Actualizar la ruta en el controlador:**
```php
public function create()
{
    return view('convenios.create-react', [
        'documentos' => TipoDocumento::all(), // o tus documentos
    ]);
}
```

2. **Agregar el entry point a vite.config.js:**
```javascript
laravel({
    input: [
        // ... otros archivos
        'resources/js/convenioCreate.jsx', // Agregar esta línea
    ],
    refresh: true,
})
```

3. **Ejecutar build:**
```bash
npm run build
# o para desarrollo
npm run dev
```

### Opción 2: Usar en paralelo (sin reemplazar)

Si deseas mantener ambas versiones, simplemente accede a la nueva vista desde una ruta diferente:

```php
Route::get('/convenios/create-react', [ConvenioController::class, 'createReact']);
```

## Uso

### Inicializar países desde el backend

Puedes cargar los países desde tu API en lugar del localStorage:

```jsx
// En ConvenioForm.jsx - modificar el useEffect
useEffect(() => {
  fetch('/api/paises')
    .then(res => res.json())
    .then(data => setPaises(data.map(p => p.nombre)))
    .catch(() => {
      // Fallback a localStorage
      const paisesLocal = localStorage.getItem('paises');
      if (paisesLocal) setPaises(JSON.parse(paisesLocal));
    });
}, []);
```

### Personalizar la lista de países

Edita `resources/js/utils/paisesLocalStorage.js` y actualiza la constante `PAISES_LISTA`.

### Personalizar el comportamiento del envío

Puedes pasar un callback `onSubmit` al componente:

```jsx
<ConvenioForm 
  documentos={documentos}
  onSubmit={(formData) => {
    console.log('Datos del formulario:', formData);
    // Tu lógica personalizada
  }}
/>
```

## Estructura del Formulario

```
Información Básica
├── Tipo de Documento (select)
├── Número de Documento (text)
└── Razón Social (text)

Contacto
├── Teléfono (number)
├── Correo (email)
├── Ciudad (readonly)
├── Dirección (text)
└── País (autocomplete reactivo ✨)

Redes Sociales
├── WhatsApp
├── Google Maps
├── LinkedIn
├── Facebook
├── Instagram
├── TikTok
├── YouTube
├── Sitio Web
└── Otras Redes Sociales
```

## Búsqueda de Países - Detalles

El buscador de países:
- Se activa después de introducir **3 letras**
- Realiza búsqueda **case-insensitive**
- Filtra en tiempo real mientras escribes
- Soporta navegación con teclado (↑↓ Enter Escape)
- Permite valores personalizados si se escriben caracteres no encontrados

## Validación

Los campos requeridos se validan a nivel HTML5:
- `tipo_documento` - Requerido
- `numero_documento` - Requerido
- `razon_social` - Requerido

Puedes agregar validación adicional en el servidor.

## Estilos

El componente utiliza las clases de Tailwind CSS del proyecto. Los estilos están sincronizados con los componentes Blade existentes para mantener consistencia visual.

## Troubleshooting

### "No se encontró el elemento con ID 'convenio-form-root'"
- Asegúrate de que el contenedor `<div id="convenio-form-root"></div>` está en el HTML
- Verifica que el script de Vite se cargó correctamente

### Los países no aparecen
- Comprueba que localStorage no está deshabilitado
- Abre la consola del navegador y ejecuta: `localStorage.getItem('paises')`
- Si está vacío, ejecuta: `inicializarPaisesLocalStorage()`

### El formulario no se envía
- Verifica que el controlador Laravel espera los datos POST
- Comprueba los errores en la consola del navegador
- Asegúrate de que el token CSRF se envía correctamente

## Próximas Mejoras Recomendadas

1. Agregar validación de formularios más robusta
2. Implementar carga desde API en lugar de localStorage
3. Agregar indicador de carga mientras se envían datos
4. Agregar mensajes de éxito/error después del envío
5. Implementar búsqueda fuzzy para países

## Soporte

Para preguntas o problemas, consulta:
- [Documentación de React](https://react.dev)
- [Documentación de AutocompleteInput](./AutocompleteInput.jsx) existente
- Logs del navegador (F12 → Console)

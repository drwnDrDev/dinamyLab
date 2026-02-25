# Guía Rápida: Componente de Convenios en React

## 📚 Archivos Creados

```
resources/
├── js/
│   ├── components/
│   │   └── ConvenioForm.jsx              ← Componente principal del formulario
│   ├── utils/
│   │   └── paisesLocalStorage.js         ← Utilidades para localStorage
│   ├── ejemplos/
│   │   └── ejemplo-inicializar-paises.js ← Ejemplo de inicialización
│   └── convenioCreate.jsx                ← Entry point React
├── views/
│   └── convenios/
│       └── create-react.blade.php        ← Vista Blade con React
└── docs/
    └── COMPONENTE_CONVENIO_REACT.md      ← Documentación completa
```

## 🚀 Inicio Rápido

### 1. Actualizar vite.config.js ✅
*(Ya realizado)*

El entry point `convenioCreate.jsx` ya está configurado en:
```javascript
'resources/js/convenioCreate.jsx'
```

### 2. Inicializar Países en localStorage

**Opción A: Automático (Recomendado)**
```javascript
// En navegador - Consola (F12)
import { inicializarPaisesLocalStorage } from '/resources/js/utils/paisesLocalStorage.js';
inicializarPaisesLocalStorage();
```

**Opción B: Desde API**
Modifica `ConvenioForm.jsx` línea ~43:
```jsx
useEffect(() => {
  fetch('/api/paises')
    .then(res => res.json())
    .then(data => setPaises(data))
    .catch(() => {
      const paisesLocal = localStorage.getItem('paises');
      if (paisesLocal) setPaises(JSON.parse(paisesLocal));
    });
}, []);
```

### 3. Actualizar el Controlador

En `app/Http/Controllers/ConvenioController.php`:

```php
public function create()
{
    return view('convenios.create-react', [
        'documentos' => TipoDocumento::all(),
    ]);
}
```

### 4. Compilar con Vite

```bash
# Desarrollo
npm run dev

# Producción
npm run build
```

### 5. Acceder al Formulario

```
http://localhost:8000/convenios/create
```

## 🔍 Características del Buscador de Países

| Característica | Descripción |
|---|---|
| **Activación** | Después de 3 letras |
| **Búsqueda** | Case-insensitive |
| **Navegación** | ↑ ↓ Enter Escape |
| **Valores Custom** | Permite texto no encontrado |
| **Fuente** | localStorage |

### Ejemplo de Uso:
```
Usuario escribe: "col"  → Muestra resultados
Usuario escribe: "colom" → Filtra a "Colombia"
```

## 📋 Estructura de Datos

### FormData Enviado:
```javascript
{
  tipo_documento: "91",
  numero_documento: "123456789",
  razon_social: "Mi Empresa S.A.",
  telefono: "1234567890",
  correo: "info@empresa.com",
  municipio: "11001",
  direccion: "Calle 1 #100",
  pais: "Colombia",           // ← Autocomplete
  redes: {
    whatsapp: "+57300000000",
    maps: "https://maps.google.com/...",
    linkedin: "https://linkedin.com/...",
    facebook: "",
    instagram: "",
    tiktok: "",
    youtube: "",
    website: "",
    otras_redes: ""
  }
}
```

## 🛠️ Personalización

### Cambiar Minutos de Activación del Buscador
En `ConvenioForm.jsx`, línea ~264:
```jsx
<AutocompleteInput
  minLengthToShow={2}  // ← Cambiar de 3 a 2 (o cualquier número)
  ...
/>
```

### Agregar Más Campos
En `ConvenioForm.jsx`:
1. Agregar al estado inicial `formData`
2. Crear `handleInputChange` si es necesario
3. Agregar input en el JSX
4. Asegurar que se envíe en `handleSubmit`

### Cambiar Estilos
Los estilos usan clases Tailwind. Modifica en:
- `className="shadow appearance-none border rounded..."`

## 🐛 Troubleshooting

### Error: "convenioCreate.jsx not found"
```bash
# Solución
npm run dev  # Asegurar que Vite está ejecutándose
```

### Países vacíos después de cargar
```javascript
// Verificar en consola
console.log(localStorage.getItem('paises'));
```

Si está vacío:
```javascript
import { inicializarPaisesLocalStorage } from '...';
inicializarPaisesLocalStorage();
```

### Formulario no se envía
1. Verificar consola del navegador (F12)
2. Verificar que el controlador existe
3. Verificar CSRF token:
   ```html
   <meta name="csrf-token" content="{{ csrf_token() }}">
   ```

## 📞 Soporte

Documentación completa: [COMPONENTE_CONVENIO_REACT.md](./COMPONENTE_CONVENIO_REACT.md)

Ejemplos de integración: [EJEMPLO_INTEGRACION_LARAVEL.php](./EJEMPLO_INTEGRACION_LARAVEL.php)

## ✨ Próximas Mejoras

- [ ] Agregar validación robusta
- [ ] Notificaciones de éxito/error
- [ ] Carga de archivos
- [ ] Búsqueda fuzzy en países
- [ ] Integración con API para países

---

**¡Listo! El componente está completamente funcional. 🎉**

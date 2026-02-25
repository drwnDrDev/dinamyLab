# Ejemplos Visuales - Componente de Convenios React

## 📸 Interfaz del Buscador de Países

### Estado 1: Input Vacío
```
┌─────────────────────────────────────┐
│ País:                               │
│ ┌───────────────────────────────────┤
│ │ Empieza a escribir (mín. 3 letras)│
│ └───────────────────────────────────┘
│ (No muestra sugerencias)             │
└─────────────────────────────────────┘
```

### Estado 2: Menos de 3 Letras
```
┌─────────────────────────────────────┐
│ País:                               │
│ ┌───────────────────────────────────┤
│ │ co                                │
│ └───────────────────────────────────┘
│ (0 resultados - esperando más texto) │
└─────────────────────────────────────┘
```

### Estado 3: 3+ Letras (Activo) ✨
```
┌─────────────────────────────────────┐
│ País:                               │
│ ┌───────────────────────────────────┤
│ │ col                               │
│ └───────────────────────────────────┘
│
│ Resultados:
│ ┌───────────────────────────────────┐
│ │ > Colombia                        │  ← Seleccionado (destacado)
│ │   Colón, Archipiélago           │
│ └───────────────────────────────────┘
└─────────────────────────────────────┘
```

### Estado 4: Selección Completa
```
┌─────────────────────────────────────┐
│ País:                               │
│ ┌───────────────────────────────────┤
│ │ Colombia                          │  ← Valor guardado
│ └───────────────────────────────────┘
│ (Dropdown cerrado automáticamente)   │
└─────────────────────────────────────┘
```

---

## 🎯 Casos de Uso

### Caso 1: Usuario busca "Colombia"
```
Paso 1: Usuario escribe "col"
├─ Verifica que tiene 3 caracteres ✓
├─ Abre dropdown con sugerencias
└─ Muestra: Colombia, Colón Archipiélago

Paso 2: Usuario sigue escribiendo "ombia"
├─ Input actualizado a "colombia"
├─ Filtra resultados
└─ Filtra a: Colombia

Paso 3: Usuario presiona Enter o click
├─ Selecciona "Colombia"
├─ Cierra dropdown
└─ Formulario listo para envío
```

### Caso 2: Usuario escribe valor no encontrado
```
Paso 1: Usuario escribe "xyz..."
├─ Verifica que tiene 3+ caracteres ✓
├─ No encuentra coincidencias
└─ Dropdown permanece abierto (0 resultados)

Paso 2: Usuario presiona Enter
├─ Valor custom "xyz..." es aceptado
├─ Se guarda en formulario
└─ allowCustom=true permite esto
```

### Caso 3: Navegación por teclado
```
Tecla ↓  → Baja en lista de sugerencias
Tecla ↑  → Sube en lista de sugerencias
Tecla Enter → Selecciona opción destacada
Tecla Escape → Cierra dropdown
```

---

## 💻 Ejemplos de Código

### Inicializar Países en el Navegador
```javascript
// Ejecutar en Console (F12) una sola vez

// Opción 1: Automático
import { inicializarPaisesLocalStorage } from '/resources/js/utils/paisesLocalStorage.js';
inicializarPaisesLocalStorage();

// Verificar resultado
JSON.parse(localStorage.getItem('paises')).slice(0, 5)
// Output: ["Afganistán", "Albania", "Alemania", "Andorra", "Angola"]
```

### Actualizar Lista de Países
```javascript
// Cargar países personalizados
const misPaises = [
  'Colombia',
  'Argentina',
  'Brasil',
  'México',
  'Perú'
];

import { actualizarPaisesLocalStorage } from '/resources/js/utils/paisesLocalStorage.js';
actualizarPaisesLocalStorage(misPaises);

// Verificar
console.log(localStorage.getItem('paises'));
```

### Obtener Países desde Código
```javascript
// En un componente React
import { obtenerPaisesLocalStorage } from '../utils/paisesLocalStorage';

const paises = obtenerPaisesLocalStorage();
console.log('Total de países:', paises.length);
```

---

## 📊 Datos de Entrada/Salida

### Enviado al Servidor (POST)
```json
{
  "_token": "xyz123token...",
  "tipo_documento": "91",
  "numero_documento": "900123456",
  "razon_social": "EMPRESA S.A.S",
  "telefono": "3005551234",
  "correo": "contacto@empresa.com",
  "municipio": "11001",
  "direccion": "Carrera 5 #10-50",
  "pais": "Colombia",
  "redes[whatsapp]": "+573005551234",
  "redes[maps]": "https://goo.gl/maps/...",
  "redes[linkedin]": "https://linkedin.com/company/empresa",
  "redes[facebook]": "https://facebook.com/empresa",
  "redes[instagram]": "@empresa",
  "redes[tiktok]": "@empresa",
  "redes[youtube]": "https://youtube.com/@empresa",
  "redes[website]": "https://www.empresa.com",
  "redes[otras_redes]": ""
}
```

### Respuesta Esperada
```json
{
  "success": true,
  "message": "Convenio creado exitosamente",
  "redirect": "/convenios/123"
}
```

---

## 🎨 Personalización de Estilos

### Cambiar Color del Botón
En `ConvenioForm.jsx`, línea ~484:

```jsx
// Antes
className="bg-blue-500 hover:bg-blue-700 text-white..."

// Después (Tailwind colors)
className="bg-green-500 hover:bg-green-700 text-white..."  // Verde
className="bg-red-500 hover:bg-red-700 text-white..."      // Rojo
className="bg-purple-500 hover:bg-purple-700 text-white..."// Púrpura
```

### Cambiar Tamaño de Input
```jsx
// Antes
className="shadow appearance-none border rounded w-full py-2 px-3..."

// Después
className="shadow appearance-none border rounded w-full py-3 px-4 text-lg..."
```

### Cambiar Border Style
```jsx
// Border rojo en error
className="border-red-500"

// Border verde en éxito
className="border-green-500"

// Border más grueso
className="border-2"
```

---

## 🧪 Testing Manual

### Checklist de Funcionalidad

- [ ] **Input básico**
  - Escribir texto normal
  - Espacio activar después de 3 letras

- [ ] **Autocomplete**
  - Escribir "col" → muestra sugerencias
  - Click en sugerencia → selecciona valor
  - Presionar Enter → selecciona valor resaltado

- [ ] **Navegación**
  - Flecha arriba/abajo navega opciones
  - Escape cierra dropdown
  - Click fuera cierra dropdown

- [ ] **Formulario Completo**
  - Llenar todos los campos requeridos
  - Seleccionar país
  - Presionar "Guardar"
  - Verificar respuesta del servidor

- [ ] **Mobile Responsive**
  - Probar en pantalla pequeña (375px)
  - Probar en tablet (768px)
  - Probar en desktop (1024px+)

---

## 📈 Estadísticas

| Métrica | Valor |
|---------|-------|
| Total de países | 195+ |
| Caracteres mínimos para buscar | 3 |
| Resultados máximos mostrados | 10 |
| Campos en formulario | 23 |
| + Campos de redes sociales | 9 |

---

## 🔗 Enlaces Relacionados

- [Documentación Completa](./COMPONENTE_CONVENIO_REACT.md)
- [Guía de Inicio Rápido](./INICIO_RAPIDO_CONVENIO_REACT.md)
- [Ejemplo Integración Laravel](./EJEMPLO_INTEGRACION_LARAVEL.php)
- [Componente AutocompleteInput](../resources/js/components/AutocompleteInput.jsx)

---

## 💡 Tips y Tricks

### Reutilizar el Autocomplete en otros formularios
```jsx
import AutocompleteInput from './AutocompleteInput';

// Usar en cualquier campo que necesite autocompletar
<AutocompleteInput
  value={estado}
  onChange={setSuGerencia}
  suggestions={misSugerencias}
  minLengthToShow={3}
/>
```

### Debug localStorage
```javascript
// Ver todos los datos
console.table(JSON.parse(localStorage.getItem('paises')));

// Limpiar datos
localStorage.removeItem('paises');

// Limpiar todo
localStorage.clear();
```

### Monitorear cambios del formulario
```jsx
// En ConvenioForm.jsx, agregar al final del componente
useEffect(() => {
  console.log('Formulario actualizado:', formData);
}, [formData]); // Ejecuta cada vez que cambia formData
```

---

**Documento generado para la versión 1.0 del Componente de Convenios React**

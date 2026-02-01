# Quick Reference - Módulo Cargador de Lista de Personas

## 📦 Archivos creados

```
backend/
├── app/
│   ├── Services/
│   │   └── ParseadorListaPersonas.php          [Servicio de parseo]
│   └── Http/Controllers/Api/
│       └── ListaPersonasController.php          [Controlador API]
├── routes/
│   └── api.php                                   [Ruta agregada]

frontend/
├── resources/js/components/
│   ├── CargadorListaPersonas.jsx                [Componente principal]
│   ├── hooks/
│   │   └── useListaPersonas.js                  [Hook personalizado]
│   ├── ejemplos/
│   │   └── CrearOrdenComponentMejorado.jsx      [Ejemplo de integración]
│   └── test/
│       └── TestCargadorListaPersonas.jsx        [Componente de prueba]

docs/
└── MODULO_CARGADOR_LISTA_PERSONAS.md            [Documentación completa]
```

## 🚀 Inicio rápido

### 1️⃣ Backend - Ya está listo

Endpoint disponible:
```
POST /api/personas/parsear-lista
```

Body:
```json
{
  "contenido": "Carlos Ramirez,1012555321\nZonia Fierro,",
  "tipo_documento": "CC"
}
```

### 2️⃣ Frontend - Uso básico

```jsx
import CargadorListaPersonas from './CargadorListaPersonas';

<CargadorListaPersonas 
    onPersonasLoaded={(persona) => console.log(persona)}
    perfil="Paciente"
/>
```

### 3️⃣ Integración en FormPersona

```jsx
import CargadorListaPersonas from './CargadorListaPersonas';
import { useListaPersonas } from './hooks/useListaPersonas';

const MiComponente = () => {
    const { cargarPersona } = useListaPersonas();
    const [persona, setPersona] = useState(null);

    const handlePersona = (p) => {
        const formateada = cargarPersona(p);
        setPersona(formateada);
    };

    return (
        <>
            {!persona && (
                <CargadorListaPersonas onPersonasLoaded={handlePersona} />
            )}
            {persona && (
                <FormPersona persona={persona} setPersona={setPersona} />
            )}
        </>
    );
};
```

## 🧪 Pruebas

Visita la ruta test:
```
/test-cargador-lista-personas
```

Para agregarlo en tus rutas:
```jsx
// En tu router o archivo de rutas
import TestCargadorListaPersonas from './test/TestCargadorListaPersonas';

<Route path="/test-cargador-lista-personas" element={<TestCargadorListaPersonas />} />
```

## 📝 Formato de entrada

```
Nombres Apellidos, Número_Documento
Nombres Apellidos,
Nombre Apellido, Documento
```

**Reglas:**
- ✅ Una persona por línea
- ✅ Separar nombre/apellido del documento con coma
- ✅ El número de documento es opcional
- ✅ Los espacios se normalizan automáticamente
- ✅ Las líneas vacías se ignoran

## 🎯 Casos de uso

### Caso 1: Cargar un paciente
```jsx
<CargadorListaPersonas perfil="Paciente" />
```

### Caso 2: Cargar un acompañante
```jsx
<CargadorListaPersonas perfil="Acompañante" />
```

### Caso 3: Con callback personalizado
```jsx
<CargadorListaPersonas 
    perfil="Paciente"
    onPersonasLoaded={(persona) => {
        // Tu lógica aquí
        guardarPersona(persona);
    }}
/>
```

## 🔧 Personalización

### Cambiar tipos de documento
En `CargadorListaPersonas.jsx`, línea ~44:
```jsx
<select value={tipoDocumento} onChange={(e) => setTipoDocumento(e.target.value)}>
    <option value="CC">Cédula...</option>
    {/* Agrega más opciones */}
</select>
```

### Cambiar estilos
El componente usa Tailwind CSS. Busca las clases `bg-`, `text-`, `border-` y ajusta según tu tema.

### Agregar validaciones
En `ParseadorListaPersonas.php`, método `parsearLinea()`:
```php
// Agregar validaciones personalizadas
private static function parsearLinea(string $linea, string $tipoDocumentoDefault): ?array {
    // Tu lógica aquí
}
```

## ⚠️ Troubleshooting

| Problema | Solución |
|----------|----------|
| "Error al parsear" | Verifica que el contenido no esté vacío |
| No encuentra personas | Asegúrate de que el documento existe en BD |
| Datos incompletos | El sistema solo trae lo que está registrado |
| Errores de CORS | Verifica la configuración de Sanctum |

## 🔗 Rutas relacionadas

```php
// Ya agregada en routes/api.php
Route::post('personas/parsear-lista', [ListaPersonasController::class, 'parsearLista']);

// Rutas existentes (para referencia)
Route::get('personas/{id}', [PersonaController::class, 'show']);
Route::post('personas', [PersonaController::class, 'store']);
Route::get('personas/buscar/{numero_documento}', [PersonaController::class, 'buscar']);
```

## 📊 Estructura de respuesta

```json
{
  "message": "Lista parseada correctamente",
  "data": [
    {
      "id": null,
      "tipo_documento": "CC",
      "numero_documento": "1012555321",
      "primer_nombre": "Carlos",
      "segundo_nombre": "",
      "primer_apellido": "Ramirez",
      "segundo_apellido": "",
      "fecha_nacimiento": "",
      "sexo": "",
      "pais_origen": "170",
      "telefono": "",
      "zona": "02",
      "pais_residencia": "170",
      "correo": "",
      "eps": "",
      "tipo_afiliacion": "",
      "existente": false,
      "nombres_completos": "Carlos Ramirez"
    }
  ],
  "total": 1
}
```

## 🎨 Estados visuales

- 🟢 **Verde**: Persona existente en BD (datos completos)
- 🔵 **Azul**: Persona nueva (datos parseados)
- ⚪ **Gris**: Cargador en espera

## 🔒 Seguridad

- ✅ Autenticación Sanctum requerida
- ✅ CSRF token automático
- ✅ Validación server-side
- ✅ Datos buscados en BD, no asumidos

## 🚀 Próximos pasos

1. Integra en tu componente principal
2. Prueba con datos de ejemplo
3. Personaliza estilos si es necesario
4. Sube a producción

## 📚 Referencias

- [Documentación completa](./MODULO_CARGADOR_LISTA_PERSONAS.md)
- [Ejemplo de integración](./ejemplos/CrearOrdenComponentMejorado.jsx)
- [Componente de prueba](./test/TestCargadorListaPersonas.jsx)

---

**Versión**: 1.0  
**Fecha**: 2026-01-13  
**Estado**: ✅ Producción lista

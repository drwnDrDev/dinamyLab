# Guía de Instalación - Módulo Cargador de Lista de Personas

## ✅ Estado: Completamente implementado

Todo está listo para usar. Solo necesitas integrar el componente en tu aplicación.

## 📋 Verificación de archivos

Asegúrate de que estos archivos existan:

### Backend
- [ ] `/app/Services/ParseadorListaPersonas.php`
- [ ] `/app/Http/Controllers/Api/ListaPersonasController.php`
- [ ] `/routes/api.php` (con la ruta agregada)

### Frontend
- [ ] `/resources/js/components/CargadorListaPersonas.jsx`
- [ ] `/resources/js/components/hooks/useListaPersonas.js`

### Documentación
- [ ] `/docs/MODULO_CARGADOR_LISTA_PERSONAS.md`
- [ ] `/QUICK_REFERENCE_CARGADOR.md`
- [ ] `/resources/js/components/ejemplos/CrearOrdenComponentMejorado.jsx`
- [ ] `/resources/js/components/test/TestCargadorListaPersonas.jsx`

## 🚀 Pasos para usar

### 1. No requiere instalación adicional
El módulo usa:
- ✅ React (ya instalado)
- ✅ Axios (ya configurado)
- ✅ Tailwind CSS (ya disponible)
- ✅ Laravel API (backend existente)

### 2. Opción A: Uso inmediato en test

Agrega una ruta de prueba:

**routes/web.php** o tu archivo de rutas frontend:
```jsx
import TestCargadorListaPersonas from '../components/test/TestCargadorListaPersonas';

<Route path="/test-cargador" element={<TestCargadorListaPersonas />} />
```

Luego abre: `http://localhost/test-cargador`

### 3. Opción B: Integración en componente existente

En el componente donde quieras usar:

```jsx
// Importar componente
import CargadorListaPersonas from './CargadorListaPersonas';
import { useListaPersonas } from './hooks/useListaPersonas';

// En tu componente
const MiComponente = () => {
    const [persona, setPersona] = useState(null);
    const { cargarPersona } = useListaPersonas();

    const handlePersona = (p) => {
        const formateada = cargarPersona(p);
        setPersona(formateada);
    };

    return (
        <>
            {!persona ? (
                <CargadorListaPersonas onPersonasLoaded={handlePersona} />
            ) : (
                <FormPersona persona={persona} setPersona={setPersona} />
            )}
        </>
    );
};
```

### 4. Opción C: Reemplazar CrearOrdenComponent

Si quieres la integración completa, puedes:

**Opción C.1:** Reemplazar directamente
```jsx
// Copiar contenido de CrearOrdenComponentMejorado.jsx
// y pegar en CrearOrdenComponent.jsx
```

**Opción C.2:** Crear componente nuevo
```jsx
// Copiar CrearOrdenComponentMejorado.jsx como nuevo componente
import CrearOrdenComponentMejorado from './CrearOrdenComponentMejorado';

// Usar en lugar del anterior
<CrearOrdenComponentMejorado />
```

## 🧪 Verificar que funciona

### Test 1: Endpoint API
```bash
curl -X POST http://localhost/api/personas/parsear-lista \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "contenido": "Carlos Ramirez, 1012555321",
    "tipo_documento": "CC"
  }'
```

Esperado:
```json
{
  "message": "Lista parseada correctamente",
  "data": [...],
  "total": 1
}
```

### Test 2: Frontend visual
```jsx
import CargadorListaPersonas from './CargadorListaPersonas';

<CargadorListaPersonas 
    onPersonasLoaded={(p) => console.log(p)}
    perfil="Paciente"
/>
```

Paste en el textarea:
```
Carlos Ramirez, 1012555321
Zonia Fierro,
```

Deberías ver 2 personas con estado.

## ⚙️ Configuración opcional

### Cambiar tipo de documento por defecto
En `CargadorListaPersonas.jsx`, línea ~27:
```jsx
const [tipoDocumento, setTipoDocumento] = useState('CC'); // Cambiar a otro valor
```

### Cambiar etiqueta de perfil
```jsx
<CargadorListaPersonas 
    perfil="Acompañante"  // Cambia la etiqueta
    onPersonasLoaded={...}
/>
```

### Cambiar estilos
Busca clases Tailwind en:
- `CargadorListaPersonas.jsx` - Estilos del componente
- Modifica `bg-`, `text-`, `border-` según tu tema

## 🔐 Permisos necesarios

Asegúrate de que el usuario autenticado tiene permisos para:
- [ ] Leer personas (`api/personas`)
- [ ] Crear personas (`api/personas`)
- [ ] Buscar personas por documento

En Laravel, esto usualmente está en:
- `app/Http/Middleware/Authenticate.php`
- `config/sanctum.php`

## 🐛 Debugging

Si algo falla, revisa:

### 1. Consola del navegador (F12)
- Busca errores en rojo
- Mira la pestaña Network para ver request/response

### 2. Logs del servidor
```bash
tail -f storage/logs/laravel.log
```

### 3. Respuesta de API
```bash
# Verifica que la ruta exista
php artisan route:list | grep personas/parsear-lista
```

### 4. Permisos de autenticación
```bash
# Verifica que el usuario está autenticado
curl -H "Authorization: Bearer $TOKEN" http://localhost/api/personas
```

## 📦 Dependencies

**Backend:**
- ✅ Laravel (existente)
- ✅ PHP 8.0+ (existente)

**Frontend:**
- ✅ React (existente)
- ✅ Axios (existente)
- ✅ Tailwind CSS (existente)

**No hay que instalar nada adicional**

## 🚀 Deploy

### 1. Desarrollo
```bash
npm run dev
php artisan serve
```

Luego abre: `http://localhost:8000/test-cargador`

### 2. Producción
```bash
npm run build
php artisan cache:clear
```

## 🎯 Próximos pasos

1. ✅ Verifica que todos los archivos existan
2. ✅ Prueba con la ruta de test
3. ✅ Integra en tu componente
4. ✅ Personaliza estilos si necesario
5. ✅ Despliega a producción

## 📞 Soporte

Si algo no funciona:

1. Revisa los logs
2. Verifica el endpoint en Postman/Insomnia
3. Consulta la documentación completa
4. Revisa los ejemplos de integración

## 📚 Documentación

- **Documentación completa**: `docs/MODULO_CARGADOR_LISTA_PERSONAS.md`
- **Quick reference**: `QUICK_REFERENCE_CARGADOR.md`
- **Ejemplo integración**: `resources/js/components/ejemplos/CrearOrdenComponentMejorado.jsx`
- **Test**: `resources/js/components/test/TestCargadorListaPersonas.jsx`

## ✨ Features

- ✅ Parseo automático de nombres y apellidos
- ✅ Búsqueda de personas en BD
- ✅ Interfaz intuitiva y responsive
- ✅ Soporte para múltiples tipos de documento
- ✅ Validación en cliente y servidor
- ✅ Seguridad CSRF
- ✅ Manejo de errores robusto
- ✅ Carga en lote

## 🎉 ¡Listo!

El módulo está completamente implementado y listo para usar. 

**Inicio rápido:**
```jsx
import CargadorListaPersonas from './CargadorListaPersonas';

<CargadorListaPersonas perfil="Paciente" onPersonasLoaded={handlePersona} />
```

---

**Fecha de creación**: 2026-01-13  
**Versión**: 1.0  
**Estado**: ✅ Listo para producción

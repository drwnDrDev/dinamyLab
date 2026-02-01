# 🚀 Inicio Rápido - 5 minutos

## Opción 1: Solo Test (Sin Integrar)

### Paso 1: Abre el navegador
```
http://localhost/test-cargador
```

### Paso 2: Pega datos de ejemplo
```
Carlos Ramirez,1012555321
Zonia Fierro,
Liliana Diaz, 123123654
```

### Paso 3: Click en "🔍 Parsear"

### Paso 4: Verifica resultados
```
✅ Carlos Ramirez (si existe en BD)
✅ Zonia Fierro (nuevo)
✅ Liliana Diaz (si existe en BD)
```

**¡Listo!** El módulo funciona.

---

## Opción 2: Integración en Componente (10 minutos)

### Paso 1: Importar componentes
```jsx
import CargadorListaPersonas from './CargadorListaPersonas';
import { useListaPersonas } from './hooks/useListaPersonas';
```

### Paso 2: Agregar estado
```jsx
const [persona, setPersona] = useState(null);
const { cargarPersona } = useListaPersonas();
```

### Paso 3: Agregar handler
```jsx
const handlePersona = (p) => {
    const formateada = cargarPersona(p);
    setPersona(formateada);
};
```

### Paso 4: Usar en JSX
```jsx
{!persona ? (
    <CargadorListaPersonas onPersonasLoaded={handlePersona} />
) : (
    <FormPersona persona={persona} setPersona={setPersona} />
)}
```

### Paso 5: Test
```
✅ Ingresa lista
✅ Clickea persona
✅ FormPersona se llena automáticamente
✅ Guarda
```

**¡Listo!** Integrado.

---

## Opción 3: Integración Completa (CrearOrdenComponent)

### Paso 1: Copiar estructura
```jsx
// Copiar de CrearOrdenComponentMejorado.jsx
// líneas: 1-50
```

### Paso 2: Reemplazar en CrearOrdenComponent
```jsx
// Reemplazar la sección de FormPersona
// con la nueva sección que incluye CargadorLista
```

### Paso 3: Test
```
✅ Página carga
✅ Muestra cargador
✅ Parsea lista
✅ Muestra resultados
✅ Selecciona persona
✅ Se llena FormPersona
✅ Puede crear orden
```

**¡Listo!** Totalmente integrado.

---

## 🧪 Verificación rápida (2 minutos)

### Backend
```bash
# Verificar ruta
php artisan route:list | grep parsear-lista

# Debe mostrar algo como:
# POST   api/personas/parsear-lista
```

### Frontend
```bash
# Compilar
npm run dev

# Debe completar sin errores
```

### API
```bash
# Probar endpoint
curl -X POST http://localhost/api/personas/parsear-lista \
  -H "Authorization: Bearer TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"contenido":"Carlos Ramirez,1012555321"}'

# Debe retornar JSON con datos parseados
```

---

## 📍 Próximos pasos

### Si todo funciona:
1. ✅ Hacer backup
2. ✅ Notificar al equipo
3. ✅ Deploy a producción

### Si hay problemas:
1. 📖 Leer CHECKLIST_VERIFICACION.md
2. 📖 Leer QUICK_REFERENCE_CARGADOR.md
3. 📖 Consultar troubleshooting en docs/

---

## 📚 Documentación

| Si quieres... | Lee... |
|---|---|
| Entender qué es | RESUMEN_MODULO_CARGADOR.md |
| Setup completo | INSTALACION_CARGADOR.md |
| Referencia rápida | QUICK_REFERENCE_CARGADOR.md |
| Detalles técnicos | docs/MODULO_CARGADOR_LISTA_PERSONAS.md |
| Ver diagramas | DIAGRAMAS_CARGADOR.md |
| Hacer test | CHECKLIST_VERIFICACION.md |
| Entender flujo | INDICE_MODULO_CARGADOR.md |

---

## ⚡ Command Cheatsheet

```bash
# Ver rutas
php artisan route:list | grep persona

# Ver logs
tail -f storage/logs/laravel.log

# Limpiar cache
php artisan cache:clear

# Compilar frontend
npm run build

# Watch mode
npm run dev
```

---

## 🎯 Formato de entrada

Válido:
```
Carlos Ramirez,1012555321
Zonia Fierro,
Juan de la Cruz, 9999999999
```

Resultado:
```
✓ Carlos Ramirez (CC: 1012555321)
✓ Zonia Fierro (sin documento)
✓ Juan de la Cruz (CC: 9999999999)
```

---

## ❓ Preguntas frecuentes

**¿Qué es esto?**
Módulo para importar listas de personas y precargar en formularios.

**¿Necesito instalar algo?**
No, todo está incluido.

**¿Dónde lo uso?**
En cualquier lugar donde uses FormPersona.

**¿Es seguro?**
100% - Usa Sanctum auth + CSRF token.

**¿Funciona en móvil?**
Sí, responsive design incluido.

**¿Qué puedo hacer si falla?**
Ver CHECKLIST_VERIFICACION.md

**¿Se puede modificar?**
Sí, todo está documentado y es modular.

**¿Hay ejemplos?**
Sí, 3 ejemplos funcionales incluidos.

---

## 🎉 ¡Ya estás listo!

Haz uno de estos:

### Opción A: Solo probar
```
Abre: http://localhost/test-cargador
```

### Opción B: Integrar en tu proyecto
```
Sigue: INSTALACION_CARGADOR.md → Paso 3
```

### Opción C: Ver ejemplos
```
Lee: CrearOrdenComponentMejorado.jsx
```

---

**Tiempo total**: ~5-10 minutos  
**Dificultad**: 🟢 Fácil  
**Resultado**: ✅ Módulo funcional

¡Éxito! 🚀

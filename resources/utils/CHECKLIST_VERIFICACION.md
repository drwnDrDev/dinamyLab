# ✅ Checklist de Verificación - Módulo Cargador

## 📋 Pre-deployment checklist

### 1️⃣ Verificar archivos creados

- [ ] `/app/Services/ParseadorListaPersonas.php` - Existe y sin errores
- [ ] `/app/Http/Controllers/Api/ListaPersonasController.php` - Existe y sin errores
- [ ] `/routes/api.php` - Contiene la ruta agregada
- [ ] `/resources/js/components/CargadorListaPersonas.jsx` - Existe y sin errores
- [ ] `/resources/js/components/hooks/useListaPersonas.js` - Existe y sin errores

### 2️⃣ Verificar Backend

```bash
# Verificar sintaxis PHP
php -l app/Services/ParseadorListaPersonas.php
php -l app/Http/Controllers/Api/ListaPersonasController.php

# Verificar que la ruta está registrada
php artisan route:list | grep parsear-lista

# Debería mostrar:
# POST api/personas/parsear-lista ............ personas.parsear-lista
```

**Resultado esperado:**
```bash
✓ No syntax errors detected
✓ Route appears in list
```

- [ ] Sin errores de sintaxis
- [ ] Ruta visible en `php artisan route:list`
- [ ] Controlador usa middleware `auth:sanctum`

### 3️⃣ Verificar Frontend

```bash
# Revisar que no hay errores en la consola
npm run dev

# Debería compilar sin errores
```

- [ ] npm/yarn instala sin errores
- [ ] Webpack/Vite compila sin warnings
- [ ] Importaciones están correctas

### 4️⃣ Test API Manual

Usar Postman, Insomnia o curl:

```bash
curl -X POST http://localhost/api/personas/parsear-lista \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "contenido": "Carlos Ramirez, 1012555321",
    "tipo_documento": "CC"
  }'
```

**Verificar respuesta:**
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
      "existente": false
    }
  ],
  "total": 1
}
```

- [ ] Endpoint responde exitosamente
- [ ] Datos parseados correctamente
- [ ] No hay errores de autenticación

### 5️⃣ Test Frontend en Browser

```bash
npm run dev
# Abrir http://localhost:3000 (o tu puerto)
```

**Acciones:**

1. Abre componente `CargadorListaPersonas`
   - [ ] Se carga sin errores en consola
   - [ ] Textarea es visible
   - [ ] Select de tipo documento aparece

2. Ingresa datos de prueba:
   ```
   Carlos Ramirez, 1012555321
   Zonia Fierro,
   ```
   - [ ] Texto se ingresa correctamente
   - [ ] Botón "Parsear" es clickeable

3. Click en "Parsear"
   - [ ] Aparece loading spinner
   - [ ] No hay errores en consola
   - [ ] Después de ~1 seg aparecen resultados

4. Verifica resultados
   - [ ] Se muestran 2 personas
   - [ ] Nombres están correctos
   - [ ] Documentos están correctos
   - [ ] Estados (existente/nuevo) son visibles

5. Click en una persona
   - [ ] Se ejecuta `onPersonasLoaded()`
   - [ ] Datos llegan correctamente
   - [ ] No hay errores

### 6️⃣ Test de Integración

```jsx
import CargadorListaPersonas from './CargadorListaPersonas';
import { useListaPersonas } from './hooks/useListaPersonas';

const TestIntegration = () => {
    const [persona, setPersona] = useState(null);
    const { cargarPersona } = useListaPersonas();

    const handlePersona = (p) => {
        const formateada = cargarPersona(p);
        console.log('Persona formateada:', formateada);
        setPersona(formateada);
    };

    return (
        <>
            {!persona && (
                <CargadorListaPersonas onPersonasLoaded={handlePersona} />
            )}
            {persona && (
                <div>
                    <p>ID: {persona.data.id}</p>
                    <p>Nombre: {persona.data.primer_nombre}</p>
                </div>
            )}
        </>
    );
};
```

- [ ] Hook `useListaPersonas` importa sin errores
- [ ] Función `cargarPersona()` formatea datos correctamente
- [ ] `persona.data` tiene estructura esperada

### 7️⃣ Test en FormPersona

Si quieres integrar en FormPersona:

```jsx
import FormPersona from './FormPersona';
import CargadorListaPersonas from './CargadorListaPersonas';
import { useListaPersonas } from './hooks/useListaPersonas';

const TestFormPersona = () => {
    const [persona, setPersona] = useState(null);
    const { cargarPersona } = useListaPersonas();

    return (
        <>
            {!persona && (
                <CargadorListaPersonas 
                    onPersonasLoaded={(p) => {
                        setPersona(cargarPersona(p));
                    }}
                />
            )}
            {persona && (
                <FormPersona persona={persona} setPersona={setPersona} />
            )}
        </>
    );
};
```

**Verificar:**
- [ ] CargadorLista carga persona
- [ ] FormPersona recibe datos precargados
- [ ] Campos de nombre/apellido están llenos
- [ ] Campos de documento están llenos
- [ ] Puedo editar y guardar

### 8️⃣ Test de Casos Límite

**Caso 1: Contenido vacío**
```
(dejar vacío)
[Parsear]
```
- [ ] Muestra error "contenido no puede estar vacío"

**Caso 2: Una sola palabra**
```
Carlos
```
- [ ] Parsea como primer nombre
- [ ] Otros campos quedan vacíos

**Caso 3: Muchas palabras**
```
Juan Carlos Alberto Ramirez López García
```
- [ ] Toma primeras 4 palabras
- [ ] 5+ palabras se ignoran

**Caso 4: Números en documento**
```
Carlos Ramirez,
```
- [ ] Sin documento OK
- [ ] Busca en BD solo si hay documento

**Caso 5: Documento que existe**
```
(con documento de persona en BD)
```
- [ ] Marca como "✓ Existente"
- [ ] Trae datos de la BD

**Caso 6: Documento que NO existe**
```
Carlos Ramirez, 9999999999
```
- [ ] Marca como "+ Nuevo"
- [ ] No llena datos adicionales

### 9️⃣ Test de Rendimiento

**Verificar tiempos:**

```javascript
// En consola del navegador
console.time('parsear');
// ... hacer click en parsear
console.timeEnd('parsear');

// Debería mostrar: ~500ms máximo
```

- [ ] Parsing: < 100ms
- [ ] API call: < 200ms
- [ ] Renderizado: < 100ms
- [ ] Total: < 500ms

### 🔟 Verificar Seguridad

- [ ] CSRF token se envía con axios
- [ ] Autenticación required en endpoint
- [ ] No hay credenciales en logs
- [ ] Contraseñas no se envían
- [ ] XSS escaping funciona (React default)

### 1️⃣1️⃣ Revisar Documentación

- [ ] `MODULO_CARGADOR_LISTA_PERSONAS.md` existe
- [ ] `QUICK_REFERENCE_CARGADOR.md` existe
- [ ] `INSTALACION_CARGADOR.md` existe
- [ ] `DIAGRAMAS_CARGADOR.md` existe
- [ ] Documentación es clara y útil

### 1️⃣2️⃣ Revisar Ejemplos

- [ ] `CrearOrdenComponentMejorado.jsx` es funcional
- [ ] `TestCargadorListaPersonas.jsx` es funcional
- [ ] Ejemplos en documentación funcionan
- [ ] Código está comentado

## 📝 Checklist de Código

### Backend

**ParseadorListaPersonas.php:**
- [ ] Método `parsear()` público y correcto
- [ ] Método `parsearLinea()` privado
- [ ] Método `parsearNombresApellidos()` privado
- [ ] Validaciones correctas
- [ ] Comentarios de PHPDoc presentes
- [ ] Retorna array válido

**ListaPersonasController.php:**
- [ ] Extiende Controller
- [ ] Método `parsearLista()` público
- [ ] Request validation presente
- [ ] Try-catch para errores
- [ ] Response JSON correcto
- [ ] Middleware auth:sanctum presente

**routes/api.php:**
- [ ] Ruta importada correctamente
- [ ] Controlador referenced correctamente
- [ ] Dentro de middleware auth:sanctum
- [ ] Método POST correcto

### Frontend

**CargadorListaPersonas.jsx:**
- [ ] Importa React hooks
- [ ] Importa axios
- [ ] Estados iniciales correctos
- [ ] Validaciones presentes
- [ ] Manejo de errores
- [ ] UI responsive
- [ ] Estilos Tailwind consistentes
- [ ] Comentarios explicativos

**useListaPersonas.js:**
- [ ] Hook exportado correctamente
- [ ] useCallback usado apropiadamente
- [ ] Formatea datos correctamente
- [ ] Retorna estructura esperada
- [ ] Sin efectos secundarios

**Ejemplos:**
- [ ] Código funcional
- [ ] Comentarios explicativos
- [ ] Importaciones correctas
- [ ] No tiene dependencias faltantes

## 🚀 Checklist Pre-Producción

- [ ] Código revisado
- [ ] Tests pasados
- [ ] Documentación completa
- [ ] Sin console.log() de debug
- [ ] Sin comentarios temporales
- [ ] HTTPS en producción
- [ ] Tokens seguros
- [ ] Backups realizados
- [ ] Equipo notificado
- [ ] Rollback plan existe

## 📊 Resultados esperados

| Test | Esperado | ✓ |
|------|----------|---|
| Endpoint existe | 200 OK | [ ] |
| Parseo simple | 2 campos | [ ] |
| Parseo complejo | 4 campos | [ ] |
| Búsqueda en BD | Encuentra | [ ] |
| Frontend carga | Sin errores | [ ] |
| Hook funciona | Formatea | [ ] |
| Integración | Funciona fluida | [ ] |
| Rendimiento | < 500ms | [ ] |
| Seguridad | CSRF OK | [ ] |
| Error handling | Muestra mensaje | [ ] |

## ✨ Después del Deploy

- [ ] Verificar en producción
- [ ] Monitorear logs
- [ ] Usuario final prueba
- [ ] Feedback recibido
- [ ] Bugs reportados (si hay)
- [ ] Documentación accesible
- [ ] Equipo capacitado

## 📞 Contacto en caso de problemas

| Problema | Solución |
|----------|----------|
| Endpoint no existe | Verificar ruta en api.php |
| No autentica | Revisar token, Sanctum config |
| Parsing mal | Revisar ParseadorListaPersonas |
| Frontend error | Ver consola F12 |
| BD no responde | Verificar conexión |
| CORS error | Revisar config/cors.php |

---

**Fecha de checklist**: 2026-01-13  
**Versión**: 1.0  
**Estado**: ✅ Listo para verificar


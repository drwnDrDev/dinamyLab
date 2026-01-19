# Documentación: Procesamiento de Exámenes por Lotes

## 📋 Resumen de Cambios

Se ha implementado un nuevo sistema de procesamiento de exámenes por lotes que permite:

1. ✅ Seleccionar un tipo de examen específico
2. ✅ Cargar automáticamente todos los procedimientos pendientes para ese examen
3. ✅ Seleccionar/deseleccionar múltiples procedimientos para procesamiento
4. ✅ Completar parámetros de forma unificada en una tabla
5. ✅ Guardar resultados para múltiples procedimientos simultáneamente

---

## 🔧 Archivos Modificados

### 1. Vista Principal: `resources/views/examenes/lote.blade.php`
**Cambios:**
- Vista completamente refactorizada
- Agregada tabla de procedimientos pendientes con opciones de selección
- Implementado sistema de checkbox para seleccionar múltiples procedimientos
- Agregada tabla de parámetros dinámicos con campos de entrada
- Formulario responsive con Tailwind CSS
- JavaScript interactivo para gestionar selecciones

**Características principales:**
- Checkbox individual para cada procedimiento
- Checkbox "Seleccionar Todo" para facilitar selecciones masivas
- Contador de procedimientos seleccionados
- Tabla de parámetros que se muestra solo cuando hay procedimientos seleccionados
- Placeholders con nombres de parámetros
- Estados visuales para procedimientos (colores según estado)

---

### 2. Controlador: `app/Http/Controllers/ExamenController.php`
**Cambios:**
- Agregado método `lote(Examen $examen)` para servir la vista de procesamiento por lotes
- Vinculación del controlador con la vista

**Código agregado:**
```php
/**
 * Show batch processing view for exam
 */
public function lote(Examen $examen)
{
    return view('examenes.lote', compact('examen'));
}
```

---

### 3. API Controller: `app/Http/Controllers/Api/ExamenesController.php`
**Cambios:**
- Agregado método `obtenerProcedimientosPendientes($examenId)` para obtener procedimientos pendientes
- Importado modelo `Procedimiento`
- Respuesta JSON estructurada con datos de paciente, orden y estado

**Método agregado:**
```php
/**
 * Obtener procedimientos pendientes para un examen específico
 */
public function obtenerProcedimientosPendientes($examenId)
{
    // Retorna lista de procedimientos pendientes con relaciones
}
```

---

### 4. API de Resultados: `app/Http/Controllers/Api/ResultadosController.php`
**Cambios:**
- Actualizado método `store()` para soportar solicitudes JSON
- Agregada detección de tipo de solicitud (JSON vs HTML)
- Importado modelo `Resultado`
- Soporte para procesamiento de múltiples parámetros simultáneamente

**Lógica implementada:**
- Si es JSON: Procesa resultados en lote y retorna JSON
- Si es formulario HTML: Procesa de forma tradicional con redirección

---

### 5. Rutas API: `routes/api.php`
**Cambios:**
- Agregada nueva ruta para obtener procedimientos pendientes

**Ruta agregada:**
```php
Route::get('procedimientos/examen/{examenId}/pendientes', 
    [ExamenesController::class, 'obtenerProcedimientosPendientes']);
```

---

### 6. Rutas Web: `routes/web.php`
**Cambios:**
- Agregada nueva ruta para acceder a la vista de lotes

**Ruta agregada:**
```php
Route::get('/examenes/{examen}/lote',[ExamenController::class,'lote'])->name('examenes.lote');
```

---

### 7. Vista Show: `resources/views/examenes/show.blade.php`
**Cambios:**
- Agregado botón "Procesar Lotes" en el header
- Botón redirige a la vista de lotes del examen

---

## 🌐 Endpoints API

### Obtener Procedimientos Pendientes
```http
GET /api/procedimientos/examen/{examenId}/pendientes
```

**Respuesta:**
```json
{
  "message": "Procedimientos pendientes obtenidos",
  "procedimientos": [
    {
      "id": 1,
      "orden_id": 101,
      "paciente_nombre": "Juan Perez",
      "paciente_documento": "1023456789",
      "fecha": "2024-01-20 10:30",
      "estado": "pendiente",
      "enviar": true
    }
  ]
}
```

### Guardar Resultados (JSON)
```http
POST /api/resultados/{procedimientoId}/store
Content-Type: application/json
X-CSRF-TOKEN: {token}

{
  "resultados": {
    "1": "valor1",
    "2": "valor2",
    "3": "valor3"
  }
}
```

**Respuesta:**
```json
{
  "message": "Resultados guardados correctamente",
  "procedimiento_id": 1,
  "estado": "terminado"
}
```

---

## 🎨 Interfaz de Usuario

### Tabla de Procedimientos
| Seleccionar | Paciente | Documento | Orden # | Fecha | Estado | Enviar |
|-----------|----------|-----------|---------|-------|--------|--------|
| ☑ | Juan Perez | 1023456789 | 101 | 2024-01-20 | Pendiente | Sí |
| ☐ | María García | 9876543210 | 102 | 2024-01-20 | Pendiente | Sí |

### Tabla de Parámetros
| Parámetro | Posición | Unidades | Resultado | Valor Referencia |
|-----------|----------|----------|-----------|------------------|
| Hemoglobina | 1 | g/dL | [INPUT] | 12-16 |
| Hematocrito | 2 | % | [INPUT] | 36-46 |
| Plaquetas | 3 | /µL | [INPUT] | 150-400 |

---

## 📱 Funcionalidad JavaScript

### Funciones principales:

1. **cargarProcedimientosPendientes()**
   - Obtiene procedimientos pendientes de la API
   - Renderiza tabla de procedimientos

2. **actualizarSeleccion(procedimientoId, seleccionado)**
   - Gestiona selección de procedimientos
   - Actualiza contador
   - Muestra/oculta formulario de parámetros

3. **renderizarParametros()**
   - Genera tabla de entrada de parámetros
   - Utiliza valores del examen cargado

4. **Envío de formulario**
   - Recolecta datos de todos los parámetros
   - Envía POST JSON para cada procedimiento seleccionado
   - Gestiona respuestas multiples en paralelo

---

## 🚀 Cómo Usar

### Acceso a la Vista
1. Navegar a `/examenes/{examen}` (vista de detalles del examen)
2. Hacer clic en el botón azul **"Procesar Lotes"**
3. Sistema carga automáticamente procedimientos pendientes

### Procesamiento
1. Seleccionar procedimientos individuales o todos
2. Ver contador actualizado
3. Formulario aparece automáticamente
4. Completar valores para cada parámetro
5. Hacer clic en **"Guardar Resultados"**
6. Sistema procesa todos los procedimientos simultáneamente

### Validaciones
- ✓ Requiere al menos un procedimiento seleccionado
- ✓ Todos los parámetros son campos requeridos
- ✓ Validación cliente y servidor
- ✓ Notificaciones de éxito/error

---

## 📊 Estados de Procedimientos

Los procedimientos cambian de estado automáticamente:
- **Pendiente** → **Terminado** (después de guardar resultados)
- Los parámetros se guardan con referencia a cada procedimiento
- El empleado actual se asigna automáticamente

---

## 🔒 Seguridad

- ✅ Requiere autenticación (middleware auth)
- ✅ Token CSRF obligatorio
- ✅ Validación de modelos (implicit route model binding)
- ✅ Respuestas JSON estructuradas
- ✅ Manejo de errores con try-catch

---

## 💾 Base de Datos

### Relaciones Utilizadas:
- Examen → Parametros
- Examen → Procedimientos → Paciente
- Procedimiento → Resultado
- Parametro → ValorReferencia

### Campos Importantes:
- `procedimientos.estado` - Estado del procedimiento
- `resultados.resultado` - Valor del resultado
- `procedimientos.empleado_id` - Empleado que ingresó datos
- `procedimientos.fecha` - Fecha de ingreso

---

## 🐛 Posibles Mejoras Futuras

1. Agregar filtros por fecha, paciente, estado
2. Opción de guardar como borrador
3. Validación de rangos de valores según parámetro
4. Descarga de reportes PDF
5. Historial de cambios
6. Búsqueda de procedimientos específicos
7. Observaciones adicionales por procedimiento

---

## 📝 Notas

- La carga es dinámica vía JavaScript/FETCH
- Los procedimientos deben estar en estado "pendiente"
- Solo se cargan parámetros asociados al examen
- El formulario es responsive en mobile
- Colores de estado facilitan visualización

---

Última actualización: 20 de Enero de 2025

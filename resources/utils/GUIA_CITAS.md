# 📋 Sistema de Registro de Citas Anónimo

## 📖 Introducción

Sistema completo para el registro anónimo de citas médicas y la gestión de pre-registros por parte del equipo administrativo.

**Características principales**:
- Registro sin autenticación requerida
- Código de confirmación único por cita
- Panel de gestión para empleados
- Filtros y búsqueda avanzada
- Estados de cita (pendiente, confirmada, procesada, cancelada)

---

## 🚀 Empezar

### Para Usuarios (Registro Anónimo)

1. Accede a `/citas/registrar`
2. Completa el formulario con tus datos
3. Recibirás un código de confirmación
4. Confirma tu cita con el código
5. ¡Listo! Tu cita está registrada

### Para Empleados (Gestión)

1. Inicia sesión en el sistema
2. Accede a `/citas`
3. Verás un listado de todos los pre-registros
4. Puedes:
   - Filtrar por estado o rango de fechas
   - Ver detalles completos
   - Cambiar el estado de la cita
   - Cancelar citas si es necesario

---

## 📱 URLs Disponibles

### Públicas (Sin autenticación)

| URL | Descripción |
|-----|-------------|
| `/citas/registrar` | Formulario de registro |
| `/citas/confirmacion/{codigo}` | Página de confirmación |
| `/citas/exito` | Página de éxito |

### Autenticadas (Solo empleados)

| URL | Descripción |
|-----|-------------|
| `/citas` | Listado de pre-registros |
| `/citas/{id}` | Detalles de un pre-registro |

---

## 📝 Campos del Formulario

### Sección 1: Datos Personales

| Campo | Tipo | Requerido | Notas |
|-------|------|-----------|-------|
| Nombres Completos | Texto | ✅ | Máx. 255 caracteres |
| Tipo de Documento | Selecto | ✅ | CC, CE, TI, PA, PE |
| Número de Documento | Texto | ✅ | Máx. 50 caracteres |
| Teléfono | Teléfono | ✅ | Máx. 20 caracteres |
| Email | Email | ✅ | Válido y único |

### Sección 2: Información de la Cita

| Campo | Tipo | Requerido | Notas |
|-------|------|-----------|-------|
| Fecha Deseada | Fecha | ✅ | A partir de hoy |
| Hora Deseada | Hora | ✅ | Formato HH:MM |
| Sede | Selecto | ❌ | Opcional |
| Modalidad | Selecto | ❌ | Opcional |
| Motivo | Texto | ❌ | Máx. 500 caracteres |
| Observaciones | Texto largo | ❌ | Máx. 1000 caracteres |

---

## 🔄 Estados de Cita

```
┌─────────────┐
│  PENDIENTE  │ ← Estado inicial
└─────────────┘
      │
      ↓
┌─────────────┐
│ CONFIRMADA  │ ← Confirmada por usuario
└─────────────┘
      │
      ↓
┌─────────────┐
│  PROCESADA  │ ← Procesada por empleado
└─────────────┘

   CANCELADA  ← Puede pasar desde cualquier estado
```

---

## 🎯 Flujo Completo

### 1. Usuario Visita `/citas/registrar`

```
GET /citas/registrar
├─ Carga sedes activas
├─ Carga modalidades activas
└─ Muestra formulario
```

### 2. Usuario Completa Formulario y Envía

```
POST /citas/registrar
├─ Valida todos los campos
├─ Genera código de confirmación (8 caracteres, único)
├─ Crea pre-registro en base de datos
└─ Redirige a /citas/confirmacion/{codigo}
```

### 3. Usuario Confirma su Cita

```
GET /citas/confirmacion/{codigo}
├─ Muestra código y resumen de datos
└─ Botón para confirmar

POST /citas/confirmar/{codigo}
├─ Actualiza estado a "confirmada"
├─ Registra fecha_confirmacion
└─ Redirige a /citas/exito
```

### 4. Empleado Gestiona Citas

```
GET /citas
├─ Listado paginado (15 por página)
├─ Filtros por estado y fecha
└─ Tabla con acciones

GET /citas/{id}
├─ Detalles completos
├─ Información personal
├─ Información de la cita
├─ Datos de contacto rápido
└─ Opciones para cambiar estado o cancelar
```

---

## 🛡️ Validaciones

### Frontend (React)
- Campos requeridos en rojo si están vacíos
- Validación de email en tiempo real
- Fecha debe ser hoy o posterior
- Hora en formato válido (HH:MM)
- Mensajes de error específicos

### Backend (Laravel)
- Validación de todos los campos
- Email válido y único
- Fecha no en el pasado
- Código de confirmación único
- Autorización con políticas

---

## 🔐 Seguridad

### Registro Anónimo
- ✅ No requiere autenticación
- ✅ CSRF protection automático
- ✅ Rate limiting recomendado (TODO)
- ✅ Código de confirmación único

### Panel de Gestión
- ✅ Requiere autenticación obligatoria
- ✅ Autorización con políticas
- ✅ Validación de permisos para actualizar/eliminar
- ✅ Auditoría de cambios (soft deletes)

---

## 📊 Ejemplo de Código de Confirmación

```
Ejemplo: A7K9P2M5

Características:
- 8 caracteres alfanuméricos
- Generado automáticamente
- Único para cada pre-registro
- Sin caracteres ambiguos (0/O, 1/I, etc.)
```

---

## 🌍 Internacionalización

Textos traducidos al español en `lang/es/citas.php`:

- Títulos y descripciones
- Labels de campos
- Mensajes de validación
- Estados de cita
- Botones de acción

---

## 🎨 Estilos y Tema

### Colores por Estado

| Estado | Color | Hexadecimal |
|--------|-------|-------------|
| Pendiente | Amarillo | #FBBF24 |
| Confirmada | Verde | #10B981 |
| Procesada | Azul | #3B82F6 |
| Cancelada | Rojo | #EF4444 |

### Componentes
- Formularios responsivos
- Tablas con scroll horizontal
- Paginación automática
- Badges de estado
- Iconos de acción

---

## 🐛 Solución de Problemas

### "El código de confirmación no es válido"
- Verifica que esté escrito correctamente
- Mayúsculas y minúsculas no importan
- El código debe estar completo (8 caracteres)

### "El email ya existe"
- Usa otro email o contacta al administrador
- Si es la misma cita, intenta confirmar nuevamente

### "No puedo ver los pre-registros"
- Debes estar autenticado como empleado
- Verifica que tengas permisos de visualización

### "El estado no cambió"
- Recarga la página
- Verifica que tengas permisos de actualización

---

## 📞 Contacto y Soporte

Para consultas sobre citas:
- Email: contacto@laboratorio.com
- Teléfono: +57 (XXX) XXXX-XXXX

Para reportar problemas técnicos:
- Contacta al equipo de TI

---

## 📌 Notas Importantes

1. **Email de confirmación** (TODO): Implementar envío automático de email
2. **SMS** (Opcional): Agregar confirmación por SMS
3. **Recordatorios**: Sistema de notificación 24h antes
4. **Sincronización**: Integrar con calendario empresarial

---

## 🚀 Futuras Mejoras

- [ ] Envío de emails automático
- [ ] Notificaciones por SMS
- [ ] Reschedule de citas
- [ ] Dashboard de estadísticas
- [ ] Integración con Google Calendar
- [ ] Sistema de pagos/reserva
- [ ] Encuestas de satisfacción
- [ ] Reportes avanzados

---

**Versión**: 1.0
**Última actualización**: 14 de enero de 2026
**Autor**: Sistema de Gestión de Laboratorio Clínico

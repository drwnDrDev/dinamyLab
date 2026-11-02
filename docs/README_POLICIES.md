# Sistema de Policies para Control de Acceso Multi-Empresa

## Resumen

Se ha implementado un sistema de policies completo para garantizar que los usuarios de una empresa no puedan acceder a recursos de otra empresa.

## Archivos Creados

### 1. Core - Trait Reutilizable
- **`app/Policies/Traits/ChecksEmpresaAccess.php`**
  - Métodos reutilizables para verificar acceso por empresa
  - `belongsToSameEmpresa()` - Verifica si usuario y recurso pertenecen a la misma empresa
  - `getUserEmpresaId()` - Obtiene la empresa del usuario
  - `canAccessEmpresaResource()` - Verificación alternativa con soporte para relaciones

### 2. Policies
- **`app/Policies/EmpresaPolicy.php`**
  - Control de acceso para el modelo Empresa
  - Los usuarios solo pueden ver su propia empresa
  
- **`app/Policies/SedePolicy.php`** (Ejemplo)
  - Control de acceso para el modelo Sede
  - Demuestra cómo usar el trait en otras policies

### 3. Configuración
- **`app/Providers/AppServiceProvider.php`**
  - Registro de las policies en el sistema

### 4. Documentación
- **`docs/SISTEMA_POLICIES_EMPRESA.md`**
  - Documentación completa del sistema
  - Cómo crear nuevas policies
  - Requisitos de los modelos
  - Guía de uso

- **`docs/EJEMPLOS_IMPLEMENTACION_POLICIES.md`**
  - Ejemplos prácticos de implementación
  - Refactorización de controladores
  - Uso en vistas Blade
  - Global Scopes para filtrado automático
  - Middleware personalizado

### 5. Tests
- **`tests/Feature/Policies/EmpresaPolicyTest.php`**
  - Suite completa de tests
  - Verifica aislamiento entre empresas
  - Valida permisos y roles

## Cómo Usar

### En Controladores:
```php
public function show(Sede $sede)
{
    $this->authorize('view', $sede);
    return view('sedes.show', compact('sede'));
}
```

### En Vistas:
```blade
@can('update', $sede)
    <a href="{{ route('sedes.edit', $sede) }}">Editar</a>
@endcan
```

### Crear Nueva Policy:
```php
use App\Policies\Traits\ChecksEmpresaAccess;

class MiPolicy
{
    use ChecksEmpresaAccess;
    
    public function view(User $user, Model $recurso): bool
    {
        return $this->belongsToSameEmpresa($user, $recurso);
    }
}
```

## Estructura de Relaciones

```
User → Empleado → Empresa
                    ↓
            Sedes, Recursos, etc.
```

## Principios de Seguridad

1. ✅ **Verificación en Backend** - Nunca confiar solo en el frontend
2. ✅ **Filtrado de Consultas** - Filtrar por empresa_id en todas las queries
3. ✅ **Autorización en Controladores** - Usar `$this->authorize()` en cada acción
4. ✅ **Protección en Vistas** - Usar `@can` para ocultar acciones no permitidas
5. ✅ **Prevención de Cambios** - No permitir modificar empresa_id
6. ✅ **Asignación Automática** - Asignar empresa_id al crear recursos

## Próximos Pasos

Para aplicar a otros modelos:

1. Crear la policy usando el trait `ChecksEmpresaAccess`
2. Registrar en `AppServiceProvider`
3. Agregar `$this->authorize()` en el controlador
4. Filtrar consultas por `empresa_id`
5. Actualizar vistas con `@can`
6. Crear tests

## Soporte

Ver documentación completa en:
- [Sistema de Policies](SISTEMA_POLICIES_EMPRESA.md)
- [Ejemplos de Implementación](EJEMPLOS_IMPLEMENTACION_POLICIES.md)

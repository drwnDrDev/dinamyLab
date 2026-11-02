# Sistema de Policies para Control de Acceso por Empresa

Este documento explica cómo funciona el sistema de policies para asegurar que los usuarios de una empresa no puedan acceder a los recursos de otra empresa.

## Estructura

### 1. Trait `ChecksEmpresaAccess`

Ubicación: `app/Policies/Traits/ChecksEmpresaAccess.php`

Este trait proporciona métodos reutilizables para verificar el acceso de usuarios a recursos basados en la empresa:

#### Métodos disponibles:

- **`belongsToSameEmpresa(User $user, Model $resource): bool`**
  
  Verifica si el usuario pertenece a la misma empresa que el recurso.
  
  ```php
  if ($this->belongsToSameEmpresa($user, $sede)) {
      // El usuario puede acceder a esta sede
  }
  ```

- **`getUserEmpresaId(User $user): ?int`**
  
  Obtiene el ID de la empresa del usuario autenticado.
  
  ```php
  $empresaId = $this->getUserEmpresaId($user);
  ```

- **`canAccessEmpresaResource(User $user, Model $resource): bool`**
  
  Alternativa que maneja tanto recursos con `empresa_id` directo como con relación `empresa()`.
  
  ```php
  if ($this->canAccessEmpresaResource($user, $recurso)) {
      // Acceso permitido
  }
  ```

### 2. Policies Implementadas

#### EmpresaPolicy
Control de acceso para el modelo `Empresa`:
- Los usuarios solo pueden ver su propia empresa
- Solo admins pueden crear/eliminar empresas
- Los usuarios con permiso pueden editar su empresa

#### SedePolicy (Ejemplo)
Control de acceso para el modelo `Sede`:
- Los usuarios solo pueden ver sedes de su empresa
- Solo pueden crear/editar/eliminar sedes de su empresa
- Requiere permisos específicos además de pertenecer a la empresa

## Cómo Usar en Nuevas Policies

### Paso 1: Crear la Policy

```php
<?php

namespace App\Policies;

use App\Models\MiModelo;
use App\Models\User;
use App\Policies\Traits\ChecksEmpresaAccess;

class MiModeloPolicy
{
    use ChecksEmpresaAccess;

    public function view(User $user, MiModelo $modelo): bool
    {
        // Verificar que el recurso pertenece a la empresa del usuario
        return $this->belongsToSameEmpresa($user, $modelo);
    }

    public function update(User $user, MiModelo $modelo): bool
    {
        // Combinar verificación de empresa con permisos
        if (!$this->belongsToSameEmpresa($user, $modelo)) {
            return false;
        }

        return $user->can('editar_modelo');
    }
}
```

### Paso 2: Registrar en AppServiceProvider

Agregar la policy en `app/Providers/AppServiceProvider.php`:

```php
protected $policies = [
    MiModelo::class => MiModeloPolicy::class,
];
```

### Paso 3: Usar en Controladores

```php
public function show(MiModelo $modelo)
{
    // Laravel automáticamente verificará la policy
    $this->authorize('view', $modelo);

    return view('mi-modelo.show', compact('modelo'));
}

public function update(Request $request, MiModelo $modelo)
{
    $this->authorize('update', $modelo);

    // Lógica de actualización
}
```

### Paso 4: Usar en Vistas Blade

```blade
@can('view', $modelo)
    {{-- Contenido visible solo si tiene acceso --}}
@endcan

@can('update', $modelo)
    <a href="{{ route('modelo.edit', $modelo) }}">Editar</a>
@endcan
```

### Paso 5: Filtrar Consultas por Empresa

En los controladores, asegúrate de filtrar las consultas:

```php
public function index()
{
    $empresaId = auth()->user()->empleado->empresa_id;
    
    $modelos = MiModelo::where('empresa_id', $empresaId)->get();
    
    return view('mi-modelo.index', compact('modelos'));
}
```

O mejor aún, usa un scope en el modelo:

```php
// En el modelo MiModelo
public function scopeDeEmpresa($query, $empresaId)
{
    return $query->where('empresa_id', $empresaId);
}

// En el controlador
$modelos = MiModelo::deEmpresa(auth()->user()->empleado->empresa_id)->get();
```

## Requisitos del Modelo

Para que un modelo pueda usar estas policies, debe:

1. Tener un campo `empresa_id` en la base de datos, O
2. Tener una relación `empresa()` definida

Ejemplo:

```php
class MiModelo extends Model
{
    protected $fillable = ['nombre', 'empresa_id'];

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }
}
```

## Estructura de Relaciones

```
User (Usuario)
  └─ hasOne → Empleado
                └─ belongsTo → Empresa
                                  └─ hasMany → Sedes, Empleados, etc.
```

## Permisos Adicionales

Además de verificar la empresa, puedes combinar con el sistema de permisos de Spatie:

```php
public function create(User $user): bool
{
    return $this->getUserEmpresaId($user) !== null 
        && $user->can('crear_recursos');
}
```

## Testing

Ejemplo de test para verificar la policy:

```php
public function test_usuario_no_puede_ver_sede_de_otra_empresa()
{
    $empresa1 = Empresa::factory()->create();
    $empresa2 = Empresa::factory()->create();
    
    $empleado = Empleado::factory()->create(['empresa_id' => $empresa1->id]);
    $user = $empleado->user;
    
    $sedeOtraEmpresa = Sede::factory()->create(['empresa_id' => $empresa2->id]);
    
    $this->actingAs($user);
    
    $response = $this->get(route('sedes.show', $sedeOtraEmpresa));
    $response->assertForbidden();
}
```

## Notas Importantes

1. **Siempre verifica primero la empresa** antes de verificar otros permisos
2. **Filtra las consultas** en el backend para evitar exposición de datos
3. **No confíes solo en el frontend** - siempre valida en el servidor
4. **Usa `$this->authorize()`** en los controladores para aplicar las policies automáticamente
5. **Los usuarios sin empleado asociado** no podrán acceder a ningún recurso de empresa

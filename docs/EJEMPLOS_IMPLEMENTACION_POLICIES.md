# Ejemplos de Implementación de Policies

## Ejemplo 1: Refactorizar EmpresaController

### Antes (código actual):
```php
public function show()
{
    if(!Auth::user()->hasRole('admin')){
        abort(403, 'No autorizado');
    }
    $empleado = Auth::user()->empleado;
    if (!$empleado) {
        abort(403, 'No autorizado');
    }

    $empresa = $empleado->empresa;

    return view('empresa.show', compact('empresa'));
}
```

### Después (usando Policy):
```php
public function show()
{
    $empleado = Auth::user()->empleado;
    
    if (!$empleado) {
        abort(403, 'No autorizado');
    }

    $empresa = $empleado->empresa;
    
    // La policy verificará automáticamente si el usuario puede ver esta empresa
    $this->authorize('view', $empresa);

    return view('empresa.show', compact('empresa'));
}
```

### O más limpio con Model Binding:
```php
public function show(Empresa $empresa)
{
    // La policy verificará automáticamente
    $this->authorize('view', $empresa);

    return view('empresa.show', compact('empresa'));
}
```

## Ejemplo 2: SedeController con Protección Completa

```php
<?php

namespace App\Http\Controllers;

use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SedeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Verificar que el usuario puede ver sedes
        $this->authorize('viewAny', Sede::class);

        // Obtener solo las sedes de la empresa del usuario
        $empresaId = Auth::user()->empleado->empresa_id;
        $sedes = Sede::where('empresa_id', $empresaId)->get();

        return view('sedes.index', compact('sedes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Sede::class);

        return view('sedes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Sede::class);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo_prestador' => 'required|string|max:50',
        ]);

        // Asegurar que se crea en la empresa del usuario
        $validated['empresa_id'] = Auth::user()->empleado->empresa_id;

        $sede = Sede::create($validated);

        return redirect()->route('sedes.show', $sede)
            ->with('success', 'Sede creada exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sede $sede)
    {
        // Verificar que la sede pertenece a la empresa del usuario
        $this->authorize('view', $sede);

        return view('sedes.show', compact('sede'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sede $sede)
    {
        $this->authorize('update', $sede);

        return view('sedes.edit', compact('sede'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sede $sede)
    {
        $this->authorize('update', $sede);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo_prestador' => 'required|string|max:50',
        ]);

        // NO permitir cambiar la empresa_id
        unset($validated['empresa_id']);

        $sede->update($validated);

        return redirect()->route('sedes.show', $sede)
            ->with('success', 'Sede actualizada exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sede $sede)
    {
        $this->authorize('delete', $sede);

        $sede->delete();

        return redirect()->route('sedes.index')
            ->with('success', 'Sede eliminada exitosamente');
    }
}
```

## Ejemplo 3: Usando Middleware para Rutas

En `routes/web.php`:

```php
// Grupo de rutas que requieren autenticación y pertenencia a una empresa
Route::middleware(['auth'])->group(function () {
    
    // Rutas de Empresa
    Route::get('/empresa', [EmpresaController::class, 'show'])
        ->name('empresa.show');
    
    Route::get('/empresa/edit', [EmpresaController::class, 'edit'])
        ->name('empresa.edit')
        ->can('update', 'App\Models\Empresa'); // Verificar con policy
    
    // Rutas de Sedes - todas protegidas por policies
    Route::resource('sedes', SedeController::class);
});
```

## Ejemplo 4: Protección en Vistas Blade

### resources/views/empresa/show.blade.php

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $empresa->razon_social }}</h1>
    
    <div class="card">
        <div class="card-body">
            <p><strong>NIT:</strong> {{ $empresa->nit }}</p>
            
            @can('update', $empresa)
                <a href="{{ route('empresa.edit', $empresa) }}" class="btn btn-primary">
                    Editar Empresa
                </a>
            @endcan
            
            @can('delete', $empresa)
                <form action="{{ route('empresa.destroy', $empresa) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" 
                        onclick="return confirm('¿Está seguro?')">
                        Eliminar
                    </button>
                </form>
            @endcan
        </div>
    </div>
    
    <h2 class="mt-4">Sedes</h2>
    
    @can('create', App\Models\Sede::class)
        <a href="{{ route('sedes.create') }}" class="btn btn-success mb-3">
            Nueva Sede
        </a>
    @endcan
    
    <div class="row">
        @foreach($empresa->sedes as $sede)
            <div class="col-md-4">
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">{{ $sede->nombre }}</h5>
                        <p class="card-text">{{ $sede->codigo_prestador }}</p>
                        
                        @can('view', $sede)
                            <a href="{{ route('sedes.show', $sede) }}" class="btn btn-sm btn-info">
                                Ver Detalles
                            </a>
                        @endcan
                        
                        @can('update', $sede)
                            <a href="{{ route('sedes.edit', $sede) }}" class="btn btn-sm btn-primary">
                                Editar
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
```

## Ejemplo 5: Scope Global para Filtrado Automático

Puedes agregar un Global Scope a los modelos para filtrar automáticamente por empresa:

### app/Models/Scopes/EmpresaScope.php

```php
<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class EmpresaScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        // Solo aplicar si hay usuario autenticado
        if (Auth::check() && Auth::user()->empleado) {
            $empresaId = Auth::user()->empleado->empresa_id;
            
            if ($empresaId) {
                $builder->where($model->getTable() . '.empresa_id', $empresaId);
            }
        }
    }
}
```

### Uso en el Modelo:

```php
<?php

namespace App\Models;

use App\Models\Scopes\EmpresaScope;
use Illuminate\Database\Eloquent\Model;

class Sede extends Model
{
    // ... resto del código

    protected static function booted()
    {
        // Aplicar el scope automáticamente
        static::addGlobalScope(new EmpresaScope());
        
        // Al crear, asignar automáticamente la empresa del usuario
        static::creating(function ($sede) {
            if (!$sede->empresa_id && Auth::check() && Auth::user()->empleado) {
                $sede->empresa_id = Auth::user()->empleado->empresa_id;
            }
        });
    }
}
```

Con este scope, todas las consultas filtrarán automáticamente por empresa:

```php
// Esto solo retornará sedes de la empresa del usuario autenticado
$sedes = Sede::all();

// Para obtener todas las sedes (solo admin)
$todasLasSedes = Sede::withoutGlobalScope(EmpresaScope::class)->get();
```

## Ejemplo 6: Middleware Personalizado

Crear un middleware para verificar que el usuario pertenece a una empresa:

```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasEmpresa
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->user() || !$request->user()->empleado || !$request->user()->empleado->empresa_id) {
            abort(403, 'Debe estar asignado a una empresa para acceder a este recurso.');
        }

        return $next($request);
    }
}
```

Registrar en `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    // ... otros middlewares
    'empresa' => \App\Http\Middleware\EnsureUserHasEmpresa::class,
];
```

Usar en rutas:

```php
Route::middleware(['auth', 'empresa'])->group(function () {
    Route::resource('sedes', SedeController::class);
});
```

## Resumen de Mejores Prácticas

1. **Siempre usar `$this->authorize()`** en los controladores
2. **Filtrar consultas** por empresa_id en el backend
3. **Usar Global Scopes** para filtrado automático
4. **Validar en vistas** con `@can` para ocultar botones
5. **No permitir cambiar** `empresa_id` en actualizaciones
6. **Asignar automáticamente** `empresa_id` al crear recursos
7. **Registrar todas las policies** en AppServiceProvider
8. **Combinar policies con permisos** de Spatie cuando sea necesario

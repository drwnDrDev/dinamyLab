<?php

namespace App\Providers;

use App\Models\Empresa;
use App\Models\Procedimiento;
use App\Models\Sede;
use App\Observers\ProcedimientoObserver;
use App\Policies\EmpresaPolicy;
use App\Policies\SedePolicy;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Empresa::class => EmpresaPolicy::class,
        Sede::class => SedePolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Procedimiento::observe(ProcedimientoObserver::class);

        // Registrar políticas
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }

        // Invalidar la caché de catálogos cuando cambie alguno de sus modelos.
        // Se registran los listeners sin instanciar los modelos: sus booted()
        // consultan la base de datos y aún no está disponible al arrancar.
        $modelosCatalogo = [
            \App\Models\ServicioHabilitado::class,
            \App\Models\ViaIngreso::class,
            \App\Models\TipoAfiliacion::class,
            \App\Models\CausaExterna::class,
            \App\Models\ModalidadAtencion::class,
            \App\Models\Finalidad::class,
            \App\Models\TipoDocumento::class,
            \App\Models\Pais::class,
            \App\Models\Municipio::class,
            \App\Models\Eps::class,
        ];

        foreach ($modelosCatalogo as $modelo) {
            Event::listen("eloquent.saved: {$modelo}", [\App\Observers\CatalogoObserver::class, 'saved']);
            Event::listen("eloquent.deleted: {$modelo}", [\App\Observers\CatalogoObserver::class, 'deleted']);
        }
    }
}

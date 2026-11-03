<?php

namespace App\Providers;

use App\Models\Empresa;
use App\Models\Sede;
use App\Policies\EmpresaPolicy;
use App\Policies\SedePolicy;
use Illuminate\Support\ServiceProvider;
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
        // Registrar políticas
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}


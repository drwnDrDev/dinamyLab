<?php

namespace App\Observers;

use App\Support\CatalogsCache;
use Illuminate\Database\Eloquent\Model;

class CatalogoObserver
{
    public function saved(Model $model): void
    {
        if ($model->wasRecentlyCreated) {
            CatalogsCache::forget();
            return;
        }

        // ServicioHabilitado::resetearNiveles() guarda `nivel` en cada request.
        // Ignorar ese cambio evita invalidar la caché en cada petición.
        if (array_diff(array_keys($model->getChanges()), ['nivel']) !== []) {
            CatalogsCache::forget();
        }
    }

    public function deleted(Model $model): void
    {
        CatalogsCache::forget();
    }
}

<?php

namespace App\Observers;

use App\Models\Procedimiento;

class ProcedimientoObserver
{
    public function saved(Procedimiento $procedimiento): void
    {
        $procedimiento->orden?->sincronizarEstado();
    }

    public function deleted(Procedimiento $procedimiento): void
    {
        $procedimiento->orden?->sincronizarEstado();
    }
}

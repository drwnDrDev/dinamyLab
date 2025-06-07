<?php

namespace App\Jobs;

use App\Models\Procedimiento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use App\Estado; 

class RevertirEstadoProcedimientos implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $limite = now()->subHours(24);

        // Revertir a 'pendiente' si han pasado 24h y no tiene resultado
        $revertidos = Procedimiento::where('estado', Estado::PROCESO)
            ->where('updated_at', '<=', $limite)
            ->whereDoesntHave('resultado')
            ->update(['estado' => 'pendiente']);

        Log::info("Procedimientos revertidos a 'pendiente': $revertidos");

        // Cambiar a 'terminado' si tiene resultado
        $terminados = Procedimiento::whereNotIn('estado', [Estado::TERMINADO, Estado::ENTREGADO, Estado::ANULADO])
            ->whereHas('resultado')
            ->update(['estado' => Estado::TERMINADO]);

        Log::info("Procedimientos actualizados a 'terminado': $terminados");
    }
}

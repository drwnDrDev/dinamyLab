<?php

namespace App\Jobs;

use App\Estado;
use App\Models\Orden;
use App\Models\Procedimiento;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class RevertirEstadoProcedimientos implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
        $limite = Carbon::now()->subHours(24); // 24 horas atrás
        $ordenesARevisar = Procedimiento::query()
            ->where('estado', Estado::PROCESO->value)
            ->where('updated_at', '<=', $limite)
            ->whereDoesntHave('resultado')
            ->pluck('orden_id');

        $revertidos = Procedimiento::where('estado', Estado::PROCESO->value)
            ->where('updated_at', '<=', $limite)
            ->whereDoesntHave('resultado')
            ->update(['estado' => Estado::PENDIENTE->value]);
        if ($revertidos !== 0) {
            \Log::info("Se han revertido $revertidos procedimientos a 'pendiente'.");
        }

        $procedimientosPorResultados = Procedimiento::query()
            ->whereNotIn('estado', [
                Estado::TERMINADO->value,
                Estado::ENTREGADO->value,
                Estado::ANULADO->value,
            ])
            ->whereHas('resultado')
            ->pluck('orden_id');

        $ordenesARevisar = $ordenesARevisar->merge($procedimientosPorResultados)->unique();
        $terminados = Procedimiento::whereNotIn('estado', [
            Estado::TERMINADO->value,
            Estado::ENTREGADO->value,
            Estado::ANULADO->value,
        ])
            ->whereHas('resultado')
            ->update(['estado' => Estado::TERMINADO->value]);
        if ($terminados !== 0) {
            \Log::info("Procedimientos actualizados a 'terminado': $terminados");
        }

        Orden::query()->whereKey($ordenesARevisar)->get()->each->sincronizarEstado();
    }
}

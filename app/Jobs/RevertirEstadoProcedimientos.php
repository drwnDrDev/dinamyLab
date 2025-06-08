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
     
        $revertidos = Procedimiento::where('estado', Estado::PROCESO)
            ->where('updated_at', '<=', $limite)
            ->whereNull('resultados')
            ->update(['estado' => 'pendiente']);
        if($revertidos !== 0) {
            \Log::info("Se han revertido $revertidos procedimientos a 'pendiente'.");
        }

        $terminados = Procedimiento::whereNotIn('estado', [Estado::TERMINADO, Estado::ENTREGADO, Estado::ANULADO])
            ->whereNotNull('resultados')
            ->update(['estado' => Estado::TERMINADO]);  
        if($terminados !== 0) {
                \Log::info("Procedimientos actualizados a 'terminado': $terminados");
            }
    }
}

<?php

namespace App\Console;



use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{   
    protected $middlewareGroups = [
    'web' => [
        // ...existing code...
        \App\Http\Middleware\HandleInertiaRequests::class,
    ],
];

    protected function schedule(Schedule $schedule)
    {
        // Ejecutar el Job cada minuto para pruebas
        $schedule->job(new RevertirEstadoProcedimientos)->everyMinute();
        
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

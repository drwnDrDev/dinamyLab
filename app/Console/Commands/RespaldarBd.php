<?php

namespace App\Console\Commands;

use App\Services\RespaldarBaseDeDatos;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class RespaldarBd extends Command
{
    protected $signature = 'db:backup';

    protected $description = 'Respalda la base MySQL en un SQL comprimido y depura copias vencidas';

    public function handle(RespaldarBaseDeDatos $backups): int
    {
        try {
            $path = $backups->ejecutar();
        } catch (Throwable $exception) {
            // No registrar stderr de mysqldump: puede contener información sensible.
            Log::error('Falló el respaldo de la base de datos.', [
                'instance' => config('instancias.actual'),
                'reason' => $exception->getMessage(),
            ]);
            $this->error('Falló el respaldo: '.$exception->getMessage());

            return self::FAILURE;
        }

        Log::info('Respaldo de base de datos completado.', ['file' => basename($path)]);
        $this->info('Respaldo creado: '.$path);

        return self::SUCCESS;
    }
}

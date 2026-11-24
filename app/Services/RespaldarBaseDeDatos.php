<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class RespaldarBaseDeDatos
{
    public function ejecutar(): string
    {
        // getConfig() resuelve también DB_URL sin abrir una conexión PDO.
        $connection = DB::connection()->getConfig();

        if (($connection['driver'] ?? null) !== 'mysql') {
            throw new RuntimeException('Los respaldos automáticos requieren una conexión MySQL.');
        }

        if (empty($connection['database']) || (int) config('backups.retention_days') < 1 || (int) config('backups.timeout') < 1) {
            throw new RuntimeException('Revise la BD, DB_BACKUP_RETENTION_DAYS y DB_BACKUP_TIMEOUT.');
        }

        $directory = $this->directorio();
        $identity = implode('|', [config('instancias.actual'), $connection['host'] ?? '', $connection['port'] ?? '', $connection['unix_socket'] ?? '', $connection['database']]);
        $prefix = 'db-'.(Str::slug(config('instancias.actual') ?: 'local') ?: 'instancia').'-'.substr(hash('sha256', $identity), 0, 12).'-';
        $lock = fopen($directory.'/'.$prefix.'lock', 'c');

        if ($lock === false) {
            throw new RuntimeException('No se pudo abrir el bloqueo del respaldo.');
        }

        try {
            if (! flock($lock, LOCK_EX | LOCK_NB)) {
                throw new RuntimeException('Ya hay un respaldo en ejecución para esta base de datos.');
            }

            $path = $this->exportar($directory, $prefix, $connection);
            // Se depura únicamente después de publicar una copia completa.
            $this->depurar($directory, $prefix, $path);

            return $path;
        } finally {
            fclose($lock);
        }
    }

    private function directorio(): string
    {
        $directory = (string) config('backups.path');

        if ($directory === '') {
            throw new RuntimeException('DB_BACKUP_PATH no puede estar vacío.');
        }

        if (! is_dir($directory) && ! mkdir($directory, 0700, true) && ! is_dir($directory)) {
            throw new RuntimeException('No se pudo crear el directorio de respaldos.');
        }

        $directory = realpath($directory);

        foreach ([public_path(), storage_path('app/public')] as $public) {
            $public = realpath($public);

            if ($public !== false && ($directory === $public || str_starts_with($directory, $public.DIRECTORY_SEPARATOR))) {
                throw new RuntimeException('DB_BACKUP_PATH debe estar fuera de los directorios públicos.');
            }
        }

        if ($directory === false || ! is_writable($directory)) {
            throw new RuntimeException('El directorio de respaldos no tiene permisos de escritura.');
        }

        return $directory;
    }

    private function exportar(string $directory, string $prefix, array $connection): string
    {
        $path = $directory.'/'.$prefix.now()->format('Ymd_His_u').'-'.bin2hex(random_bytes(4)).'.sql.gz';
        $partial = $path.'.part';
        $credentials = null;
        $gzip = null;
        $process = null;
        $previousMask = umask(0077);

        try {
            $credentials = tempnam($directory, '.mysql-');

            if ($credentials === false) {
                throw new RuntimeException('No se pudo crear el archivo temporal de conexión.');
            }

            $options = [
                'user' => $connection['username'],
                'password' => $connection['password'],
                'host' => $connection['host'],
                'port' => $connection['port'],
                'default-character-set' => $connection['charset'] ?? 'utf8mb4',
            ];

            if (! empty($connection['unix_socket'])) {
                $options['socket'] = $connection['unix_socket'];
            } else {
                $options['protocol'] = 'TCP';
            }

            if (defined('PDO::MYSQL_ATTR_SSL_CA') && ! empty($connection['options'][constant('PDO::MYSQL_ATTR_SSL_CA')])) {
                $options['ssl-ca'] = $connection['options'][constant('PDO::MYSQL_ATTR_SSL_CA')];
                $options['ssl-mode'] = 'VERIFY_CA';
            }

            $contents = "[client]\n";

            foreach ($options as $key => $value) {
                $value = strtr((string) $value, ['\\' => '\\\\', '"' => '\\"', "\n" => '\\n', "\r" => '\\r', "\t" => '\\t']);
                $contents .= $key.'="'.$value.'"'."\n";
            }

            if (file_put_contents($credentials, $contents) !== strlen($contents)) {
                throw new RuntimeException('No se pudo escribir el archivo temporal de conexión.');
            }

            $gzip = gzopen($partial, 'wb6');

            if ($gzip === false) {
                throw new RuntimeException('No se pudo abrir el archivo de respaldo.');
            }

            // Sin shell ni contraseñas en los argumentos; salida comprimida por bloques.
            $process = new Process([
                (string) config('backups.binary'),
                '--defaults-file='.$credentials,
                '--single-transaction',
                '--quick',
                '--skip-lock-tables',
                '--no-tablespaces',
                '--set-gtid-purged=OFF',
                '--hex-blob',
                '--',
                $connection['database'],
            ], timeout: (int) config('backups.timeout'));
            $process->disableOutput();
            $bytes = 0;
            $exitCode = $process->run(function (string $type, string $buffer) use ($gzip, &$bytes): void {
                if ($type !== Process::OUT) {
                    return;
                }

                $length = strlen($buffer);

                if (gzwrite($gzip, $buffer) !== $length) {
                    throw new RuntimeException('No se pudo escribir el respaldo completo; revise el espacio disponible.');
                }

                $bytes += $length;
            });

            if ($exitCode !== 0 || $bytes === 0) {
                throw new RuntimeException("mysqldump falló (código {$exitCode}) o no produjo datos. Revise el cliente MySQL, la conexión y los permisos de BD.");
            }

            $closed = gzclose($gzip);
            $gzip = null;

            if (! $closed || ! rename($partial, $path)) {
                throw new RuntimeException('No se pudo finalizar el archivo de respaldo.');
            }

            return $path;
        } finally {
            if ($process?->isRunning()) {
                $process->stop();
            }

            if (is_resource($gzip)) {
                gzclose($gzip);
            }

            if (is_string($credentials) && is_file($credentials)) {
                unlink($credentials);
            }

            if (is_file($partial)) {
                unlink($partial);
            }

            umask($previousMask);
        }
    }

    private function depurar(string $directory, string $prefix, string $current): void
    {
        $cutoff = now()->subDays((int) config('backups.retention_days'))->getTimestamp();

        foreach (glob($directory.'/'.$prefix.'*.sql.gz') ?: [] as $path) {
            if ($path !== $current && ! is_link($path) && is_file($path) && filemtime($path) < $cutoff && ! unlink($path)) {
                throw new RuntimeException('El respaldo se creó, pero no se pudo depurar una copia vencida.');
            }
        }
    }
}

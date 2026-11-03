<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Verifica que la configuración de esta instancia sea correcta y esté aislada.
 *
 * Pensado para ejecutarse en cada despliegue: detecta el .env equivocado, la
 * cookie compartida entre subdominios y la URL que no corresponde al dominio.
 */
class VerificarInstancia extends Command
{
    protected $signature = 'instancia:verificar';

    protected $description = 'Verifica que la configuración de esta instancia esté aislada y sea correcta';

    public function handle(): int
    {
        $fallos = 0;

        $instancia = (string) config('instancias.actual');
        $hosts = config('instancias.hosts', []);
        $hostUrl = parse_url((string) config('app.url'), PHP_URL_HOST);
        $cookie = (string) config('session.cookie');

        $fallos += $this->check(
            'APP_INSTANCE definido',
            filled($instancia),
            'Falta APP_INSTANCE en el .env.'
        );

        $fallos += $this->check(
            'APP_KEY definido',
            filled(config('app.key')) && config('app.key') !== 'base64:',
            'Falta APP_KEY o quedó como placeholder.'
        );

        $fallos += $this->check(
            'APP_HOSTS definido',
            $hosts !== [],
            'Falta APP_HOSTS en el .env.'
        );

        $fallos += $this->check(
            'APP_URL dentro de APP_HOSTS',
            $hostUrl !== null && in_array($hostUrl, $hosts, true),
            "El host de APP_URL ({$hostUrl}) no está en APP_HOSTS."
        );

        $fallos += $this->check(
            'SESSION_DOMAIN vacío (cookie host-only)',
            blank(config('session.domain')),
            'SESSION_DOMAIN debe quedar vacío: con un dominio compartido la sesión cruza instancias.'
        );

        $fallos += $this->check(
            'SESSION_COOKIE lleva el nombre de la instancia',
            $instancia !== '' && str_contains($cookie, $instancia),
            "SESSION_COOKIE ({$cookie}) debe contener el nombre de la instancia."
        );

        if (config('app.env') === 'production') {
            $fallos += $this->check(
                'APP_DEBUG desactivado en producción',
                config('app.debug') === false,
                'APP_DEBUG debe ser false en producción.'
            );
        }

        $fallos += $this->check(
            'APP_ENV es production',
            config('app.env') === 'production',
            'APP_ENV debe ser production: este comando verifica un despliegue real.'
        );

        $cliente = config('instancias.cliente', []);

        $fallos += $this->check(
            'CLIENTE_RAZON_SOCIAL definido',
            filled($cliente['razon_social'] ?? null),
            'Falta CLIENTE_RAZON_SOCIAL: ClienteSeeder crearía una empresa sin nombre.'
        );

        $fallos += $this->check(
            'CLIENTE_NIT definido',
            filled($cliente['nit'] ?? null),
            'Falta CLIENTE_NIT: ClienteSeeder no puede crear la empresa.'
        );

        $fallos += $this->check(
            'CLIENTE_ADMIN_EMAIL es un email válido',
            filter_var($cliente['admin_email'] ?? '', FILTER_VALIDATE_EMAIL) !== false,
            'CLIENTE_ADMIN_EMAIL no es un email válido: no se podría iniciar sesión.'
        );

        // env() no aplica el default si la variable existe vacía: un
        // CLIENTE_ADMIN_PASSWORD= en blanco deja al admin sin contraseña.
        $fallos += $this->check(
            'CLIENTE_ADMIN_PASSWORD no está vacía',
            filled($cliente['admin_password'] ?? null),
            'CLIENTE_ADMIN_PASSWORD está vacía: el admin quedaría sin contraseña.'
        );

        $fallos += $this->check(
            'CLIENTE_ADMIN_DOCUMENTO definido',
            filled($cliente['admin_documento'] ?? null),
            'Falta CLIENTE_ADMIN_DOCUMENTO: no se puede crear el empleado del admin.'
        );

        $this->newLine();

        if ($fallos > 0) {
            $this->error("Se encontraron {$fallos} problema(s) de configuración.");

            return self::FAILURE;
        }

        $this->info('Instancia correctamente configurada y aislada.');

        return self::SUCCESS;
    }

    private function check(string $nombre, bool $ok, string $mensaje): int
    {
        if ($ok) {
            $this->line("  <fg=green>OK</>     {$nombre}");

            return 0;
        }

        $this->line("  <fg=red>FALLA</>  {$nombre} — {$mensaje}");

        return 1;
    }
}

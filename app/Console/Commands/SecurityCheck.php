<?php

namespace App\Console\Commands;

use App\Support\MailAvailability;
use Illuminate\Console\Command;

/**
 * Revisa la configuración del entorno (.env) contra lo que no se puede
 * garantizar desde el código: depuración encendida, cookies de sesión,
 * HTTPS, 2FA y correo. Pensado para correrse tras cada despliegue:
 *
 *   docker compose exec app php artisan security:check
 *
 * Termina con código 1 si hay algún problema grave (✘).
 */
class SecurityCheck extends Command
{
    protected $signature = 'security:check';

    protected $description = 'Revisa la configuración de seguridad del entorno (APP_DEBUG, cookies, HTTPS, 2FA, correo)';

    private int $errors = 0;

    private int $warnings = 0;

    public function handle(): int
    {
        // La instancia del comando se reutiliza entre ejecuciones en el mismo proceso (tests).
        $this->errors = 0;
        $this->warnings = 0;

        $url = (string) config('app.url');
        $host = (string) parse_url($url, PHP_URL_HOST);
        $https = str_starts_with(strtolower($url), 'https://');
        // Se considera "desplegado" todo lo que no sea la máquina local.
        $deployed = ! in_array($host, ['', 'localhost', '127.0.0.1', '::1'], true);

        $this->line('');
        $this->line('Entorno: APP_ENV='.config('app.env')."  APP_URL={$url}".($deployed ? '' : '  (local)'));
        $this->line('');

        if (empty(config('app.key'))) {
            $this->bad('APP_KEY vacío', 'Genera una con `php artisan key:generate`. Sin ella no hay sesiones ni cifrado (secretos de 2FA).');
        } else {
            $this->pass('APP_KEY configurada');
        }

        if (config('app.debug')) {
            $deployed
                ? $this->bad('APP_DEBUG=true en un servidor', 'Una pantalla de error mostraría rutas, consultas SQL y variables de entorno. Pon APP_DEBUG=false en el .env.')
                : $this->pass('APP_DEBUG=true (aceptable en local)');
        } else {
            $this->pass('APP_DEBUG=false');
        }

        if ($deployed && in_array(config('app.env'), ['local', 'testing'], true)) {
            $this->caution('APP_ENV='.config('app.env').' en un servidor', 'Usa APP_ENV=production.');
        }

        if ($deployed) {
            if ($https) {
                config('session.secure') === true
                    ? $this->pass('SESSION_SECURE_COOKIE=true')
                    : $this->bad('Sirves por HTTPS pero la cookie de sesión no es Secure', 'Pon SESSION_SECURE_COOKIE=true en el .env.');
            } else {
                $this->caution('El sistema se sirve por HTTP (APP_URL sin https)', 'Contraseñas, códigos 2FA y la cookie de sesión viajan sin cifrar por la red. Cuando haya HTTPS, pon APP_URL=https://… y SESSION_SECURE_COOKIE=true (no lo actives antes: sin HTTPS nadie podría iniciar sesión).');
            }
        }

        config('session.http_only') === false
            ? $this->bad('SESSION_HTTP_ONLY=false', 'El JavaScript de la página podría leer la cookie de sesión. Quita esa variable o ponla en true.')
            : $this->pass('Cookie de sesión HttpOnly');

        in_array(strtolower((string) config('session.same_site')), ['lax', 'strict'], true)
            ? $this->pass('SESSION_SAME_SITE='.config('session.same_site'))
            : $this->caution('SESSION_SAME_SITE no es lax/strict', 'Pon SESSION_SAME_SITE=lax.');

        config('two_factor.required')
            ? $this->pass('2FA obligatorio (TWO_FACTOR_REQUIRED=true)')
            : $this->caution('2FA no es obligatorio (TWO_FACTOR_REQUIRED=false)', 'Solo se pide a quien lo activó por su cuenta. En un servidor déjalo en true.');

        MailAvailability::canDeliver()
            ? $this->pass('Correo saliente configurado ('.config('mail.default').'): "¿Olvidaste tu contraseña?" disponible')
            : $this->caution('Correo saliente sin configurar (MAIL_MAILER='.config('mail.default').')', 'El enlace "¿Olvidaste tu contraseña?" está oculto; las contraseñas las restablece un Admin TI desde /users. Para habilitarlo configura MAIL_MAILER=smtp, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_FROM_ADDRESS y APP_URL.');

        $this->line('');
        $this->line("Resultado: {$this->errors} problema(s) grave(s), {$this->warnings} advertencia(s).");

        return $this->errors > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function pass(string $message): void
    {
        $this->line("  <fg=green>✔</> {$message}");
    }

    private function caution(string $message, string $fix): void
    {
        $this->warnings++;
        $this->line("  <fg=yellow>⚠</> {$message}");
        $this->line("      → {$fix}");
    }

    private function bad(string $message, string $fix): void
    {
        $this->errors++;
        $this->line("  <fg=red>✘</> {$message}");
        $this->line("      → {$fix}");
    }
}

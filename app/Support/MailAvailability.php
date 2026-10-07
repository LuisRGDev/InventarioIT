<?php

namespace App\Support;

/**
 * Dice si el correo saliente está realmente configurado. Con MAIL_MAILER=log
 * (el valor por omisión) o array los correos no llegan a nadie: se escriben
 * en el log. Se usa para no ofrecer "¿Olvidaste tu contraseña?" cuando el
 * enlace de restablecimiento no le llegaría al usuario.
 */
class MailAvailability
{
    /** Transportes que NO entregan correo a una bandeja real. */
    private const NON_DELIVERING = ['log', 'array'];

    public static function canDeliver(): bool
    {
        return self::mailerDelivers((string) config('mail.default'));
    }

    private static function mailerDelivers(string $mailer, int $depth = 0): bool
    {
        $config = config("mail.mailers.{$mailer}");

        if (! is_array($config) || $depth > 3) {
            return false;
        }

        $transport = $config['transport'] ?? null;

        // failover / roundrobin: basta con que alguno de sus mailers entregue.
        if (in_array($transport, ['failover', 'roundrobin'], true)) {
            foreach ($config['mailers'] ?? [] as $member) {
                if (self::mailerDelivers((string) $member, $depth + 1)) {
                    return true;
                }
            }

            return false;
        }

        return $transport !== null && ! in_array($transport, self::NON_DELIVERING, true);
    }
}

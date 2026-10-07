<?php

namespace App\Http\Middleware;

use App\Support\MailAvailability;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Con el correo sin configurar (MAIL_MAILER=log/array) el enlace de
 * restablecimiento nunca llegaría al usuario: se cierra el flujo en vez de
 * aparentar que funciona. Los admins restablecen contraseñas desde /users.
 */
class EnsurePasswordResetIsAvailable
{
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(MailAvailability::canDeliver(), 404);

        return $next($request);
    }
}

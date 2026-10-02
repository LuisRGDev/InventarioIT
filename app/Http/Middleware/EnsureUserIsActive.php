<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Corta el acceso de inmediato cuando a un usuario con sesión ya abierta
 * lo desactivan desde el panel de administración. El chequeo de
 * active=false en LoginForm::authenticate() solo bloquea logins nuevos;
 * sin esto, alguien desactivado seguía teniendo acceso completo con su
 * sesión existente hasta que expirara sola (120 min) o cerrara sesión por
 * su cuenta.
 */
class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && ! $user->active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('status', 'Tu cuenta ha sido desactivada. Contacta a un administrador.');
        }

        return $next($request);
    }
}

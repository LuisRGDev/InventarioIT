<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Con TWO_FACTOR_REQUIRED=true, un usuario autenticado que todavía no activó
 * la autenticación en dos pasos solo puede ver la pantalla de configuración
 * (y cerrar sesión): se le redirige ahí desde cualquier otra ruta.
 *
 * Se aplica por ruta (alias "two_factor") y NO en el grupo global "web":
 * /livewire/update también pasa por "web", y la propia pantalla de
 * configuración es un componente Livewire que necesita poder actualizarse
 * antes de que el 2FA esté activo.
 */
class EnsureTwoFactorIsEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && config('two_factor.required') && ! $user->hasTwoFactorEnabled()) {
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                abort(403, 'Debes activar la autenticación en dos pasos.');
            }

            return redirect()->route('two-factor.settings')
                ->with('status', 'Por seguridad debes activar la autenticación en dos pasos para continuar.');
        }

        return $next($request);
    }
}

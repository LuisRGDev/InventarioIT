<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Si un admin fijó la contraseña del usuario (alta o restablecimiento), solo
 * puede ver la pantalla de cambio de contraseña (y cerrar sesión) hasta que
 * elija la suya. Igual que two_factor, se aplica por ruta y no en el grupo
 * "web": la propia pantalla de cambio es un componente Livewire.
 */
class EnsurePasswordIsChanged
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->must_change_password) {
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                abort(403, 'Debes cambiar tu contraseña temporal.');
            }

            return redirect()->route('password.force-change')
                ->with('status', 'Tu contraseña es temporal. Elige una nueva para continuar.');
        }

        return $next($request);
    }
}

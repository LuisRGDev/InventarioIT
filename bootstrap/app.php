<?php

use App\Http\Middleware\EnsurePasswordIsChanged;
use App\Http\Middleware\EnsurePasswordResetIsAvailable;
use App\Http\Middleware\EnsureTwoFactorIsEnabled;
use App\Http\Middleware\EnsureUserIsActive;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
            'role_or_permission' => RoleOrPermissionMiddleware::class,
            'active' => EnsureUserIsActive::class,
            'two_factor' => EnsureTwoFactorIsEnabled::class,
            'password_changed' => EnsurePasswordIsChanged::class,
            'password_reset_available' => EnsurePasswordResetIsAvailable::class,
        ]);

        // A diferencia de role:/permission: (middleware por ruta, que
        // necesita el registro explícito en AppServiceProvider para
        // aplicar también a las acciones AJAX de Livewire), esto se agrega
        // al grupo global "web", que Livewire sí respeta de forma nativa
        // en /livewire/update.
        $middleware->web(append: [
            EnsureUserIsActive::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

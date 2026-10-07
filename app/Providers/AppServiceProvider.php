<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Sin esto, el middleware role:/permission: solo protege la carga
        // inicial de página de un componente Livewire; las peticiones AJAX
        // posteriores (wire:click, wire:submit, etc.) van todas al endpoint
        // compartido /livewire/update, que Livewire solo protege con la
        // lista fija de "persistent middleware" (auth, can:, etc.). Hay que
        // añadir explícitamente los alias de spatie/laravel-permission a esa
        // lista para que los mismos roles se re-verifiquen en cada acción.
        //
        // "active" (App\Http\Middleware\EnsureUserIsActive) está además
        // registrado en el grupo global "web" (bootstrap/app.php), que sí
        // cubre /livewire/update en una petición HTTP real. Pero el harness
        // de pruebas de Livewire (Volt::test()/Livewire::test()) no repite
        // el pipeline HTTP completo: solo vuelve a aplicar esta lista de
        // "persistent middleware". Sin añadirlo aquí también, un usuario
        // desactivado seguía pudiendo ejecutar acciones Livewire en los
        // tests (y, por prudencia, mejor no depender únicamente del
        // comportamiento del grupo "web" en producción tampoco).
        Livewire::addPersistentMiddleware([
            'role',
            'permission',
            'role_or_permission',
            'active',
            // Exige 2FA activo también en las acciones Livewire de las páginas
            // protegidas (ver EnsureTwoFactorIsEnabled).
            'two_factor',
            // Ídem para la contraseña temporal (EnsurePasswordIsChanged).
            'password_changed',
        ]);

        // Política de contraseñas centralizada: aplica automáticamente a
        // todo uso de Password::defaults() (reseteo, cambio de contraseña
        // en el perfil, alta de usuarios). No se usa uncompromised() porque
        // consulta la API de Have I Been Pwned por HTTPS en cada validación;
        // en una red corporativa con salida a internet restringida eso
        // podría bloquear o hacer fallar silenciosamente el formulario.
        Password::defaults(function () {
            return Password::min(10)->mixedCase()->numbers()->symbols();
        });
    }
}

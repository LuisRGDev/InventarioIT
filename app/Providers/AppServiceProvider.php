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
        Livewire::addPersistentMiddleware([
            'role',
            'permission',
            'role_or_permission',
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

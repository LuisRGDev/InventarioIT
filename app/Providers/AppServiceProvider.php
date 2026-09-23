<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
    }
}

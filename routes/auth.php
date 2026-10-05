<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    // El auto-registro público está deshabilitado: las cuentas de esta
    // herramienta interna deben crearlas un Admin TI (p. ej. vía
    // `php artisan tinker` o un panel de gestión de usuarios dedicado).
    // La ruta 'register' se elimina intencionalmente.

    // Sin throttle de ruta: estas rutas Volt solo sirven la página (GET); el
    // envío del formulario viaja por /livewire/update, así que un
    // throttle:5,1 aquí no frenaba ningún intento de login y sí devolvía 429
    // a quien recargara la página unas veces (o a toda una oficina detrás de
    // la misma IP). El freno real de fuerza bruta está en LoginForm (por
    // correo+IP y por correo); el reset de contraseña lo limita el broker
    // de Laravel (config auth.passwords.*.throttle).
    Volt::route('login', 'pages.auth.login')
        ->name('login');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Volt::route('verify-email', 'pages.auth.verify-email')
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Volt::route('confirm-password', 'pages.auth.confirm-password')
        ->name('password.confirm');
});

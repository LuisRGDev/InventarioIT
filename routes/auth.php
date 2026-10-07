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

    // Segundo paso del login: solo se llega con una contraseña ya validada
    // (pendiente en sesión, ver LoginForm::authenticate()); sin pendiente
    // la propia página redirige a /login.
    Volt::route('two-factor-challenge', 'pages.auth.two-factor-challenge')
        ->name('two-factor.challenge');

    // Solo con correo saliente real configurado (ver MailAvailability): con
    // MAIL_MAILER=log el enlace nunca le llegaría al usuario, así que estas
    // rutas responden 404 y el login no ofrece "¿Olvidaste tu contraseña?".
    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->middleware('password_reset_available')
        ->name('password.request');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->middleware('password_reset_available')
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

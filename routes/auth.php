<?php

use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware('guest')->group(function () {
    // El auto-registro público está deshabilitado: las cuentas de esta
    // herramienta interna deben crearlas un Admin TI (p. ej. vía
    // `php artisan tinker` o un panel de gestión de usuarios dedicado).
    // La ruta 'register' se elimina intencionalmente.

    Volt::route('login', 'pages.auth.login')
        ->name('login')
        ->middleware('throttle:5,1');

    Volt::route('forgot-password', 'pages.auth.forgot-password')
        ->name('password.request')
        ->middleware('throttle:5,1');

    Volt::route('reset-password/{token}', 'pages.auth.reset-password')
        ->name('password.reset')
        ->middleware('throttle:5,1');
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

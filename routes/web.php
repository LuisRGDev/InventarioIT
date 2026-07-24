<?php

use App\Http\Controllers\DeviceCategoryController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\EmployeeController;
use App\Livewire\AssignDevicePage;
use App\Livewire\ReplaceDevicePage;
use App\Livewire\ReturnDevicePage;
use Illuminate\Support\Facades\Route;

// ─── Página de inicio → dashboard ────────────────────────────────────────────
Route::redirect('/', 'dashboard');

// ─── Dashboard ────────────────────────────────────────────────────────────────
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ─── Perfil (Breeze) ──────────────────────────────────────────────────────────
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// ─── Rutas autenticadas ───────────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Empleados
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/{employee}/history', [EmployeeController::class, 'history'])
        ->name('employees.history');

    // Equipos
    Route::get('/devices/export', [DeviceController::class, 'export'])->name('devices.export');
    Route::resource('devices', DeviceController::class);
    Route::get('devices/{device}/history', [DeviceController::class, 'history'])
        ->name('devices.history');

    // Categorías (solo gestión, sin show individual)
    Route::resource('device-categories', DeviceCategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // ─── Operaciones de asignación (Livewire Full-Page Components) ────────────
    Route::get('assignments', [\App\Http\Controllers\AssignmentController::class, 'index'])
        ->name('assignments.index');
    Route::get('assignments/assign', AssignDevicePage::class)
        ->name('assignments.assign');

    Route::get('assignments/return/{deviceId?}', ReturnDevicePage::class)
        ->name('assignments.return');

    Route::get('assignments/replace/{employeeId?}', ReplaceDevicePage::class)
        ->name('assignments.replace');
});

require __DIR__ . '/auth.php';

<?php

use App\Http\Controllers\DeviceCategoryController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceModelController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MaintenanceController;
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

    // Dashboard Export e Import
    Route::get('/dashboard/export', [DeviceController::class, 'exportGeneral'])->name('dashboard.export');
    Route::get('/dashboard/import-template', [DeviceController::class, 'downloadGeneralTemplate'])->name('dashboard.import.template');
    Route::post('/dashboard/import', [DeviceController::class, 'importGeneral'])->name('dashboard.import');

    // Empleados
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::resource('employees', EmployeeController::class);
    Route::get('employees/{employee}/history', [EmployeeController::class, 'history'])
        ->name('employees.history');

    // Equipos
    Route::get('devices/export', [DeviceController::class, 'export'])->name('devices.export');
    Route::get('devices/import-template', [DeviceController::class, 'downloadTemplate'])->name('devices.import.template');
    Route::post('devices/import', [DeviceController::class, 'import'])->name('devices.import');
    Route::resource('devices', DeviceController::class);
    Route::get('devices/{device}/history', [DeviceController::class, 'history'])
        ->name('devices.history');

    // Mantenimientos de Equipos
    Route::get('maintenances/export', [MaintenanceController::class, 'export'])->name('maintenances.export');
    Route::post('maintenances/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenances.complete');
    Route::post('maintenances/{maintenance}/cancel', [MaintenanceController::class, 'cancel'])->name('maintenances.cancel');
    Route::resource('maintenances', MaintenanceController::class)->only(['index', 'create', 'store', 'show']);

    // Categorías y Estándares / Modelos de Hardware
    Route::resource('device-categories', DeviceCategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('device-models', DeviceModelController::class);

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

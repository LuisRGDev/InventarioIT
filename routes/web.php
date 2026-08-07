<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DeviceCategoryController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceModelController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobPositionController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\PhoneLineController;
use App\Livewire\AssignDevicePage;
use App\Livewire\AssignPhoneLinePage;
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

    // Líneas Telefónicas
    Route::get('phone-lines/export', function () {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\PhoneLinesExport, 'lineas_telefonicas.xlsx');
    })->name('phone-lines.export');
    Route::resource('phone-lines', PhoneLineController::class);
    Route::get('phone-lines/{phone_line}/history', [PhoneLineController::class, 'history'])
        ->name('phone-lines.history');

    // Extensiones
    Route::get('office-extensions/export', function () {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OfficeExtensionsExport, 'extensiones_telefonicas.xlsx');
    })->name('office-extensions.export');
    Route::get('office-extensions/import-template', [\App\Http\Controllers\OfficeExtensionController::class, 'downloadTemplate'])->name('office-extensions.import.template');
    Route::post('office-extensions/import', [\App\Http\Controllers\OfficeExtensionController::class, 'import'])->name('office-extensions.import');
    Route::resource('office-extensions', \App\Http\Controllers\OfficeExtensionController::class);

    // Mantenimientos de Equipos
    Route::get('maintenances/export', [MaintenanceController::class, 'export'])->name('maintenances.export');
    Route::post('maintenances/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenances.complete');
    Route::post('maintenances/{maintenance}/cancel', [MaintenanceController::class, 'cancel'])->name('maintenances.cancel');
    Route::resource('maintenances', MaintenanceController::class)->only(['index', 'create', 'store', 'show']);

    // Categorías y Estándares / Modelos de Hardware / Puestos
    Route::resource('device-categories', DeviceCategoryController::class)
        ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('device-models', DeviceModelController::class);
    Route::get('job-positions/import-template', [JobPositionController::class, 'downloadTemplate'])->name('job-positions.import.template');
    Route::post('job-positions/import', [JobPositionController::class, 'import'])->name('job-positions.import');
    Route::resource('job-positions', JobPositionController::class);

    // ─── Operaciones de asignación (Livewire Full-Page Components) ────────────
    // Historial y Asignaciones
    Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
    Route::get('assignments/assign', AssignDevicePage::class)->name('assignments.assign');
    Route::get('assignments/assign-phone-line', AssignPhoneLinePage::class)->name('assignments.assign-phone-line');
    Route::get('assignments/assign-extension', \App\Livewire\AssignExtensionPage::class)->name('assignments.assign-extension');
    Route::get('assignments/return/{device?}', \App\Livewire\ReturnDevicePage::class)->name('assignments.return');
    Route::get('assignments/replace/{employee?}', \App\Livewire\ReplaceDevicePage::class)->name('assignments.replace');

    Route::get('assignments/{assignment}/carta-responsiva', [\App\Http\Controllers\AssignmentController::class, 'downloadCartaResponsiva'])
        ->name('assignments.carta-responsiva');
});

require __DIR__ . '/auth.php';

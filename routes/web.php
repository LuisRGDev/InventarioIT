<?php

use App\Exports\OfficeExtensionsExport;
use App\Exports\PhoneLinesExport;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\DeviceCategoryController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceModelController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\JobPositionController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\OfficeExtensionController;
use App\Http\Controllers\PhoneLineController;
use App\Livewire\AssignDevicePage;
use App\Livewire\AssignExtensionPage;
use App\Livewire\AssignPhoneLinePage;
use App\Livewire\ReplaceDevicePage;
use App\Livewire\ReturnDevicePage;
use App\Support\Roles;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

// ─── Página de inicio → dashboard ────────────────────────────────────────────
Route::redirect('/', 'dashboard');

// ─── Dashboard ────────────────────────────────────────────────────────────────
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'role:'.Roles::READ])
    ->name('dashboard');

// ─── Perfil (Breeze) — autogestión, no requiere rol de inventario ────────────
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

// ─── Rutas autenticadas ───────────────────────────────────────────────────────
// Los grupos siguientes aplican el modelo de permisos definido en App\Support\Roles:
//   READ       → Admin TI, Técnico, Solo lectura (ver/listar/exportar)
//   WRITE      → Admin TI, Técnico (crear/editar/asignar/devolver/reemplazar/importar)
//   ADMIN_ONLY → Admin TI (eliminar registros y gestionar catálogos)
Route::middleware(['auth'])->group(function () {

    // Nota de orden: dentro de un mismo verbo HTTP, Laravel prueba las rutas
    // en el orden en que se registran. "GET /recurso/create" y "GET
    // /recurso/{id}" (show) tienen la misma forma (2 segmentos), así que
    // "create"/"edit" deben registrarse ANTES que el "{id}" comodín o
    // Laravel interpretará "create" como un id y devolverá 404. Por eso los
    // grupos WRITE y ADMIN_ONLY (que declaran create/edit) van antes que el
    // grupo READ (que declara index/show).

    // ── Escritura: crear/editar/asignar/devolver/reemplazar/importar ──
    Route::middleware('role:'.Roles::WRITE)->group(function () {
        Route::get('/dashboard/import-template', [DeviceController::class, 'downloadGeneralTemplate'])->name('dashboard.import.template');
        Route::post('/dashboard/import', [DeviceController::class, 'importGeneral'])->name('dashboard.import')->middleware('throttle:5,1');

        Route::resource('employees', EmployeeController::class)->only(['create', 'store', 'edit', 'update']);

        Route::get('devices/import-template', [DeviceController::class, 'downloadTemplate'])->name('devices.import.template');
        Route::post('devices/import', [DeviceController::class, 'import'])->name('devices.import')->middleware('throttle:5,1');
        Route::resource('devices', DeviceController::class)->only(['create', 'store', 'edit', 'update']);

        Route::get('phone-lines/import-template', [PhoneLineController::class, 'downloadTemplate'])->name('phone-lines.import.template');
        Route::post('phone-lines/import', [PhoneLineController::class, 'import'])->name('phone-lines.import')->middleware('throttle:5,1');
        Route::resource('phone-lines', PhoneLineController::class)->only(['create', 'store', 'edit', 'update']);

        Route::get('office-extensions/import-template', [OfficeExtensionController::class, 'downloadTemplate'])->name('office-extensions.import.template');
        Route::post('office-extensions/import', [OfficeExtensionController::class, 'import'])->name('office-extensions.import')->middleware('throttle:5,1');
        Route::resource('office-extensions', OfficeExtensionController::class)->only(['create', 'store', 'edit', 'update']);

        Route::post('maintenances/{maintenance}/complete', [MaintenanceController::class, 'complete'])->name('maintenances.complete');
        Route::post('maintenances/{maintenance}/cancel', [MaintenanceController::class, 'cancel'])->name('maintenances.cancel');
        Route::resource('maintenances', MaintenanceController::class)->only(['create', 'store']);

        // ─── Operaciones de asignación (Livewire Full-Page Components) ────────
        Route::get('assignments/assign', AssignDevicePage::class)->name('assignments.assign');
        Route::get('assignments/assign-phone-line', AssignPhoneLinePage::class)->name('assignments.assign-phone-line');
        Route::get('assignments/assign-extension', AssignExtensionPage::class)->name('assignments.assign-extension');
        Route::get('assignments/return/{device?}', ReturnDevicePage::class)->name('assignments.return');
        Route::get('assignments/replace/{employee?}', ReplaceDevicePage::class)->name('assignments.replace');

        Route::post('assignments/phone-lines/{assignment}/return', [AssignmentController::class, 'returnPhoneLine'])->name('assignments.phone-lines.return');
        Route::post('assignments/extensions/{assignment}/return', [AssignmentController::class, 'returnExtension'])->name('assignments.extensions.return');
    });

    // ── Solo Admin TI: eliminar registros y gestionar catálogos ──
    Route::middleware('role:'.Roles::ADMIN_ONLY)->group(function () {
        Route::resource('employees', EmployeeController::class)->only(['destroy']);
        Route::resource('devices', DeviceController::class)->only(['destroy']);
        Route::resource('phone-lines', PhoneLineController::class)->only(['destroy']);
        Route::resource('office-extensions', OfficeExtensionController::class)->only(['destroy']);

        Route::resource('device-categories', DeviceCategoryController::class)->only(['store', 'update', 'destroy']);
        Route::resource('device-models', DeviceModelController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

        Route::get('job-positions/import-template', [JobPositionController::class, 'downloadTemplate'])->name('job-positions.import.template');
        Route::post('job-positions/import', [JobPositionController::class, 'import'])->name('job-positions.import')->middleware('throttle:5,1');
        Route::resource('job-positions', JobPositionController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);
    });

    // ── Lectura: dashboard export, listados, detalle, historial, exportaciones ──
    // (va al final: index/show usan comodines "{id}" que deben registrarse
    // después de los "create"/"edit" de los grupos anteriores; ver nota arriba)
    Route::middleware('role:'.Roles::READ)->group(function () {
        Route::get('/dashboard/export', [DeviceController::class, 'exportGeneral'])->name('dashboard.export')->middleware('throttle:10,1');

        Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export')->middleware('throttle:10,1');
        Route::resource('employees', EmployeeController::class)->only(['index', 'show']);
        Route::get('employees/{employee}/history', [EmployeeController::class, 'history'])->name('employees.history');

        Route::get('devices/export', [DeviceController::class, 'export'])->name('devices.export')->middleware('throttle:10,1');
        Route::resource('devices', DeviceController::class)->only(['index', 'show']);
        Route::get('devices/{device}/history', [DeviceController::class, 'history'])->name('devices.history');

        Route::get('phone-lines/export', function () {
            return Excel::download(new PhoneLinesExport, 'lineas_telefonicas.xlsx');
        })->name('phone-lines.export')->middleware('throttle:10,1');
        Route::resource('phone-lines', PhoneLineController::class)->only(['index', 'show']);
        Route::get('phone-lines/{phone_line}/history', [PhoneLineController::class, 'history'])->name('phone-lines.history');

        Route::get('office-extensions/export', function () {
            return Excel::download(new OfficeExtensionsExport, 'extensiones_telefonicas.xlsx');
        })->name('office-extensions.export')->middleware('throttle:10,1');
        Route::resource('office-extensions', OfficeExtensionController::class)->only(['index', 'show']);

        Route::get('maintenances/export', [MaintenanceController::class, 'export'])->name('maintenances.export')->middleware('throttle:10,1');
        Route::resource('maintenances', MaintenanceController::class)->only(['index', 'show']);

        Route::resource('device-categories', DeviceCategoryController::class)->only(['index']);
        Route::resource('device-models', DeviceModelController::class)->only(['index', 'show']);
        Route::resource('job-positions', JobPositionController::class)->only(['index', 'show']);

        Route::get('assignments', [AssignmentController::class, 'index'])->name('assignments.index');
        Route::get('assignments/{assignment}/carta-responsiva', [AssignmentController::class, 'downloadCartaResponsiva'])
            ->name('assignments.carta-responsiva');
    });
});

require __DIR__.'/auth.php';

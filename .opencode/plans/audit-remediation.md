# Plan de Remediación - Auditoría InventarioIT
## Fecha: 2026-09-18

---

## FASE 1: CRÍTICO (Seguridad y Datos)

### 1.1 Inyección SQL en WithSorting
**Archivo:** `app/Livewire/Traits/WithSorting.php`
**Cambios:**
- Agregar propiedad `protected array $allowedSortColumns = []`
- Agregar validación en `sortByField()` con `in_array()` strict
- Agregar método `getSortBy()` que retorna columna válida o default
- Agregar tipo `string` a propiedades `$sortBy` y `$sortDirection`

### 1.2 BitLocker Key expuesta en Device
**Archivo:** `app/Models/Device.php`
**Cambios:**
- Agregar `protected $hidden = ['bitlocker_key', 'bitlocker_identifier']`

### 1.3 job_position_id missing en Employee
**Archivo:** `app/Models/Employee.php`
**Cambios:**
- Agregar `'job_position_id'` a `$fillable`
- Agregar `protected $hidden = ['domain_account']`

### 1.4 Race condition en PhoneLineAssignmentService
**Archivo:** `app/Services/PhoneLineAssignmentService.php`
**Cambios:**
- En `returnLine()`: agregar `$assignment->phoneLine->refresh()->lockForUpdate()` antes de la transacción

### 1.5 Race condition en ExtensionAssignmentService
**Archivo:** `app/Services/ExtensionAssignmentService.php`
**Cambios:**
- En `returnExtension()`: agregar `$assignment->officeExtension->refresh()->lockForUpdate()` antes de la transacción

### 1.6 Race condition en DeviceAssignmentService::replace()
**Archivo:** `app/Services/DeviceAssignmentService.php`
**Cambios:**
- En `replace()`: agregar `$oldDevice->refresh()->lockForUpdate()` y `$newDevice->refresh()->lockForUpdate()` al inicio del transaction

### 1.7 PhoneLinesExport error de sintaxis
**Archivo:** `app/Exports/PhoneLinesExport.php`
**Cambios:** Reescribir el método `map()` completo con variables correctas:
```php
public function map($phoneLine): array
{
    $employee = $phoneLine->currentAssignment?->employee;
    $smartphone = 'Ninguno';
    if ($employee) {
        $device = $employee->currentDevices->first(function ($device) {
            return $device->category && $device->category->slug === 'smartphone';
        });
        if ($device) {
            $smartphone = trim($device->brand . ' ' . $device->model);
        }
    }
    return [
        $phoneLine->number,
        $phoneLine->provider ?? '',
        $phoneLine->data_plan ?? '',
        $phoneLine->plan_cost ? $phoneLine->plan_cost : '',
        $phoneLine->notes ?? '',
        $employee ? $employee->name : '',
        $employee ? $employee->email : '',
        $employee ? $employee->employee_code : '',
        $employee ? $employee->department : '',
        $employee ? $employee->position : '',
        $smartphone,
    ];
}
```

### 1.8 WithChunkReading en todos los exports
**Archivos:** Todos en `app/Exports/` y `app/Exports/Sheets/`
**Cambios:** Agregar interfaz `WithChunkReading` y método `chunkSize()` retornando 1000

### 1.9 Validación en ReplaceDevicePage
**Archivo:** `app/Livewire/ReplaceDevicePage.php`
**Cambios:**
- En `prepareConfirm()`: agregar validación de `conditionOnReturn`, `conditionOnDelivery`, `oldDeviceNewStatus` contra enum values
- En `replace()`: re-validar antes de ejecutar

---

## FASE 2: ALTO (Performance y Robustez)

### 2.1 Throttle middleware en rutas import/export
**Archivo:** `routes/web.php`
**Cambios:** Agregar `->middleware('throttle:10,1')` a todas las rutas POST de import y GET de export

### 2.2 N+1 en DeviceController::show()
**Archivo:** `app/Http/Controllers/DeviceController.php`
**Cambios:** En `show()`, agregar eager loads faltantes: `deviceModel`, `activeMaintenance`, `lastPreventiveMaintenance`, `currentAssignment.returnedBy`

### 2.3 N+1 en EmployeesExport
**Archivo:** `app/Exports/EmployeesExport.php`
**Cambios:**
- En `collection()`: agregar `->withCount('currentAssignments')`
- En `map()`: usar `$employee->current_assignments_count` en vez de `->count()`

### 2.4 N+1 en GlobalEmployeesInventorySheet
**Archivo:** `app/Exports/Sheets/GlobalEmployeesInventorySheet.php`
**Cambios:**
- En `collection()`: agregar `'currentOfficeExtensions'`, `'jobPosition'` al `with()`
- En `map()`: usar null-safe operator para `$computer?->brand`

### 2.5 Try-catch en controllers para services
**Archivos:** `DeviceController.php`, `EmployeeController.php`, `AssignmentController.php`
**Cambios:** Envolver llamadas a services en try-catch con mensajes genéricos

### 2.6 Límites a colecciones en formularios
**Archivo:** `app/Http/Controllers/DeviceController.php`
**Cambios:**
- `create()` y `edit()`: limitar Employee a `active()->limit(500)->get()`
- DeviceModel: limitar a `limit(200)->get()`

### 2.7 Usar services en GlobalEmployeesInventoryImportSheet
**Archivo:** `app/Imports/Sheets/GlobalEmployeesInventoryImportSheet.php`
**Cambios:** Reemplazar `PhoneLineAssignment::create()` y `OfficeExtensionAssignment::create()` con llamadas a los services correspondientes

### 2.8 Límite de filas en imports
**Archivos:** Todos en `app/Imports/` y `app/Imports/Sheets/`
**Cambios:** Agregar validación de límite de filas (5000 max) al inicio de `collection()`

### 2.9 LockForUpdate al inicio de replace() (complemento de 1.6)
Ya cubierto en 1.6

---

## FASE 3: MEDIO (Calidad y Consistencia)

### 3.1 Form Requests para DeviceCategory, DeviceModel, JobPosition
**Archivos nuevos:**
- `app/Http/Requests/StoreDeviceCategoryRequest.php`
- `app/Http/Requests/UpdateDeviceCategoryRequest.php`
- `app/Http/Requests/StoreDeviceModelRequest.php`
- `app/Http/Requests/UpdateDeviceModelRequest.php`
- `app/Http/Requests/StoreJobPositionRequest.php`
- `app/Http/Requests/UpdateJobPositionRequest.php`

**Archivos a modificar:**
- `DeviceCategoryController.php`: usar Form Requests
- `DeviceModelController.php`: usar Form Requests
- `JobPositionController.php`: usar Form Requests

### 3.2 Cache count() en controllers
**Archivos:** `DeviceModelController.php`, `JobPositionController.php`
**Cambios:** Guardar `count()` en variable local antes de usar en condición y mensaje

### 3.3 Unificar límite en EmployeeController
**Archivo:** `app/Http/Controllers/EmployeeController.php`
**Cambios:** Agregar `limit(500)` consistente en `create()` y `edit()`

### 3.4 Genéricar mensajes de error en Livewire
**Archivos:** `ReturnDevicePage.php`, `ReplaceDevicePage.php`, `EmployeeOnboardingWizard.php`
**Cambios:** En bloques `catch (\Exception $e)`, usar mensaje genérico y reportar internamente

### 3.5 Caché en DashboardMetrics
**Archivo:** `app/Livewire/DashboardMetrics.php`
**Cambios:** Agregar `Cache::remember()` con TTL a `recentAssignments()`, `expiringWarranties()`, `activeMaintenances()`

### 3.6 Cachear datos de referencia en table components
**Archivos:** `DeviceTable.php`, `MaintenanceTable.php`
**Cambios:** Usar `Cache::remember()` para categorías y stats

### 3.7 Tipos en propiedades públicas de Livewire
**Archivos:** `DeviceTable.php`, `EmployeeTable.php`, `MaintenanceTable.php`, `WithSorting.php`
**Cambios:** Agregar tipos PHP a todas las propiedades públicas

### 3.8 #[Layout] en EmployeeOnboardingWizard
**Archivo:** `app/Livewire/EmployeeOnboardingWizard.php`
**Cambios:** Agregar `->layout('layouts.app', ['title' => 'Onboarding'])` en `render()`

### 3.9 HTTP 409/422 en errores de constraint
**Archivos:** Controllers con `destroy()` methods
**Cambios:** Retornar `abort(409, '...')` en vez de `back()->with('error', ...)`

### 3.10 HasFactory a modelos
**Archivos:** `Device.php`, `DeviceCategory.php`, `DeviceModel.php`, `DeviceAssignment.php`, `DeviceMaintenance.php`, `Employee.php`, `JobPosition.php`, `User.php`
**Cambios:** Agregar `use HasFactory;` trait

### 3.11 Paginar history() en PhoneLineController
**Archivo:** `app/Http/Controllers/PhoneLineController.php`
**Cambios:** Cambiar `->get()` a `->paginate(15)` en `history()`

### 3.12 Slugs de categorías a config
**Archivos:** `app/Models/DeviceCategory.php`, `config/inventory.php` (nuevo)
**Cambios:** Crear config con slugs de computadoras y smartphones, usar en métodos `isComputer()` e `isSmartphone()`

---

## FASE 4: BAJO (Mejoras Opcionales)

### 4.1 Escapar wildcards en LIKE
**Archivos:** Controllers con búsquedas LIKE
**Cambios:** Crear scope o helper que escape `%` y `_` en input de búsqueda

### 4.2 currentEmployee() accessor en PhoneLine y OfficeExtension
**Archivos:** `PhoneLine.php`, `OfficeExtension.php`
**Cambios:** Agregar accessor que retorna empleado actual vía `currentAssignment?->employee`

### 4.3 parseDate() a trait
**Archivos:** 4 archivos de imports
**Cambios:** Crear `app/Imports/Concerns/ParsesExcelDates.php` trait y usar en cada clase

### 4.4 Limpiar temp files
**Archivos:** `routes/console.php` o nuevo Command
**Cambios:** Agregar tarea programada para limpiar archivos en `storage/app/temp/` mayores a 1 hora

### 4.5 Centralizar warranty_expiring_days
**Archivos:** `config/inventory.php`, `Device.php`, `DashboardMetrics.php`
**Cambios:** Usar `config('inventory.warranty_warning_days', 30)` en todos los lugares

---

## FASE 5: VERIFICACIÓN

### 5.1 Ejecutar Laravel Pint
```bash
./vendor/bin/pint
```

### 5.2 Verificar syntax PHP
```bash
php -l app/**/*.php
```

### 5.3 Ejecutar tests
```bash
php artisan test
```

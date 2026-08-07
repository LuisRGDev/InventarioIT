<?php

namespace App\Imports\Sheets;

use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Employee;
use App\Enums\DeviceStatus;
use App\Enums\EmployeeStatus;
use App\Services\DeviceAssignmentService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\DB;

class GlobalEmployeesInventoryImportSheet implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private $categories;
    private $assignmentService;

    public function __construct()
    {
        $this->categories = collect();
        foreach (DeviceCategory::all() as $cat) {
            $this->categories->put(mb_strtolower($cat->name, 'UTF-8'), $cat);
            $this->categories->put(mb_strtolower($cat->slug, 'UTF-8'), $cat);
            $this->categories->put(str_replace(['á','é','í','ó','ú','ä','ë','ï','ö','ü'], ['a','e','i','o','u','a','e','i','o','u'], mb_strtolower($cat->name, 'UTF-8')), $cat);
        }
        $this->assignmentService = app(DeviceAssignmentService::class);
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $employeeEmail = mb_strtolower(trim((string) ($row['correo'] ?? '')), 'UTF-8');
                if (empty($employeeEmail) || $employeeEmail === 'n/a') {
                    continue; // Requerimos el correo del empleado para asignarlo
                }

                // 1. Empleado
                $department = (string) ($row['departamento'] ?? 'General');
                $positionName = (string) ($row['puesto'] ?? 'General');
                $direction = (string) ($row['direccion'] ?? 'General');

                // Intentar buscar el JobPosition
                $jobPositionId = null;
                if (!empty($row['departamento']) && !empty($row['puesto'])) {
                    $jobPosition = \App\Models\JobPosition::where('name', $positionName)
                        ->where('area', $department)
                        ->first();
                    
                    if (!$jobPosition && !empty($row['direccion'])) {
                        // Si no existe, lo creamos
                        $jobPosition = \App\Models\JobPosition::create([
                            'direction' => $direction,
                            'area' => $department,
                            'name' => $positionName,
                        ]);
                    }
                    $jobPositionId = $jobPosition?->id;
                }

                $employee = Employee::firstOrCreate(
                    ['email' => $employeeEmail],
                    [
                        'name'           => (string) ($row['empleado'] ?? 'Colaborador Importado'),
                        'employee_code'  => !empty($row['numero_de_empleado']) && $row['numero_de_empleado'] !== 'N/A' ? (string) $row['numero_de_empleado'] : null,
                        'department'     => $department,
                        'position'       => $positionName,
                        'job_position_id'=> $jobPositionId,
                        'domain_account' => (string) ($row['usuario_de_dominio'] ?? null),
                        'status'         => EmployeeStatus::Activo,
                    ]
                );

                if ($employee->wasRecentlyCreated === false) {
                    if (!empty($row['empleado']) && $row['empleado'] !== 'N/A') $employee->name = (string) $row['empleado'];
                    if (!empty($row['numero_de_empleado']) && $row['numero_de_empleado'] !== 'N/A') $employee->employee_code = (string) $row['numero_de_empleado'];
                    if (!empty($row['departamento']) && $row['departamento'] !== 'N/A') $employee->department = $department;
                    if (!empty($row['puesto']) && $row['puesto'] !== 'N/A') $employee->position = $positionName;
                    if (!empty($row['usuario_de_dominio']) && $row['usuario_de_dominio'] !== 'N/A') $employee->domain_account = (string) $row['usuario_de_dominio'];
                    if ($jobPositionId) $employee->job_position_id = $jobPositionId;
                    $employee->status = EmployeeStatus::Activo;
                    $employee->save();
                }

                // Extraemos notas
                $notes = (string) ($row['notas'] ?? '');

                // 2. Computadora
                $computerSerial = trim((string) ($row['numero_de_serie'] ?? ''));
                if (!empty($computerSerial) && $computerSerial !== 'N/A') {
                    // Buscar la llave que contiene 'categoria' de forma segura
                    $catKey = collect(array_keys($row->toArray()))->first(fn($k) => str_contains((string)$k, 'categoria'));
                    $categoryInput = $catKey ? mb_strtolower(trim((string) ($row[$catKey] ?? '')), 'UTF-8') : '';
                    $normalizedCategory = str_replace(['á','é','í','ó','ú','ä','ë','ï','ö','ü'], ['a','e','i','o','u','a','e','i','o','u'], $categoryInput);
                    
                    $categoryId = ($this->categories->get($categoryInput) ?? $this->categories->get($normalizedCategory))?->id;
                    if (!$categoryId) {
                        $categoryId = $this->categories->get('portatil')?->id ?? DeviceCategory::first()?->id ?? 1;
                    }

                    $existingComputer = Device::where('serial_number', $computerSerial)->first();

                    $computerData = [
                        'device_category_id'   => $categoryId,
                        'brand'                => (string) ($row['marca'] ?? $existingComputer?->brand ?? 'Genérico'),
                        'model'                => (string) ($row['modelo'] ?? $existingComputer?->model ?? 'Genérico'),
                        'mac_address_ethernet' => !empty($row['mac_ethernet']) ? (string) $row['mac_ethernet'] : ($existingComputer?->mac_address_ethernet ?? null),
                        'mac_address_wifi'     => !empty($row['mac_wifi']) ? (string) $row['mac_wifi'] : ($existingComputer?->mac_address_wifi ?? null),
                        'service_tag'          => !empty($row['tag_service']) ? (string) $row['tag_service'] : ($existingComputer?->service_tag ?? null),
                        'bitlocker_identifier' => !empty($row['identificador_de_bl']) ? (string) $row['identificador_de_bl'] : ($existingComputer?->bitlocker_identifier ?? null),
                        'bitlocker_key'        => !empty($row['clave_de_bl']) ? (string) $row['clave_de_bl'] : ($existingComputer?->bitlocker_key ?? null),
                        'purchase_date'        => $this->parseDate($row['fecha_de_compra'] ?? null) ?? $existingComputer?->purchase_date,
                        'warranty_expires_at'  => $this->parseDate($row['garantia_expira'] ?? null) ?? $existingComputer?->warranty_expires_at,
                    ];

                    $specs = $existingComputer?->specs ?? [];
                    if (!empty($row['procesador'])) $specs['cpu'] = (string) $row['procesador'];
                    if (!empty($row['nucleos'])) $specs['cores'] = (string) $row['nucleos'];
                    if (!empty($row['ram'])) $specs['ram'] = (string) $row['ram'];
                    if (!empty($row['almacenamiento'])) $specs['storage'] = (string) $row['almacenamiento'];
                    if (!empty($row['sistema_operativo'])) $specs['os'] = (string) $row['sistema_operativo'];
                    
                    $computerData['specs'] = count($specs) > 0 ? $specs : null;
                    if (!empty($notes)) {
                        $computerData['notes'] = $existingComputer?->notes ? $existingComputer->notes . ' | ' . $notes : $notes;
                    }

                    if (!$existingComputer) {
                        $computerData['status'] = DeviceStatus::Disponible;
                        $existingComputer = Device::create(array_merge(['serial_number' => $computerSerial], $computerData));
                    } else {
                        $existingComputer->update($computerData);
                    }

                    $this->assignDeviceToEmployee($existingComputer, $employee);
                }

                // 3. Celular
                $imei = trim((string) ($row['imei'] ?? ''));
                if (!empty($imei) && $imei !== 'N/A') {
                    $smartphoneCategoryId = $this->categories->get('smartphone')?->id ?? DeviceCategory::first()?->id ?? 1;
                    $existingMobile = Device::where('imei', $imei)->first();

                    $modeloMobileKey = collect(array_keys($row->toArray()))->filter(fn($k) => str_contains((string)$k, 'modelo'))->last();
                    $osMobileKey = collect(array_keys($row->toArray()))->filter(fn($k) => str_contains((string)$k, 'sistema_operativo'))->last();

                    $mobileData = [
                        'device_category_id' => $smartphoneCategoryId,
                        'brand'              => (string) ($row['marca_de_celular'] ?? $existingMobile?->brand ?? 'Genérico'),
                        'model'              => (string) ($modeloMobileKey && isset($row[$modeloMobileKey]) ? $row[$modeloMobileKey] : ($existingMobile?->model ?? 'Genérico')),
                    ];

                    $mobileSpecs = $existingMobile?->specs ?? [];
                    if ($osMobileKey && !empty($row[$osMobileKey])) $mobileSpecs['os'] = (string) $row[$osMobileKey];
                    $mobileData['specs'] = count($mobileSpecs) > 0 ? $mobileSpecs : null;

                    if (!empty($notes)) {
                        $mobileData['notes'] = $existingMobile?->notes ? $existingMobile->notes . ' | ' . $notes : $notes;
                    }

                    if (!$existingMobile) {
                        $mobileData['status'] = DeviceStatus::Disponible;
                        // For smartphones, if we don't have a serial number in the import, use IMEI or generate one
                        $serialForMobile = $existingMobile?->serial_number ?? 'MOB-' . $imei;
                        $existingMobile = Device::create(array_merge(['serial_number' => $serialForMobile, 'imei' => $imei], $mobileData));
                    } else {
                        $existingMobile->update($mobileData);
                    }

                    $this->assignDeviceToEmployee($existingMobile, $employee);
                }

                // 4. Línea Telefónica
                $phoneNumber = trim((string) ($row['numero_de_telefono'] ?? ''));
                if (!empty($phoneNumber) && $phoneNumber !== 'N/A') {
                    $existingLine = \App\Models\PhoneLine::where('number', $phoneNumber)->first();

                    $lineData = [
                        'number' => $phoneNumber,
                        'data_plan' => (string) ($row['tipo_de_plan'] ?? $existingLine?->data_plan ?? null),
                        'plan_cost' => !empty($row['costo_de_plan']) ? (float) $row['costo_de_plan'] : ($existingLine?->plan_cost ?? null),
                        'notes' => $notes,
                    ];

                    if (!$existingLine) {
                        $lineData['status'] = \App\Enums\PhoneLineStatus::Asignada;
                        $existingLine = \App\Models\PhoneLine::create($lineData);
                    } else {
                        $lineData['status'] = \App\Enums\PhoneLineStatus::Asignada;
                        $existingLine->update($lineData);
                    }

                    // Asignar línea telefónica al empleado si no está asignada ya
                    $currentLineAssignment = $existingLine->currentAssignment;
                    if (!$currentLineAssignment || $currentLineAssignment->employee_id !== $employee->id) {
                        if ($currentLineAssignment) {
                            $currentLineAssignment->update(['returned_at' => now()]);
                        }
                        \App\Models\PhoneLineAssignment::create([
                            'phone_line_id' => $existingLine->id,
                            'employee_id' => $employee->id,
                            'assigned_at' => now(),
                            'notes' => 'Asignado/Actualizado durante importación masiva del Dashboard.',
                        ]);
                    }
                }

                // 5. Extensión de Oficina
                $extensionNumber = trim((string) ($row['numero_de_extension'] ?? ''));
                if (!empty($extensionNumber) && $extensionNumber !== 'N/A') {
                    $existingExtension = \App\Models\OfficeExtension::where('extension_number', $extensionNumber)->first();
                    $extDirection = (string) ($row['direccion_de_extension'] ?? $existingExtension?->direct_number ?? null);

                    $extData = [
                        'extension_number' => $extensionNumber,
                        'direct_number' => $extDirection,
                        'status' => \App\Enums\ExtensionStatus::Asignada,
                    ];

                    if (!$existingExtension) {
                        $existingExtension = \App\Models\OfficeExtension::create($extData);
                    } else {
                        $existingExtension->update($extData);
                    }

                    // Asignar extensión al empleado
                    $currentExtAssignment = $existingExtension->currentAssignment;
                    if (!$currentExtAssignment || $currentExtAssignment->employee_id !== $employee->id) {
                        if ($currentExtAssignment) {
                            $currentExtAssignment->update(['returned_at' => now()]);
                        }
                        \App\Models\OfficeExtensionAssignment::create([
                            'office_extension_id' => $existingExtension->id,
                            'employee_id' => $employee->id,
                            'assigned_at' => now(),
                            'notes' => 'Asignado/Actualizado durante importación masiva del Dashboard.',
                        ]);
                    }
                }
            }
        });
    }

    private function assignDeviceToEmployee($device, $employee)
    {
        $device->refresh();
        $currentAssignment = $device->currentAssignment;

        if (!$currentAssignment || $currentAssignment->employee_id !== $employee->id) {
            if ($currentAssignment) {
                $this->assignmentService->returnDevice($device, [
                    'condition_on_return' => 'buen_estado',
                    'notes' => 'Reasignación automática en importación.'
                ]);
                $device->refresh();
            }

            if ($device->status !== DeviceStatus::Disponible) {
                $device->update(['status' => DeviceStatus::Disponible]);
            }

            $this->assignmentService->assign(
                $device,
                $employee,
                [
                    'condition_on_delivery' => 'buen_estado',
                    'notes' => 'Asignado/Actualizado durante importación masiva del Dashboard.',
                ]
            );
        } else {
            if ($device->status !== DeviceStatus::Asignado) {
                $device->update(['status' => DeviceStatus::Asignado]);
            }
        }
    }

    private function parseDate($value)
    {
        if (empty($value) || $value === 'N/A' || $value === 'n/a') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }
            $valClean = trim((string) $value);
            if (preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/', $valClean)) {
                $separator = str_contains($valClean, '/') ? '/' : '-';
                return Carbon::createFromFormat("d{$separator}m{$separator}Y", $valClean);
            }
            return Carbon::parse($valClean);
        } catch (\Exception $e) {
            return null;
        }
    }
}

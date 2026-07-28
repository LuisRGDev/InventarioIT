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

class AssignedDevicesImportSheet implements ToCollection, WithHeadingRow, SkipsEmptyRows
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
                // Obtener número de serie (llave única para el dispositivo)
                $serialNumber = trim((string) ($row['numero_de_serie'] ?? $row['serie'] ?? ''));
                if (empty($serialNumber)) {
                    continue; // Saltar filas sin número de serie
                }

                // Parsear categoría
                $categoryInput = mb_strtolower(trim((string) ($row['categoria'] ?? $row['categoria_tipo'] ?? '')), 'UTF-8');
                $normalizedCategory = str_replace(['á','é','í','ó','ú','ä','ë','ï','ö','ü'], ['a','e','i','o','u','a','e','i','o','u'], $categoryInput);
                $categoryId = ($this->categories->get($categoryInput) ?? $this->categories->get($normalizedCategory))?->id;

                // Specs array
                $specs = [];
                if (!empty($row['procesador_cpu']) || !empty($row['cpu'])) $specs['cpu'] = (string) ($row['procesador_cpu'] ?? $row['cpu']);
                if (!empty($row['nucleos'])) $specs['cores'] = (string) $row['nucleos'];
                if (!empty($row['ram'])) $specs['ram'] = (string) $row['ram'];
                if (!empty($row['almacenamiento'])) $specs['storage'] = (string) $row['almacenamiento'];
                if (!empty($row['sistema_operativo']) || !empty($row['os'])) $specs['os'] = (string) ($row['sistema_operativo'] ?? $row['os']);
                if (!empty($row['telefono']) || !empty($row['telefono_numero'])) $specs['phone_number'] = (string) ($row['telefono'] ?? $row['telefono_numero']);
                if (!empty($row['imei'])) $specs['imei'] = (string) $row['imei'];
                if (!empty($row['plan_de_datos'])) $specs['data_plan'] = (string) $row['plan_de_datos'];

                $existingDevice = Device::where('serial_number', $serialNumber)->first();
                if (!$categoryId && $existingDevice) {
                    $categoryId = $existingDevice->device_category_id;
                }
                if (!$categoryId) {
                    $categoryId = DeviceCategory::first()?->id ?? 1;
                }

                $deviceData = [
                    'device_category_id'   => $categoryId,
                    'brand'                => (string) ($row['marca'] ?? $existingDevice?->brand ?? 'Genérico'),
                    'model'                => (string) ($row['modelo'] ?? $existingDevice?->model ?? 'Genérico'),
                    'computer_name'        => !empty($row['hostname_nombre']) ? (string) $row['hostname_nombre'] : (!empty($row['hostname']) ? (string) $row['hostname'] : ($existingDevice?->computer_name ?? null)),
                    'mac_address_ethernet' => !empty($row['mac_ethernet']) ? (string) $row['mac_ethernet'] : ($existingDevice?->mac_address_ethernet ?? null),
                    'mac_address_wifi'     => !empty($row['mac_wifi']) ? (string) $row['mac_wifi'] : ($existingDevice?->mac_address_wifi ?? null),
                    'purchase_date'        => $this->parseDate($row['fecha_compra'] ?? null) ?? $existingDevice?->purchase_date,
                    'warranty_expires_at'  => $this->parseDate($row['garantia_expira'] ?? null) ?? $existingDevice?->warranty_expires_at,
                    'specs'                => count($specs) > 0 ? array_merge($existingDevice?->specs ?? [], $specs) : ($existingDevice?->specs ?? null),
                    'notes'                => !empty($row['notas_del_equipo']) ? (string) $row['notas_del_equipo'] : (!empty($row['notas']) ? (string) $row['notas'] : ($existingDevice?->notes ?? null)),
                ];

                if (!$existingDevice) {
                    $deviceData['status'] = DeviceStatus::Disponible;
                    $existingDevice = Device::create(array_merge(['serial_number' => $serialNumber], $deviceData));
                } else {
                    $existingDevice->update($deviceData);
                }

                // Procesar empleado y asignación
                $employeeEmail = mb_strtolower(trim((string) ($row['correo_empleado'] ?? $row['email_empleado'] ?? '')), 'UTF-8');
                if (!empty($employeeEmail) && $employeeEmail !== 'n/a') {
                    $employee = Employee::firstOrCreate(
                        ['email' => $employeeEmail],
                        [
                            'name'          => (string) ($row['empleado_asignado'] ?? $row['nombre_empleado'] ?? 'Colaborador Importado'),
                            'employee_code' => !empty($row['no_empleado']) && $row['no_empleado'] !== 'N/A' ? (string) $row['no_empleado'] : null,
                            'department'    => (string) ($row['departamento'] ?? 'General'),
                            'position'      => (string) ($row['puesto'] ?? 'General'),
                            'status'        => EmployeeStatus::Activo,
                        ]
                    );

                    // Si ya existe y se enviaron nuevos datos coherentes, actualizar información laboral
                    if ($employee->wasRecentlyCreated === false) {
                        if (!empty($row['empleado_asignado']) && $row['empleado_asignado'] !== 'N/A') $employee->name = (string) $row['empleado_asignado'];
                        if (!empty($row['departamento']) && $row['departamento'] !== 'N/A') $employee->department = (string) $row['departamento'];
                        if (!empty($row['puesto']) && $row['puesto'] !== 'N/A') $employee->position = (string) $row['puesto'];
                        $employee->status = EmployeeStatus::Activo;
                        $employee->save();
                    }

                    // Verificar asignación actual
                    $existingDevice->refresh();
                    $currentAssignment = $existingDevice->currentAssignment;

                    if (!$currentAssignment || $currentAssignment->employee_id !== $employee->id) {
                        if ($currentAssignment) {
                            $this->assignmentService->returnDevice($existingDevice, [
                                'condition_on_return' => 'buen_estado',
                                'notes' => 'Reasignación automática en importación general desde Dashboard.'
                            ]);
                            $existingDevice->refresh();
                        }

                        if ($existingDevice->status !== DeviceStatus::Disponible) {
                            $existingDevice->update(['status' => DeviceStatus::Disponible]);
                        }

                        $this->assignmentService->assign(
                            $existingDevice,
                            $employee,
                            [
                                'condition_on_delivery' => 'buen_estado',
                                'notes' => 'Asignado/Actualizado durante importación masiva del Dashboard.',
                            ]
                        );
                    } else {
                        // Ya está asignado a este empleado; asegurar estatus 'Asignado'
                        if ($existingDevice->status !== DeviceStatus::Asignado) {
                            $existingDevice->update(['status' => DeviceStatus::Asignado]);
                        }
                    }
                }
            }
        });
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

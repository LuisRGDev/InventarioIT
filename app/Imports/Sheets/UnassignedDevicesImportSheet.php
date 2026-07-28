<?php

namespace App\Imports\Sheets;

use App\Models\Device;
use App\Models\DeviceCategory;
use App\Enums\DeviceStatus;
use App\Services\DeviceAssignmentService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\DB;

class UnassignedDevicesImportSheet implements ToCollection, WithHeadingRow, SkipsEmptyRows
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
                // Obtener número de serie
                $serialNumber = trim((string) ($row['numero_de_serie'] ?? $row['serie'] ?? ''));
                if (empty($serialNumber) || $serialNumber === 'N/A' || $serialNumber === 'n/a') {
                    continue;
                }

                // Parsear estatus
                $statusInput = mb_strtolower(trim((string) ($row['estatus_actual'] ?? $row['estatus'] ?? '')), 'UTF-8');
                $targetStatus = $this->parseDeviceStatus($statusInput);

                // Parsear categoría
                $categoryInput = mb_strtolower(trim((string) ($row['categoria_tipo'] ?? $row['categoria'] ?? '')), 'UTF-8');
                $normalizedCategory = str_replace(['á','é','í','ó','ú','ä','ë','ï','ö','ü'], ['a','e','i','o','u','a','e','i','o','u'], $categoryInput);
                $categoryId = ($this->categories->get($categoryInput) ?? $this->categories->get($normalizedCategory))?->id;

                // Specs array
                $specs = [];
                if (!empty($row['procesador_cpu']) || !empty($row['cpu'])) $specs['cpu'] = (string) ($row['procesador_cpu'] ?? $row['cpu']);
                if (!empty($row['nucleos'])) $specs['cores'] = (string) $row['nucleos'];
                if (!empty($row['ram'])) $specs['ram'] = (string) $row['ram'];
                if (!empty($row['almacenamiento'])) $specs['storage'] = (string) $row['almacenamiento'];
                if (!empty($row['sistema_operativo']) || !empty($row['os'])) $specs['os'] = (string) ($row['sistema_operativo'] ?? $row['os']);
                if (!empty($row['telefono_numero']) || !empty($row['telefono'])) $specs['phone_number'] = (string) ($row['telefono_numero'] ?? $row['telefono']);
                if (!empty($row['imei'])) $specs['imei'] = (string) $row['imei'];
                if (!empty($row['plan_de_datos'])) $specs['data_plan'] = (string) $row['plan_de_datos'];

                $existingDevice = Device::where('serial_number', $serialNumber)->first();
                if (!$categoryId && $existingDevice) {
                    $categoryId = $existingDevice->device_category_id;
                }
                if (!$categoryId) {
                    $categoryId = DeviceCategory::first()?->id ?? 1;
                }

                // Si ya estaba asignado en BD pero llegó en la pestaña de "Sin Asignar", devolverlo y cerrar su asignación
                if ($existingDevice && $existingDevice->currentAssignment) {
                    $this->assignmentService->returnDevice($existingDevice, [
                        'condition_on_return' => 'buen_estado',
                        'new_status'          => $targetStatus->value,
                        'notes'               => 'Devolución automática en importación general (Pestaña Sin Asignar/Stock).'
                    ]);
                }

                $deviceData = [
                    'device_category_id'   => $categoryId,
                    'brand'                => (string) ($row['marca'] ?? $existingDevice?->brand ?? 'Genérico'),
                    'model'                => (string) ($row['modelo'] ?? $existingDevice?->model ?? 'Genérico'),
                    'computer_name'        => !empty($row['hostname_identificador']) && $row['hostname_identificador'] !== 'N/A' ? (string) $row['hostname_identificador'] : (!empty($row['hostname']) ? (string) $row['hostname'] : ($existingDevice?->computer_name ?? null)),
                    'mac_address_ethernet' => !empty($row['mac_ethernet']) && $row['mac_ethernet'] !== 'N/A' ? (string) $row['mac_ethernet'] : ($existingDevice?->mac_address_ethernet ?? null),
                    'mac_address_wifi'     => !empty($row['mac_wifi']) && $row['mac_wifi'] !== 'N/A' ? (string) $row['mac_wifi'] : ($existingDevice?->mac_address_wifi ?? null),
                    'status'               => $targetStatus,
                    'purchase_date'        => $this->parseDate($row['fecha_compra'] ?? null) ?? $existingDevice?->purchase_date,
                    'warranty_expires_at'  => $this->parseDate($row['garantia_expira'] ?? null) ?? $existingDevice?->warranty_expires_at,
                    'specs'                => count($specs) > 0 ? array_merge($existingDevice?->specs ?? [], $specs) : ($existingDevice?->specs ?? null),
                    'notes'                => !empty($row['notas_ubicacion_en_almacen']) && $row['notas_ubicacion_en_almacen'] !== 'N/A' ? (string) $row['notas_ubicacion_en_almacen'] : (!empty($row['notas']) ? (string) $row['notas'] : ($existingDevice?->notes ?? null)),
                ];

                if (!$existingDevice) {
                    Device::create(array_merge(['serial_number' => $serialNumber], $deviceData));
                } else {
                    $existingDevice->update($deviceData);
                }
            }
        });
    }

    private function parseDeviceStatus(string $input): DeviceStatus
    {
        $clean = mb_strtolower(trim($input), 'UTF-8');
        $cleanNorm = str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'], $clean);
        
        return match ($cleanNorm) {
            'en reparacion', 'en_reparacion', 'reparacion', 'mantenimiento' => DeviceStatus::EnReparacion,
            'obsoleto', 'desuso' => DeviceStatus::Obsoleto,
            'dado de baja', 'baja', 'eliminado' => DeviceStatus::Baja,
            'asignado', 'en uso' => DeviceStatus::Disponible, // Como está en la hoja Sin Asignar, si alguien pone Asignado por error pasa a Disponible en stock
            default => DeviceStatus::Disponible,
        };
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

<?php

namespace App\Imports;

use App\Models\Device;
use App\Models\DeviceCategory;
use App\Enums\DeviceStatus;
use App\Models\Employee;
use App\Enums\EmployeeStatus;
use App\Services\DeviceAssignmentService;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class DevicesImport implements ToCollection, WithHeadingRow, WithValidation
{
    private $categories;
    private $assignmentService;

    public function __construct()
    {
        // Cachear las categorías para no consultar la DB en cada fila
        $this->categories = DeviceCategory::all()->keyBy(function($item) {
            return strtolower($item->name);
        });
        $this->assignmentService = app(DeviceAssignmentService::class);
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $categoryName = strtolower($row['categoria'] ?? '');
                $categoryId = $this->categories->get($categoryName)?->id;

                $specs = [];
                if (!empty($row['procesador_cpu'])) $specs['cpu'] = $row['procesador_cpu'];
                if (!empty($row['nucleos'])) $specs['cores'] = $row['nucleos'];
                if (!empty($row['ram'])) $specs['ram'] = $row['ram'];
                if (!empty($row['almacenamiento'])) $specs['storage'] = $row['almacenamiento'];
                if (!empty($row['sistema_operativo'])) $specs['os'] = $row['sistema_operativo'];
                if (!empty($row['telefono'])) $specs['phone_number'] = $row['telefono'];
                if (!empty($row['imei'])) $specs['imei'] = $row['imei'];
                if (!empty($row['plan_de_datos'])) $specs['data_plan'] = $row['plan_de_datos'];

                $device = Device::create([
                    'device_category_id'  => $categoryId,
                    'brand'               => $row['marca'],
                    'model'               => $row['modelo'],
                    'serial_number'       => $row['numero_de_serie'],
                    'computer_name'       => $row['hostname'] ?? null,
                    'mac_address_ethernet'=> $row['mac_ethernet'] ?? null,
                    'mac_address_wifi'    => $row['mac_wifi'] ?? null,
                    'status'              => DeviceStatus::Disponible,
                    'purchase_date'       => $this->parseDate($row['fecha_compra'] ?? null),
                    'warranty_expires_at' => $this->parseDate($row['garantia_expira'] ?? null),
                    'specs'               => count($specs) > 0 ? $specs : null,
                    'notes'               => $row['notas'] ?? null,
                ]);

                // Si viene un correo de empleado, crearlo/buscarlo y asignarlo
                if (!empty($row['correo_empleado'])) {
                    $employee = Employee::firstOrCreate(
                        ['email' => strtolower($row['correo_empleado'])],
                        [
                            'name'           => $row['nombre_empleado'] ?? 'Empleado Importado',
                            'employee_code'  => $row['no_empleado'] ?? null,
                            'department'     => $row['departamento'] ?? 'General',
                            'position'       => $row['puesto'] ?? 'General',
                            'status'         => EmployeeStatus::Activo,
                        ]
                    );

                    $this->assignmentService->assign(
                        $device,
                        $employee,
                        [
                            'condition_on_delivery' => 'buen_estado',
                            'notes' => 'Asignado automáticamente durante importación masiva.',
                        ]
                    );
                }
            }
        });
    }

    public function rules(): array
    {
        $categoryNames = DeviceCategory::pluck('name')->map(fn($n) => strtolower($n))->toArray();

        return [
            'categoria' => ['required', function($attribute, $value, $fail) use ($categoryNames) {
                if (!in_array(strtolower($value), $categoryNames)) {
                    $fail("La categoría '{$value}' no existe. Opciones válidas: " . implode(', ', $categoryNames));
                }
            }],
            'marca'           => ['required', 'string'],
            'modelo'          => ['required', 'string'],
            'numero_de_serie' => ['required', 'string', 'unique:devices,serial_number'],
            // Las demás columnas son opcionales
        ];
    }

    public function customValidationMessages()
    {
        return [
            'categoria.required'       => 'La columna Categoría es obligatoria.',
            'marca.required'           => 'La columna Marca es obligatoria.',
            'modelo.required'          => 'La columna Modelo es obligatoria.',
            'numero_de_serie.required' => 'El Número de Serie es obligatorio.',
            'numero_de_serie.unique'   => 'El Número de Serie :input ya existe en la base de datos.',
        ];
    }

    private function parseDate($value)
    {
        if (empty($value)) {
            return null;
        }

        try {
            // Excel a veces envía fechas como enteros
            if (is_numeric($value)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            }
            return Carbon::parse($value);
        } catch (\Exception $e) {
            return null;
        }
    }
}

<?php

namespace App\Imports;

use App\Enums\EmployeeStatus;
use App\Enums\PhoneLineStatus;
use App\Models\Employee;
use App\Models\PhoneLine;
use App\Services\PhoneLineAssignmentService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class PhoneLinesImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    private $assignmentService;

    public function __construct()
    {
        $this->assignmentService = app(PhoneLineAssignmentService::class);
    }

    public function collection(Collection $rows)
    {
        $maxRows = config('inventory.import_max_rows', 5000);
        if ($rows->count() > $maxRows) {
            throw new \Exception("El archivo contiene {$rows->count()} filas. El máximo permitido es {$maxRows}.");
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Las cabeceras normalizadas suelen ser snake_case de los nombres con minúsculas
                $phoneLine = PhoneLine::updateOrCreate(
                    ['number' => trim((string) $row['numero_telefonico'])],
                    [
                        'provider' => $row['proveedor'] ?? null,
                        'data_plan' => $row['plan_de_datos'] ?? null,
                        'plan_cost' => $row['costo_del_plan'] ? (float) $row['costo_del_plan'] : null,
                        'status' => PhoneLineStatus::Disponible,
                        'notes' => $row['notas'] ?? null,
                    ]
                );

                // Si viene un correo de empleado, crearlo/buscarlo y asignarlo
                if (! empty($row['correo_empleado'])) {
                    $employee = Employee::firstOrCreate(
                        ['email' => mb_strtolower(trim($row['correo_empleado']), 'UTF-8')],
                        [
                            'name' => $row['nombre_empleado'] ?? 'Empleado Importado',
                            'employee_code' => $row['no_empleado'] ?? null,
                            'department' => $row['departamento'] ?? 'General',
                            'position' => $row['puesto'] ?? 'General',
                            'status' => EmployeeStatus::Activo,
                        ]
                    );

                    $currentAssignment = $phoneLine->currentAssignment;
                    if (! $currentAssignment || $currentAssignment->employee_id !== $employee->id) {
                        if ($currentAssignment) {
                            $currentAssignment->update(['returned_at' => now()]);
                        }
                        $this->assignmentService->assign(
                            $phoneLine,
                            $employee,
                            [
                                'notes' => 'Asignado automáticamente durante importación masiva.',
                            ]
                        );
                        if ($phoneLine->status->value !== PhoneLineStatus::Asignada->value) {
                            $phoneLine->update(['status' => PhoneLineStatus::Asignada]);
                        }
                    }
                }
            }
        });
    }

    public function rules(): array
    {
        return [
            'numero_telefonico' => ['required', 'string'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'numero_telefonico.required' => 'El Número Telefónico es obligatorio.',
        ];
    }
}

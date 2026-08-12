<?php

namespace App\Imports;

use App\Models\PhoneLine;
use App\Enums\PhoneLineStatus;
use App\Models\Employee;
use App\Enums\EmployeeStatus;
use App\Services\PhoneLineAssignmentService;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\DB;

class PhoneLinesImport implements ToCollection, WithHeadingRow, WithValidation, SkipsEmptyRows
{
    private $assignmentService;

    public function __construct()
    {
        $this->assignmentService = app(PhoneLineAssignmentService::class);
    }

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Las cabeceras normalizadas suelen ser snake_case de los nombres con minúsculas
                $phoneLine = PhoneLine::create([
                    'number'      => $row['numero_telefonico'],
                    'provider'    => $row['proveedor'] ?? null,
                    'data_plan'   => $row['plan_de_datos'] ?? null,
                    'plan_cost'   => $row['costo_del_plan'] ? (float) $row['costo_del_plan'] : null,
                    'status'      => PhoneLineStatus::Disponible,
                    'notes'       => $row['notas'] ?? null,
                ]);

                // Si viene un correo de empleado, crearlo/buscarlo y asignarlo
                if (!empty($row['correo_empleado'])) {
                    $employee = Employee::firstOrCreate(
                        ['email' => mb_strtolower(trim($row['correo_empleado']), 'UTF-8')],
                        [
                            'name'           => $row['nombre_empleado'] ?? 'Empleado Importado',
                            'employee_code'  => $row['no_empleado'] ?? null,
                            'department'     => $row['departamento'] ?? 'General',
                            'position'       => $row['puesto'] ?? 'General',
                            'status'         => EmployeeStatus::Activo,
                        ]
                    );

                    $this->assignmentService->assign(
                        $phoneLine,
                        $employee,
                        [
                            'notes' => 'Asignado automáticamente durante importación masiva.',
                        ]
                    );
                }
            }
        });
    }

    public function rules(): array
    {
        return [
            'numero_telefonico' => ['required', 'string', 'unique:phone_lines,number'],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'numero_telefonico.required' => 'El Número Telefónico es obligatorio.',
            'numero_telefonico.unique'   => 'El Número Telefónico :input ya existe en la base de datos.',
        ];
    }
}

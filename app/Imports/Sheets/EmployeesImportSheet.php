<?php

namespace App\Imports\Sheets;

use App\Models\Employee;
use App\Enums\EmployeeStatus;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\DB;

class EmployeesImportSheet implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Obtener email o número de empleado como identificador
                $email = mb_strtolower(trim((string) ($row['email'] ?? $row['correo'] ?? '')), 'UTF-8');
                $code  = trim((string) ($row['numero_de_empleado'] ?? $row['no_empleado'] ?? ''));

                if (empty($email) && empty($code)) {
                    continue; // Saltar si no tiene ni email ni número de empleado
                }
                if ($email === 'n/a' && empty($code)) {
                    continue;
                }

                $existingEmployee = null;
                if (!empty($email) && $email !== 'n/a') {
                    $existingEmployee = Employee::where('email', $email)->first();
                }
                if (!$existingEmployee && !empty($code) && $code !== 'N/A') {
                    $existingEmployee = Employee::where('employee_code', $code)->first();
                }

                $statusInput = mb_strtolower(trim((string) ($row['estatus'] ?? '')), 'UTF-8');
                $targetStatus = $this->parseEmployeeStatus($statusInput);

                $employeeData = [
                    'employee_code'  => !empty($code) && $code !== 'N/A' ? $code : ($existingEmployee?->employee_code ?? null),
                    'domain_account' => !empty($row['cuenta_de_dominio']) && $row['cuenta_de_dominio'] !== 'N/A' ? (string) $row['cuenta_de_dominio'] : ($existingEmployee?->domain_account ?? null),
                    'name'           => !empty($row['nombre']) && $row['nombre'] !== 'N/A' ? (string) $row['nombre'] : ($existingEmployee?->name ?? 'Empleado Importado'),
                    'email'          => !empty($email) && $email !== 'n/a' ? $email : ($existingEmployee?->email ?? ($code ? "empleado_{$code}@local.dev" : 'sin-email@local.dev')),
                    'phone'          => !empty($row['telefono']) && $row['telefono'] !== 'N/A' ? (string) $row['telefono'] : ($existingEmployee?->phone ?? null),
                    'department'     => !empty($row['departamento']) && $row['departamento'] !== 'N/A' ? (string) $row['departamento'] : ($existingEmployee?->department ?? 'General'),
                    'position'       => !empty($row['puesto']) && $row['puesto'] !== 'N/A' ? (string) $row['puesto'] : ($existingEmployee?->position ?? 'Colaborador'),
                    'status'         => $targetStatus,
                    'notes'          => !empty($row['notas']) && $row['notas'] !== 'N/A' ? (string) $row['notas'] : ($existingEmployee?->notes ?? null),
                ];

                if (!$existingEmployee) {
                    Employee::create($employeeData);
                } else {
                    $existingEmployee->update($employeeData);
                }
            }
        });
    }

    private function parseEmployeeStatus(string $input): EmployeeStatus
    {
        $clean = mb_strtolower(trim($input), 'UTF-8');
        $cleanNorm = str_replace(['á','é','í','ó','ú'], ['a','e','i','o','u'], $clean);
        
        return match ($cleanNorm) {
            'inactivo', 'suspension' => EmployeeStatus::Inactivo,
            'dado de baja', 'baja', 'despedido', 'renuncia' => EmployeeStatus::Baja,
            default => EmployeeStatus::Activo,
        };
    }
}

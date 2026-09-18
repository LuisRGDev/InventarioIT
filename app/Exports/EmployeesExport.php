<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeesExport implements FromCollection, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping
{
    public function collection()
    {
        return Employee::active()->withCount('currentAssignments')->get();
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Número de Empleado',
            'Cuenta de Dominio',
            'Nombre',
            'Email',
            'Teléfono',
            'Departamento',
            'Puesto',
            'Estatus',
            'Notas',
            'Equipos Asignados',
            'Fecha de Registro',
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->employee_code,
            $employee->domain_account,
            $employee->name,
            $employee->email,
            $employee->phone,
            $employee->department,
            $employee->position,
            $employee->status->label(),
            $employee->notes,
            $employee->current_assignments_count,
            $employee->created_at->format('Y-m-d'),
        ];
    }
}

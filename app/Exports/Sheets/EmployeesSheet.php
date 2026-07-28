<?php

namespace App\Exports\Sheets;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeesSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    public function collection()
    {
        return Employee::with('currentAssignments')->get();
    }

    public function headings(): array
    {
        return [
            'ID Empleado',
            'Número de Empleado',
            'Cuenta de Dominio',
            'Nombre',
            'Email',
            'Teléfono',
            'Departamento',
            'Puesto',
            'Estatus',
            'Total Equipos Asignados',
            'Notas',
            'Fecha de Registro',
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->id,
            $employee->employee_code,
            $employee->domain_account ?? 'N/A',
            $employee->name,
            $employee->email ?? 'N/A',
            $employee->phone ?? 'N/A',
            $employee->department ?? 'N/A',
            $employee->position ?? 'N/A',
            $employee->status->label(),
            $employee->currentAssignments->count(),
            $employee->notes ?? '',
            $employee->created_at->format('Y-m-d'),
        ];
    }

    public function title(): string
    {
        return 'Padrón de Empleados';
    }
}

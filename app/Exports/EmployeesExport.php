<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Employee::all();
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
            $employee->currentAssignments()->count(),
            $employee->created_at->format('Y-m-d'),
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\OfficeExtension;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OfficeExtensionsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return OfficeExtension::with('currentAssignment.employee')->get();
    }

    public function headings(): array
    {
        return [
            'Número de Extensión',
            'Número Directo',
            'Estatus',
            'Notas',
            'Nombre Empleado',
            'Correo Empleado',
            'Departamento'
        ];
    }

    public function map($extension): array
    {
        $employeeName = 'N/A';
        $employeeEmail = 'N/A';
        $department   = 'N/A';
        
        if ($extension->currentAssignment && $extension->currentAssignment->employee) {
            $employeeName = $extension->currentAssignment->employee->name;
            $employeeEmail = $extension->currentAssignment->employee->email ?? 'N/A';
            $department   = $extension->currentAssignment->employee->department;
        }

        return [
            $extension->extension_number,
            $extension->direct_number ?? 'N/A',
            $extension->status->label(),
            $extension->notes ?? '',
            $employeeName,
            $employeeEmail,
            $department
        ];
    }
}

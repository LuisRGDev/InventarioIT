<?php

namespace App\Exports;

use App\Exports\Concerns\EscapesFormulaInjection;
use App\Models\OfficeExtension;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class OfficeExtensionsExport implements FromQuery, WithChunkReading, WithCustomValueBinder, WithHeadings, WithMapping
{
    use EscapesFormulaInjection;

    public function query()
    {
        return OfficeExtension::query()->with('currentAssignment.employee');
    }

    public function chunkSize(): int
    {
        return 1000;
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
            'Departamento',
        ];
    }

    public function map($extension): array
    {
        $employeeName = 'N/A';
        $employeeEmail = 'N/A';
        $department = 'N/A';

        if ($extension->currentAssignment && $extension->currentAssignment->employee) {
            $employeeName = $extension->currentAssignment->employee->name;
            $employeeEmail = $extension->currentAssignment->employee->email ?? 'N/A';
            $department = $extension->currentAssignment->employee->department;
        }

        return [
            $extension->extension_number,
            $extension->direct_number ?? 'N/A',
            $extension->status->label(),
            $extension->notes ?? '',
            $employeeName,
            $employeeEmail,
            $department,
        ];
    }
}

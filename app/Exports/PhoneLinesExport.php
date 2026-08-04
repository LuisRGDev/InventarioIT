<?php

namespace App\Exports;

use App\Models\PhoneLine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PhoneLinesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return PhoneLine::with(['currentAssignment.employee.currentDevices.category'])->get();
    }

    public function headings(): array
    {
        return [
            'Nombre del empleado',
            'Correo',
            'Línea/Número de teléfono',
            'Marca y modelo del celular asignado',
            'Tipo de plan',
            'Costo del plan',
        ];
    }

    public function map($phoneLine): array
    {
        $employee = $phoneLine->currentAssignment?->employee;
        
        $assignedSmartphone = 'Ninguno';
        if ($employee) {
            $smartphone = $employee->currentDevices->first(function ($device) {
                return $device->category && $device->category->slug === 'smartphone';
            });
            if ($smartphone) {
                $assignedSmartphone = trim($smartphone->brand . ' ' . $smartphone->model);
            }
        }

        return [
            $employee ? $employee->name : 'No Asignada',
            $employee ? $employee->email : 'N/A',
            $phoneLine->number,
            $assignedSmartphone,
            $phoneLine->data_plan ?: 'N/A',
            $phoneLine->plan_cost ? '$' . number_format($phoneLine->plan_cost, 2) : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

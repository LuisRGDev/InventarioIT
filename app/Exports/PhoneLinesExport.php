<?php

namespace App\Exports;

use App\Models\PhoneLine;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PhoneLinesExport implements FromCollection, ShouldAutoSize, WithChunkReading, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return PhoneLine::with(['currentAssignment.employee.currentDevices.category'])->get();
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function headings(): array
    {
        return [
            'Número Telefónico',
            'Proveedor',
            'Plan de Datos',
            'Costo del Plan',
            'Notas',
            'Nombre Empleado',
            'Correo Empleado',
            'No. Empleado',
            'Departamento',
            'Puesto',
            'Smartphone Asignado',
        ];
    }

    public function map($phoneLine): array
    {
        $employee = $phoneLine->currentAssignment?->employee;

        $smartphone = 'Ninguno';
        if ($employee) {
            $device = $employee->currentDevices->first(function ($device) {
                return $device->category && $device->category->slug === 'smartphone';
            });
            if ($device) {
                $smartphone = trim($device->brand.' '.$device->model);
            }
        }

        return [
            $phoneLine->number,
            $phoneLine->provider ?? '',
            $phoneLine->data_plan ?? '',
            $phoneLine->plan_cost ? $phoneLine->plan_cost : '',
            $phoneLine->notes ?? '',
            $employee ? $employee->name : '',
            $employee ? $employee->email : '',
            $employee ? $employee->employee_code : '',
            $employee ? $employee->department : '',
            $employee ? $employee->position : '',
            $smartphone,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

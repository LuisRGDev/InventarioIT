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

    public function map(): array
    {
         = ->currentAssignment?->employee;
        
         = 'Ninguno';
        if () {
             = ->currentDevices->first(function (\) {
                return \->category && \->category->slug === 'smartphone';
            });
            if (\) {
                \ = trim(\->brand . ' ' . \->model);
            }
        }

        return [
            \->number,
            \->provider ?? '',
            \->data_plan ?? '',
            \->plan_cost ? \->plan_cost : '',
            \->notes ?? '',
            \ ? \->name : '',
            \ ? \->email : '',
            \ ? \->employee_code : '',
            \ ? \->department : '',
            \ ? \->position : '',
            \,
        ];
    }

    public function styles(Worksheet \)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

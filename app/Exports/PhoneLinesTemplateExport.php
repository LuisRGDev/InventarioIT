<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PhoneLinesTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                '55 1234 5678',
                'Telcel',
                'Telcel Max Sin Límite 3000',
                '399.00',
                'Línea asignada a ventas',
                'Juan Perez',
                'juan.perez@itam.local',
                'EMP-001',
                'Ventas',
                'Gerente',
                '',
            ],
            [
                '55 8765 4321',
                'AT&T',
                'AT&T Consíguelo 5',
                '599.00',
                'Línea de guardia',
                '',
                '',
                '',
                '',
                '',
                '',
            ]
        ];
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
            'Smartphone Asignado'
        ];
    }
}

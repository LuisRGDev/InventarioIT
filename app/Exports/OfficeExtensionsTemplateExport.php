<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class OfficeExtensionsTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                '101',
                '5551234567',
                'asignada',
                'Extension en sala de juntas',
                'Maria Gomez',
                'maria.gomez@itam.local',
                'Recursos Humanos'
            ],
            [
                '102',
                '',
                'disponible',
                '',
                '',
                '',
                ''
            ]
        ];
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
}

<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmployeesTemplateSheet implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    public function array(): array
    {
        return [
            [
                'EMP-ID-01',
                'EMP-102',
                'carlos.gomez',
                'Carlos Gómez',
                'carlos.gomez@empresa.local',
                '5511223344',
                'Sistemas IT',
                'Administrador de Redes',
                'Activo',
                '1',
                'Responsable de infraestructura e inventario',
                '2024-03-01',
            ],
            [
                'EMP-ID-02',
                'EMP-103',
                'maria.lopez',
                'María López',
                'maria.lopez@empresa.local',
                '5522334455',
                'Ventas',
                'Ejecutiva Comercial',
                'Activo',
                '1',
                'Personal de campo zona norte',
                '2024-01-20',
            ]
        ];
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

    public function title(): string
    {
        return 'Padrón de Empleados';
    }
}

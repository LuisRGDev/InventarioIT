<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class AssignedDevicesTemplateSheet implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    public function array(): array
    {
        return [
            [
                'EQ-1001',
                'Asignado',
                'Carlos Gómez',
                'carlos.gomez@empresa.local',
                'EMP-102',
                'Sistemas IT',
                'Administrador de Redes',
                'Portátil',
                'Lenovo',
                'ThinkPad T14',
                'PF3AB890',
                'LT-IT-GOMEZ',
                '00:1A:2B:3C:4D:77',
                '00:1A:2B:3C:4D:78',
                '2024-03-01',
                '2027-03-01',
                'Intel Core i7',
                '8',
                '16GB',
                '512GB SSD',
                'Windows 11 Pro',
                '',
                '',
                '',
                'Equipo asignado en perfectas condiciones'
            ],
            [
                'EQ-1002',
                'Asignado',
                'María López',
                'maria.lopez@empresa.local',
                'EMP-103',
                'Ventas',
                'Ejecutiva Comercial',
                'Smartphone',
                'Samsung',
                'Galaxy S23',
                'SM-S911-34567',
                'CEL-VENTAS-02',
                '',
                '00:1C:3B:5D:8A:22',
                '2024-01-20',
                '2025-01-20',
                'Snapdragon 8 Gen 2',
                '8',
                '8GB',
                '256GB',
                'Android 14',
                '5598765432',
                '869453051234567',
                'Plan Empresarial Ilimitado 5G',
                'Incluye funda de protección'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'ID Equipo',
            'Estatus',
            'Empleado Asignado',
            'Correo Empleado',
            'No. Empleado',
            'Departamento',
            'Puesto',
            'Categoría',
            'Marca',
            'Modelo',
            'Número de Serie',
            'Hostname / Nombre',
            'MAC Ethernet',
            'MAC WiFi',
            'Fecha Compra',
            'Garantía Expira',
            'Procesador (CPU)',
            'Núcleos',
            'RAM',
            'Almacenamiento',
            'Sistema Operativo',
            'Teléfono',
            'IMEI',
            'Plan de Datos',
            'Notas del Equipo'
        ];
    }

    public function title(): string
    {
        return 'Equipos Asignados';
    }
}

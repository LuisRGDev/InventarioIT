<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DevicesTemplateExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function array(): array
    {
        return [
            [
                'Portátil',
                'Dell',
                'Latitude 5420',
                'SN-12345678',
                'PC-ITAM-001',
                '00:1A:2B:3C:4D:5E',
                '00:1A:2B:3C:4D:5F',
                '2024-01-15',
                '2027-01-15',
                'Intel Core i5',
                '4',
                '16GB',
                '512GB SSD',
                'Windows 11 Pro',
                '',
                '',
                '',
                'Equipo nuevo para gerencia'
            ],
            [
                'Smartphone',
                'Apple',
                'iPhone 13',
                'IMEI-987654321',
                '',
                '',
                '',
                '2024-02-10',
                '2025-02-10',
                '',
                '',
                '',
                '128GB',
                'iOS 17',
                '5512345678',
                '987654321012345',
                'Plan Telcel Max 1000',
                'Celular de ventas'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Categoría',
            'Marca',
            'Modelo',
            'Número de Serie',
            'Hostname',
            'MAC Ethernet',
            'MAC WiFi',
            'Fecha Compra',
            'Garantía Expira',
            'Procesador CPU',
            'Núcleos',
            'RAM',
            'Almacenamiento',
            'Sistema Operativo',
            'Teléfono',
            'IMEI',
            'Plan de Datos',
            'Notas'
        ];
    }
}

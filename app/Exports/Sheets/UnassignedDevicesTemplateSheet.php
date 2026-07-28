<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class UnassignedDevicesTemplateSheet implements FromArray, WithHeadings, ShouldAutoSize, WithTitle
{
    public function array(): array
    {
        return [
            [
                'EQ-2001',
                'Disponible',
                'Monitor',
                'Dell',
                'UltraSharp U2723QE 27"',
                'CN-0A1B2C-74261-23A-0123',
                'MON-PISO-01',
                '',
                '',
                '2024-04-10',
                '2027-04-10',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                'Monitor 4K en almacén listo para asignación'
            ],
            [
                'EQ-2002',
                'Disponible',
                'Impresora',
                'HP',
                'LaserJet Pro M404dw',
                'VNB3K12345',
                'IMP-CONTABILIDAD',
                '00:25:9A:12:34:56',
                '00:25:9A:12:34:57',
                '2023-11-05',
                '2025-11-05',
                '',
                '',
                '256MB',
                '',
                '',
                '',
                '',
                '',
                'Impresora de red en segundo piso'
            ],
            [
                'EQ-2003',
                'En Reparación',
                'Portátil',
                'HP',
                'EliteBook 840 G8',
                '5CG1234567',
                'LT-REP-01',
                'A4:4E:31:00:11:22',
                'A4:4E:31:00:11:23',
                '2022-08-15',
                '2025-08-15',
                'Intel Core i5',
                '4',
                '16GB',
                '256GB SSD',
                'Windows 11 Pro',
                '',
                '',
                '',
                'Enviado a garantía por cambio de teclado'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'ID Equipo',
            'Estatus Actual',
            'Categoría / Tipo',
            'Marca',
            'Modelo',
            'Número de Serie',
            'Hostname / Identificador',
            'MAC Ethernet',
            'MAC WiFi',
            'Fecha Compra',
            'Garantía Expira',
            'Procesador (CPU)',
            'Núcleos',
            'RAM',
            'Almacenamiento',
            'Sistema Operativo',
            'Teléfono / Número',
            'IMEI',
            'Plan de Datos',
            'Notas / Ubicación en Almacén'
        ];
    }

    public function title(): string
    {
        return 'Equipos Sin Asignar (Stock)';
    }
}

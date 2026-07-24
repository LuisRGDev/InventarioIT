<?php

namespace App\Exports;

use App\Models\Device;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DevicesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return Device::with(['category', 'currentAssignment.employee'])->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Categoría',
            'Marca',
            'Modelo',
            'Número de Serie',
            'Hostname',
            'MAC Ethernet',
            'MAC WiFi',
            'Estatus',
            'Empleado Asignado',
            'Correo Empleado',
            'No. Empleado',
            'Departamento',
            'Puesto',
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
            'Notas'
        ];
    }

    public function map($device): array
    {
        return [
            $device->id,
            $device->category?->name ?? '',
            $device->brand,
            $device->model,
            $device->serial_number,
            $device->computer_name,
            $device->mac_address_ethernet,
            $device->mac_address_wifi,
            $device->status->label(),
            $device->currentAssignment?->employee?->name ?? 'N/A',
            $device->currentAssignment?->employee?->email ?? 'N/A',
            $device->currentAssignment?->employee?->employee_code ?? 'N/A',
            $device->currentAssignment?->employee?->department ?? 'N/A',
            $device->currentAssignment?->employee?->position ?? 'N/A',
            $device->purchase_date?->format('Y-m-d'),
            $device->warranty_expires_at?->format('Y-m-d'),
            $device->specs['cpu'] ?? '',
            $device->specs['cores'] ?? '',
            $device->specs['ram'] ?? '',
            $device->specs['storage'] ?? '',
            $device->specs['os'] ?? '',
            $device->specs['phone_number'] ?? '',
            $device->specs['imei'] ?? '',
            $device->specs['data_plan'] ?? '',
            $device->notes,
        ];
    }
}

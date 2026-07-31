<?php

namespace App\Exports\Sheets;

use App\Models\Device;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class UnassignedDevicesSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    public function collection()
    {
        return Device::whereDoesntHave('currentAssignment')->with(['category'])->get();
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
            'Costo de Plan',
            'Notas / Ubicación en Almacén'
        ];
    }

    public function map($device): array
    {
        return [
            $device->id,
            $device->status->label(),
            $device->category?->name ?? 'Sin Categoría',
            $device->brand,
            $device->model,
            $device->serial_number,
            $device->computer_name ?? 'N/A',
            $device->mac_address_ethernet ?? 'N/A',
            $device->mac_address_wifi ?? 'N/A',
            $device->purchase_date?->format('Y-m-d') ?? 'N/A',
            $device->warranty_expires_at?->format('Y-m-d') ?? 'N/A',
            $device->specs['cpu'] ?? '',
            $device->specs['cores'] ?? '',
            $device->specs['ram'] ?? '',
            $device->specs['storage'] ?? '',
            $device->specs['os'] ?? '',
            $device->phone_number ?? '',
            $device->imei ?? '',
            $device->data_plan ?? '',
            $device->plan_cost ?? '',
            $device->notes ?? '',
        ];
    }

    public function title(): string
    {
        return 'Equipos Sin Asignar (Stock)';
    }
}

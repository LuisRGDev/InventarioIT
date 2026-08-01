<?php

namespace App\Exports\Sheets;

use App\Models\Device;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class AssignedDevicesSheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    public function collection()
    {
        return Device::whereHas('currentAssignment')->with(['category', 'currentAssignment.employee'])->get();
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
            'IMEI',
            'Notas del Equipo'
        ];
    }

    public function map($device): array
    {
        $employee = $device->currentAssignment?->employee;

        return [
            $device->id,
            $device->status->label(),
            $employee?->name ?? 'N/A',
            $employee?->email ?? 'N/A',
            $employee?->employee_code ?? 'N/A',
            $employee?->department ?? 'N/A',
            $employee?->position ?? 'N/A',
            $device->category?->name ?? 'N/A',
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
            $device->imei ?? '',
            $device->notes ?? '',
        ];
    }

    public function title(): string
    {
        return 'Equipos Asignados';
    }
}

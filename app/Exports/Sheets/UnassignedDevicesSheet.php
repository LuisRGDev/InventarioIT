<?php

namespace App\Exports\Sheets;

use App\Exports\Concerns\EscapesFormulaInjection;
use App\Models\Device;
use App\Support\Roles;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class UnassignedDevicesSheet implements FromCollection, ShouldAutoSize, WithChunkReading, WithCustomValueBinder, WithHeadings, WithMapping, WithTitle
{
    use EscapesFormulaInjection;

    protected bool $canViewBitlocker;

    public function __construct()
    {
        $this->canViewBitlocker = auth()->user()?->hasRole(Roles::ADMIN) ?? false;
    }

    public function collection()
    {
        return Device::whereDoesntHave('currentAssignment')->with(['category'])->get();
    }

    public function chunkSize(): int
    {
        return 1000;
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
            'Identificador de BL',
            'Clave de BL',
            'IMEI',
            'Notas / Ubicación en Almacén',
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
            $this->canViewBitlocker ? $device->bitlocker_identifier : '***',
            $this->canViewBitlocker ? $device->bitlocker_key : '***',
            $device->imei ?? '',
            $device->notes ?? '',
        ];
    }

    public function title(): string
    {
        return 'Equipos Sin Asignar (Stock)';
    }
}

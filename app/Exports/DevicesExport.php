<?php

namespace App\Exports;

use App\Exports\Concerns\EscapesFormulaInjection;
use App\Models\Device;
use App\Support\Roles;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DevicesExport implements FromQuery, ShouldAutoSize, WithChunkReading, WithCustomValueBinder, WithHeadings, WithMapping
{
    use EscapesFormulaInjection;

    /**
     * Las llaves BitLocker solo se exponen a Admin TI; para el resto de
     * roles con acceso a exportar (Técnico, Solo lectura) se redactan.
     */
    protected bool $canViewBitlocker;

    public function __construct()
    {
        $this->canViewBitlocker = auth()->user()?->hasRole(Roles::ADMIN) ?? false;
    }

    public function query()
    {
        // FromQuery (en vez de FromCollection) para que WithChunkReading
        // funcione de verdad: FromCollection carga toda la tabla en memoria
        // de una sola vez con ->get() antes de que el chunking pudiera
        // aplicar (Hallazgo Alto H9 de la auditoría).
        return Device::query()->with(['category', 'currentAssignment.employee']);
    }

    public function chunkSize(): int
    {
        return 1000;
    }

    public function headings(): array
    {
        return [
            'ID',
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
            'Etiqueta de Servicio',
            'Hostname',
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
            'Notas',
        ];
    }

    public function map($device): array
    {
        return [
            $device->id,
            $device->status->label(),
            $device->currentAssignment?->employee?->name ?? 'N/A',
            $device->currentAssignment?->employee?->email ?? 'N/A',
            $device->currentAssignment?->employee?->employee_code ?? 'N/A',
            $device->currentAssignment?->employee?->department ?? 'N/A',
            $device->currentAssignment?->employee?->position ?? 'N/A',
            $device->category?->name ?? '',
            $device->brand,
            $device->model,
            $device->serial_number,
            $device->service_tag,
            $device->computer_name,
            $device->mac_address_ethernet,
            $device->mac_address_wifi,
            $device->purchase_date?->format('Y-m-d'),
            $device->warranty_expires_at?->format('Y-m-d'),
            $device->specs['cpu'] ?? '',
            $device->specs['cores'] ?? '',
            $device->specs['ram'] ?? '',
            $device->specs['storage'] ?? '',
            $device->specs['os'] ?? '',
            $this->canViewBitlocker ? $device->bitlocker_identifier : '***',
            $this->canViewBitlocker ? $device->bitlocker_key : '***',
            $device->imei,
            $device->notes,
        ];
    }
}

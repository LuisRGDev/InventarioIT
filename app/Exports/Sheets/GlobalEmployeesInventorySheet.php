<?php

namespace App\Exports\Sheets;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithTitle;

class GlobalEmployeesInventorySheet implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithTitle
{
    public function collection()
    {
        return Employee::with(['currentAssignments.device.category'])->get();
    }

    public function headings(): array
    {
        return [
            'Numero de empleado',
            'Empleado',
            'Correo',
            'Departamento',
            'Puesto',
            'Usuario de dominio',
            'Categoria (Desktop/Portatil)',
            'Marca',
            'Modelo',
            'Procesador',
            'Sistema Operativo',
            'Almacenamiento',
            'RAM',
            'Nucleos',
            'Id del dispositivo',
            'Mac Ethernet',
            'Mac Wifi',
            'Status',
            'Tag Service',
            'Fecha de compra',
            'garantia Expira',
            'Marca de Celular',
            'Modelo',
            'IMEI',
            'Sistema operativo',
            'Tipo de plan',
            'Costo de plan',
            'Notas',
        ];
    }

    public function map($employee): array
    {
        $computer = null;
        $smartphone = null;

        foreach ($employee->currentAssignments as $assignment) {
            $device = $assignment->device;
            if ($device && $device->category) {
                if ($device->category->slug === 'smartphone') {
                    $smartphone = $device;
                } elseif (in_array($device->category->slug, ['portatil', 'desktop'])) {
                    $computer = $device;
                }
            }
        }

        return [
            $employee->employee_code ?? '',
            $employee->name ?? '',
            $employee->email ?? '',
            $employee->department ?? '',
            $employee->position ?? '',
            $employee->domain_account ?? '',
            
            // Computer fields
            $computer ? ($computer->category->name ?? '') : '',
            $computer->brand ?? '',
            $computer->model ?? '',
            $computer->specs['cpu'] ?? '',
            $computer->specs['os'] ?? '',
            $computer->specs['storage'] ?? '',
            $computer->specs['ram'] ?? '',
            $computer->specs['cores'] ?? '',
            $computer->id ?? '',
            $computer->mac_address_ethernet ?? '',
            $computer->mac_address_wifi ?? '',
            $computer ? $computer->status->label() : '',
            $computer->service_tag ?? '',
            $computer ? ($computer->purchase_date ? $computer->purchase_date->format('Y-m-d') : '') : '',
            $computer ? ($computer->warranty_expires_at ? $computer->warranty_expires_at->format('Y-m-d') : '') : '',
            
            // Mobile fields
            $smartphone->brand ?? '',
            $smartphone->model ?? '',
            $smartphone->imei ?? '',
            $smartphone->specs['os'] ?? '',
            $smartphone->data_plan ?? '',
            $smartphone->plan_cost ?? '',
            
            // Notes
            trim(implode(' | ', array_filter([
                $employee->notes,
                $computer ? $computer->notes : null,
                $smartphone ? $smartphone->notes : null
            ]))),
        ];
    }

    public function title(): string
    {
        return 'Inventario Asignado Global';
    }
}

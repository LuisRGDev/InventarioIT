<?php

namespace App\Exports;

use App\Models\DeviceMaintenance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MaintenancesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return DeviceMaintenance::with(['device', 'user', 'device.category'])->latest('started_at')->get();
    }

    public function headings(): array
    {
        return [
            'ID Mantenimiento',
            'Equipo / Categoría',
            'Marca y Modelo',
            'Número de Serie',
            'Tipo de Mantenimiento',
            'Estatus del Servicio',
            'Título / Asunto',
            'Descripción Inicial',
            'Notas de Solución',
            'Técnico Responsable',
            'Fecha Programada',
            'Fecha de Inicio en Taller',
            'Fecha de Finalización',
            'Próximo Mantenimiento Sugerido',
        ];
    }

    public function map($maintenance): array
    {
        return [
            "MANT-{$maintenance->id}",
            $maintenance->device ? "{$maintenance->device->category->name} - {$maintenance->device->computer_name}" : 'N/A',
            $maintenance->device ? "{$maintenance->device->brand} {$maintenance->device->model}" : 'N/A',
            $maintenance->device ? $maintenance->device->serial_number : 'N/A',
            $maintenance->type->label(),
            $maintenance->status->label(),
            $maintenance->title,
            $maintenance->description ?? 'N/A',
            $maintenance->resolution_notes ?? 'Pendiente / No especificado',
            $maintenance->user ? $maintenance->user->name : 'N/A',
            $maintenance->scheduled_at ? $maintenance->scheduled_at->format('Y-m-d') : 'N/A',
            $maintenance->started_at ? $maintenance->started_at->format('Y-m-d H:i') : 'N/A',
            $maintenance->completed_at ? $maintenance->completed_at->format('Y-m-d H:i') : 'N/A',
            $maintenance->next_due_at ? $maintenance->next_due_at->format('Y-m-d') : 'N/A',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF'], 'size' => 11],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF1E293B']], // Slate-800
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }
}

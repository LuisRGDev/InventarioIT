<?php

namespace App\Services;

use App\Models\DeviceAssignment;
use Carbon\Carbon;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ResponsiveLetterService
{
    public function generate(DeviceAssignment $assignment): string
    {
        $assignment->loadMissing(['device', 'employee', 'assignedBy']);

        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('CARTA RESPONSIVA', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        Carbon::setLocale('es');
        $fecha = $assignment->assigned_at->translatedFormat('d \d\e F \d\e Y');
        $section->addText('Fecha: ' . $fecha);

        $device = $assignment->device;
        $equipoInfo = $device->brand . ' ' . $device->model;

        if ($device->imei) {
            $equipoInfo .= ', IMEI: ' . $device->imei;
        } else {
            $equipoInfo .= ', SN: ' . $device->serial_number;
        }

        $section->addText('Equipo: ' . $equipoInfo);
        $section->addText('Teléfono: ' . ($device->phone_number ?: 'N/A'));
        $section->addText('Empleado: ' . $assignment->employee->name);
        $section->addText('Entregó: ' . ($assignment->assignedBy ? $assignment->assignedBy->name : 'N/A'));
        $section->addTextBreak(2);
        $section->addText('Recibí de conformidad el equipo anterior.');

        $fileName = 'carta_responsiva_' . $assignment->id . '_' . time() . '.docx';
        $outputPath = $tempDir . '/' . $fileName;

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($outputPath);

        return $outputPath;
    }
}

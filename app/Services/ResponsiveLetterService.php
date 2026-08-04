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

        $tempPath = storage_path('app/temp');
        if (!is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $section->addText('CARTA RESPONSIVA', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        $section->addTextBreak(1);

        Carbon::setLocale('es');
        $dateText = $assignment->assigned_at->translatedFormat('d \d\e F \d\e Y');
        $section->addText('Fecha: ' . $dateText);

        $device = $assignment->device;
        $equipoText = $device->brand . ' ' . $device->model;

        if ($device->imei) {
            $equipoText .= ', IMEI: ' . $device->imei;
        } else {
            $equipoText .= ', SN: ' . $device->serial_number;
        }

        $section->addText('Equipo: ' . $equipoText);
        
        $phoneLine = $assignment->employee->currentPhoneLines()->first();
        $phoneNumber = $phoneLine ? $phoneLine->number : 'N/A';
        $section->addText('Teléfono: ' . $phoneNumber);
        
        $extension = $assignment->employee->currentOfficeExtensions()->first();
        $extensionText = $extension ? 'Ext. ' . $extension->extension_number . ($extension->direct_number ? ' (' . $extension->direct_number . ')' : '') : 'N/A';
        $section->addText('Extensión: ' . $extensionText);
        
        $section->addText('Empleado: ' . $assignment->employee->name);
        $section->addText('Entregó: ' . ($assignment->assignedBy ? $assignment->assignedBy->name : 'N/A'));
        $section->addTextBreak(2);
        $section->addText('Recibí de conformidad el equipo anterior.');

        $fileName = 'carta_responsiva_' . $assignment->id . '_' . time() . '.docx';
        $filePath = $tempPath . '/' . $fileName;

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        return $filePath;
    }
}

<?php

namespace App\Services;

use App\Models\DeviceAssignment;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;

class ResponsiveLetterService
{
    public function generate(DeviceAssignment $assignment): string
    {
        $assignment->loadMissing(['device.category', 'employee', 'assignedBy']);

        $tempPath = storage_path('app/temp');
        if (! is_dir($tempPath)) {
            mkdir($tempPath, 0755, true);
        }

        $phpWord = new PhpWord;
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'paperSize' => 'Letter',
            'marginTop' => 1440,
            'marginBottom' => 1440,
            'marginLeft' => 1800,
            'marginRight' => 1800,
            'headerHeight' => 1080,
        ]);

        $header = $section->addHeader();
        $header->addImage(public_path('images/responsiva-header.png'), [
            'width' => 432,
            'height' => 73,
            'alignment' => Jc::CENTER,
        ]);

        $device = $assignment->device;
        $employee = $assignment->employee;

        $equipoDescripcion = trim($device->brand.' '.$device->model);
        $equipoDescripcion .= $device->imei
            ? ', No. de Serie/IMEI: '.$device->imei
            : ', No. de Serie/IMEI: '.$device->serial_number;

        $fecha = $assignment->assigned_at->locale('es')->translatedFormat('l d \d\e F \d\e Y');

        $section->addTextBreak(1);
        $section->addText("Tlalnepantla de Baz, Estado de México, {$fecha}");
        $section->addTextBreak(1);

        $section->addText(
            'Por medio de la presente, hago constar que he recibido de Middleby Worldwide el siguiente equipo:',
            ['bold' => true],
            ['spacing' => 276, 'alignment' => Jc::BOTH]
        );
        $section->addTextBreak(1);

        $section->addListItem($equipoDescripcion, 0, null, null, ['alignment' => Jc::BOTH]);

        if ($device->category?->isComputer() || $device->category?->isSmartphone()) {
            $section->addListItem('Cargador', 0, null, null, ['alignment' => Jc::BOTH]);
        }

        $phoneLine = $employee->currentPhoneLines()->first();
        if ($phoneLine) {
            $section->addListItem('Número telefónico: '.$phoneLine->number, 0, null, null, ['alignment' => Jc::BOTH]);
        }

        $section->addTextBreak(1);

        $section->addText(
            'Me comprometo a utilizar este equipo y sus accesorios exclusivamente para los fines establecidos '.
            'en mi relación contractual con la empresa. Garantizo mantener el equipo en condiciones óptimas y '.
            'protegerlo contra cualquier daño o pérdida. En caso de negligencia de mi parte que derive en daño '.
            'o pérdida del equipo, asumo la total responsabilidad de reponerlo o cubrir su valor.',
            [],
            ['spacing' => 276, 'alignment' => Jc::BOTH]
        );
        $section->addTextBreak(1);

        $section->addText(
            'Asimismo, me comprometo a notificar de inmediato a mis jefes directos y al departamento de '.
            'Tecnología, utilizando los canales oficiales, ante cualquier incidente que pudiera comprometer '.
            'la integridad del equipo o su funcionamiento.',
            [],
            ['spacing' => 276, 'alignment' => Jc::BOTH]
        );
        $section->addTextBreak(2);

        $section->addText('Atentamente');
        $section->addTextBreak(2);
        $section->addText('__________________________', [], ['alignment' => Jc::CENTER]);
        $section->addText($employee->name, [], ['alignment' => Jc::CENTER]);
        $section->addTextBreak(1);

        $entregoNombre = $assignment->assignedBy ? $assignment->assignedBy->name : 'N/A';
        $section->addText('Entrego: '.$entregoNombre);

        $fileName = 'carta_responsiva_'.$assignment->id.'_'.time().'.docx';
        $filePath = $tempPath.'/'.$fileName;

        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save($filePath);

        return $filePath;
    }
}

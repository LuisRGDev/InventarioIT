<?php

namespace App\Services;

use App\Models\DeviceAssignment;
use Carbon\Carbon;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ResponsiveLetterService
{
    public function generate(DeviceAssignment ): string
    {
        ->loadMissing(['device', 'employee', 'assignedBy']);

         = storage_path('app/temp');
        if (!is_dir()) {
            mkdir(, 0755, true);
        }

         = new PhpWord();
         = ->addSection();
        ->addText('CARTA RESPONSIVA', ['bold' => true, 'size' => 16], ['alignment' => 'center']);
        ->addTextBreak(1);

        Carbon::setLocale('es');
         = ->assigned_at->translatedFormat('d \d\e F \d\e Y');
        ->addText('Fecha: ' . );

         = ->device;
         = ->brand . ' ' . ->model;

        if (->imei) {
             .= ', IMEI: ' . ->imei;
        } else {
             .= ', SN: ' . ->serial_number;
        }

        ->addText('Equipo: ' . );
        ->addText('Teléfono: ' . (->phone_number ?: 'N/A'));
        ->addText('Empleado: ' . ->employee->name);
        ->addText('Entregó: ' . (->assignedBy ? ->assignedBy->name : 'N/A'));
        ->addTextBreak(2);
        ->addText('Recibí de conformidad el equipo anterior.');

         = 'carta_responsiva_' . ->id . '_' . time() . '.docx';
         =  . '/' . ;

         = IOFactory::createWriter(, 'Word2007');
        ->save();

        return ;
    }
}

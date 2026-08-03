<?php

namespace App\Services;

use App\Models\DeviceAssignment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class ResponsiveLetterService
{
    /**
     * Genera el .docx de la carta responsiva para una asignación.
     * 
     * @param DeviceAssignment $assignment (con relaciones cargadas)
     * @return string Ruta del archivo generado
     */
    public function generate(DeviceAssignment $assignment): string
    {
        $assignment->loadMissing(['device', 'employee', 'assignedBy']);
        
        // La ruta de la plantilla original
        $templatePath = storage_path('app/templates/carta_responsiva_template.docx');
        
        if (!file_exists($templatePath)) {
            throw new \Exception("La plantilla de carta responsiva no existe en: {$templatePath}");
        }

        $template = new TemplateProcessor($templatePath);
        
        // Fecha formateada: "(día) de (mes) de (año)"
        // Asegurar que Carbon use español
        Carbon::setLocale('es');
        $fecha = $assignment->assigned_at->translatedFormat('d \d\e F \d\e Y');
        $template->setValue('FECHA', $fecha);
        
        // Info del equipo: "Marca Modelo, SN: xxx" o "Marca Modelo, IMEI: xxx"
        $device = $assignment->device;
        $equipoInfo = "{$device->brand} {$device->model}";
        
        if ($device->imei) {
            $equipoInfo .= ", IMEI: {$device->imei}";
        } else {
            $equipoInfo .= ", SN: {$device->serial_number}";
        }
        $template->setValue('EQUIPO_INFO', $equipoInfo);
        
        // Número telefónico (del equipo, si aplica)
        $numeroTelefonico = $device->phone_number ?: 'N/A';
        $template->setValue('NUMERO_TELEFONICO', $numeroTelefonico);
        
        // Nombre del empleado
        $template->setValue('NOMBRE_EMPLEADO', $assignment->employee->name);
        
        // Quién entregó
        $entrego = $assignment->assignedBy ? $assignment->assignedBy->name : 'N/A';
        $template->setValue('ENTREGO', $entrego);
        
        // Guardar en temp
        $tempDir = storage_path('app/temp');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        
        $fileName = 'carta_responsiva_' . $assignment->id . '_' . time() . '.docx';
        $outputPath = $tempDir . '/' . $fileName;
        
        $template->saveAs($outputPath);
        
        return $outputPath;
    }
}

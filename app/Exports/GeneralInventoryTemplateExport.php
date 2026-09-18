<?php

namespace App\Exports;

use App\Exports\Sheets\GlobalEmployeesInventoryTemplateSheet;
use App\Exports\Sheets\UnassignedDevicesTemplateSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GeneralInventoryTemplateExport implements WithMultipleSheets
{
    /**
     * Retorna las hojas de la plantilla general de ejemplo.
     */
    public function sheets(): array
    {
        return [
            new GlobalEmployeesInventoryTemplateSheet,
            new UnassignedDevicesTemplateSheet,
        ];
    }
}

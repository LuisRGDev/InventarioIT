<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\GlobalEmployeesInventoryTemplateSheet;
use App\Exports\Sheets\UnassignedDevicesTemplateSheet;

class GeneralInventoryTemplateExport implements WithMultipleSheets
{
    /**
     * Retorna las hojas de la plantilla general de ejemplo.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            new GlobalEmployeesInventoryTemplateSheet(),
            new UnassignedDevicesTemplateSheet(),
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\AssignedDevicesTemplateSheet;
use App\Exports\Sheets\UnassignedDevicesTemplateSheet;
use App\Exports\Sheets\EmployeesTemplateSheet;

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
            new AssignedDevicesTemplateSheet(),
            new UnassignedDevicesTemplateSheet(),
            new EmployeesTemplateSheet(),
        ];
    }
}

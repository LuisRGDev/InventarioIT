<?php

namespace App\Imports;

use App\Imports\Sheets\GlobalEmployeesInventoryImportSheet;
use App\Imports\Sheets\UnassignedDevicesImportSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GeneralInventoryImport implements WithMultipleSheets
{
    /**
     * Define los procesadores para cada pestaña del Excel (por índice).
     */
    public function sheets(): array
    {
        return [
            0 => new GlobalEmployeesInventoryImportSheet,
            1 => new UnassignedDevicesImportSheet,
        ];
    }
}

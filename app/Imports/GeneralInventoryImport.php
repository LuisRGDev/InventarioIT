<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\Sheets\GlobalEmployeesInventoryImportSheet;
use App\Imports\Sheets\UnassignedDevicesImportSheet;

class GeneralInventoryImport implements WithMultipleSheets
{
    /**
     * Define los procesadores para cada pestaña del Excel (por índice).
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            0 => new GlobalEmployeesInventoryImportSheet(),
            1 => new UnassignedDevicesImportSheet(),
        ];
    }
}

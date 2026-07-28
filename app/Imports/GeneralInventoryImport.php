<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Imports\Sheets\AssignedDevicesImportSheet;
use App\Imports\Sheets\UnassignedDevicesImportSheet;
use App\Imports\Sheets\EmployeesImportSheet;

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
            0 => new AssignedDevicesImportSheet(),
            1 => new UnassignedDevicesImportSheet(),
            2 => new EmployeesImportSheet(),
        ];
    }
}

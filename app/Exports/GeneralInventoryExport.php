<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\AssignedDevicesSheet;
use App\Exports\Sheets\UnassignedDevicesSheet;
use App\Exports\Sheets\EmployeesSheet;

class GeneralInventoryExport implements WithMultipleSheets
{
    /**
     * Retorna un arreglo con todas las hojas que tendrá el archivo Excel exportado desde el Dashboard.
     *
     * @return array
     */
    public function sheets(): array
    {
        return [
            new AssignedDevicesSheet(),
            new UnassignedDevicesSheet(),
            new EmployeesSheet(),
        ];
    }
}

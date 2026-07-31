<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\GlobalEmployeesInventorySheet;
use App\Exports\Sheets\UnassignedDevicesSheet;

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
            new GlobalEmployeesInventorySheet(),
            new UnassignedDevicesSheet(),
        ];
    }
}

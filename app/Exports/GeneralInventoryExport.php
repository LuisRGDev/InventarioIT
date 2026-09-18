<?php

namespace App\Exports;

use App\Exports\Sheets\GlobalEmployeesInventorySheet;
use App\Exports\Sheets\UnassignedDevicesSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GeneralInventoryExport implements WithMultipleSheets
{
    /**
     * Retorna un arreglo con todas las hojas que tendrá el archivo Excel exportado desde el Dashboard.
     */
    public function sheets(): array
    {
        return [
            new GlobalEmployeesInventorySheet,
            new UnassignedDevicesSheet,
        ];
    }
}

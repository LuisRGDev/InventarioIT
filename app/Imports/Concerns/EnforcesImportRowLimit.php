<?php

namespace App\Imports\Concerns;

use Illuminate\Support\Collection;

/**
 * Consolida el límite de filas por archivo, que se repetía idéntico al
 * inicio de collection() en DevicesImport y en los 3
 * Imports/Sheets/*ImportSheet (Hallazgo Alto H7 de la auditoría).
 */
trait EnforcesImportRowLimit
{
    protected function guardRowLimit(Collection $rows): void
    {
        $maxRows = config('inventory.import_max_rows', 5000);

        if ($rows->count() > $maxRows) {
            throw new \Exception("El archivo contiene {$rows->count()} filas. El máximo permitido es {$maxRows}.");
        }
    }
}

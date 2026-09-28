<?php

namespace App\Imports\Concerns;

use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

/**
 * Consolida el parseDate() que estaba duplicado (byte a byte, salvo por el
 * chequeo de "N/A") en DevicesImport y en los 3 Imports/Sheets/*ImportSheet
 * (Hallazgo Alto H7 de la auditoría). Antes de esta consolidación, un bug
 * corregido en una copia (como el de IMEI, Hallazgo Crítico C6) no se
 * propagaba automáticamente a las demás.
 */
trait ParsesExcelDates
{
    protected function parseDate($value): ?Carbon
    {
        if (empty($value) || $value === 'N/A' || $value === 'n/a') {
            return null;
        }

        try {
            // Excel a veces envía fechas como enteros (número de serie de Excel)
            if (is_numeric($value)) {
                return Carbon::instance(Date::excelToDateTimeObject($value));
            }

            // Respaldo para formato común dd/mm/yyyy o dd-mm-yyyy en español
            $valClean = trim((string) $value);
            if (preg_match('/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/', $valClean)) {
                $separator = str_contains($valClean, '/') ? '/' : '-';

                return Carbon::createFromFormat("d{$separator}m{$separator}Y", $valClean);
            }

            return Carbon::parse($valClean);
        } catch (\Exception $e) {
            return null;
        }
    }
}

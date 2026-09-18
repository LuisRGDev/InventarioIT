<?php

namespace App\Imports\Concerns;

use DateTime;

trait ParsesExcelDates
{
    protected function parseDate($value): ?DateTime
    {
        if (empty($value)) {
            return null;
        }

        if ($value instanceof DateTime) {
            return $value;
        }

        $value = (string) trim($value);

        // Try common date formats
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d', 'd-M-Y'];

        foreach ($formats as $format) {
            $parsed = DateTime::createFromFormat($format, $value);
            if ($parsed !== false) {
                return $parsed;
            }
        }

        // Last resort: strtotime
        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return (new DateTime)->setTimestamp($timestamp);
        }

        return null;
    }
}

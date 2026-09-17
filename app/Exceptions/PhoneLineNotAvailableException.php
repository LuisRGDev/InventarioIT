<?php

namespace App\Exceptions;

use Exception;

class PhoneLineNotAvailableException extends Exception
{
    public function __construct(string $message = 'La línea telefónica no está disponible para asignación.')
    {
        parent::__construct($message);
    }
}

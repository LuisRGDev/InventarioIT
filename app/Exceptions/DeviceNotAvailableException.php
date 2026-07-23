<?php

namespace App\Exceptions;

use Exception;

class DeviceNotAvailableException extends Exception
{
    public function __construct(string $message = 'El equipo no está disponible para asignación.')
    {
        parent::__construct($message);
    }
}

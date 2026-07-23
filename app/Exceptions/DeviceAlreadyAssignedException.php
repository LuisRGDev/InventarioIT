<?php

namespace App\Exceptions;

use Exception;

class DeviceAlreadyAssignedException extends Exception
{
    public function __construct(string $message = 'El equipo ya tiene una asignación activa.')
    {
        parent::__construct($message);
    }
}

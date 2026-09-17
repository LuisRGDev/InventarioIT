<?php

namespace App\Exceptions;

use Exception;

class PhoneLineAlreadyAssignedException extends Exception
{
    public function __construct(string $message = 'La línea telefónica ya tiene una asignación activa.')
    {
        parent::__construct($message);
    }
}

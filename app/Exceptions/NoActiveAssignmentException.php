<?php

namespace App\Exceptions;

use Exception;

class NoActiveAssignmentException extends Exception
{
    public function __construct(string $message = 'El equipo no tiene una asignación activa.')
    {
        parent::__construct($message);
    }
}

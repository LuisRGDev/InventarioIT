<?php

namespace App\Exceptions;

use Exception;

class ExtensionAlreadyAssignedException extends Exception
{
    public function __construct(string $message = 'La extensión ya tiene una asignación activa.')
    {
        parent::__construct($message);
    }
}

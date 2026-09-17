<?php

namespace App\Exceptions;

use Exception;

class ExtensionNotAvailableException extends Exception
{
    public function __construct(string $message = 'La extensión no está disponible para asignación.')
    {
        parent::__construct($message);
    }
}

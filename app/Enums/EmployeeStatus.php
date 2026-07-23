<?php

namespace App\Enums;

enum EmployeeStatus: string
{
    case Activo   = 'activo';
    case Inactivo = 'inactivo';
    case Baja     = 'baja';

    public function label(): string
    {
        return match ($this) {
            self::Activo   => 'Activo',
            self::Inactivo => 'Inactivo',
            self::Baja     => 'Dado de baja',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Activo   => 'green',
            self::Inactivo => 'yellow',
            self::Baja     => 'red',
        };
    }
}

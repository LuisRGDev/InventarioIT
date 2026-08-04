<?php

namespace App\Enums;

enum ExtensionStatus: string
{
    case Disponible = 'disponible';
    case Asignada   = 'asignada';
    case Baja       = 'baja';

    public function label(): string
    {
        return match($this) {
            self::Disponible => 'Disponible',
            self::Asignada   => 'Asignada',
            self::Baja       => 'Baja',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Disponible => 'bg-emerald-100 text-emerald-800',
            self::Asignada   => 'bg-amber-100 text-amber-800',
            self::Baja       => 'bg-red-100 text-red-800',
        };
    }
}

<?php

namespace App\Enums;

enum DeviceStatus: string
{
    case Disponible   = 'disponible';
    case Asignado     = 'asignado';
    case EnReparacion = 'en_reparacion';
    case Obsoleto     = 'obsoleto';
    case Baja         = 'baja';

    public function label(): string
    {
        return match ($this) {
            self::Disponible   => 'Disponible',
            self::Asignado     => 'Asignado',
            self::EnReparacion => 'En Reparación',
            self::Obsoleto     => 'Obsoleto',
            self::Baja         => 'Dado de baja',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Disponible   => 'green',
            self::Asignado     => 'blue',
            self::EnReparacion => 'yellow',
            self::Obsoleto     => 'gray',
            self::Baja         => 'red',
        };
    }
}

<?php

namespace App\Enums;

enum DeviceCondition: string
{
    case Nuevo      = 'nuevo';
    case BuenEstado = 'buen_estado';
    case Daniado    = 'daniado';
    case Obsoleto   = 'obsoleto';

    public function label(): string
    {
        return match ($this) {
            self::Nuevo      => 'Nuevo',
            self::BuenEstado => 'Buen estado',
            self::Daniado    => 'Dañado',
            self::Obsoleto   => 'Obsoleto',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Nuevo      => 'blue',
            self::BuenEstado => 'green',
            self::Daniado    => 'red',
            self::Obsoleto   => 'gray',
        };
    }
}

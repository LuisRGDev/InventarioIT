<?php

namespace App\Enums;

enum PhoneLineStatus: string
{
    case Disponible = 'disponible';
    case Asignada = 'asignada';
    case Baja = 'baja';

    public function label(): string
    {
        return match($this) {
            self::Disponible => 'Disponible',
            self::Asignada => 'Asignada',
            self::Baja => 'Baja',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::Disponible => 'bg-green-100 text-green-800 border-green-200',
            self::Asignada => 'bg-blue-100 text-blue-800 border-blue-200',
            self::Baja => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }
}

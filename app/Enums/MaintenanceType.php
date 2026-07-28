<?php

namespace App\Enums;

enum MaintenanceType: string
{
    case Preventivo = 'preventivo';
    case Correctivo = 'correctivo';
    case Upgrade    = 'upgrade';

    public function label(): string
    {
        return match ($this) {
            self::Preventivo => 'Mantenimiento Preventivo',
            self::Correctivo => 'Mantenimiento Correctivo (Reparación)',
            self::Upgrade    => 'Actualización / Upgrade',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Preventivo => 'blue',
            self::Correctivo => 'amber',
            self::Upgrade    => 'purple',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Preventivo => 'bg-sky-50 text-sky-700 border-sky-200',
            self::Correctivo => 'bg-amber-50 text-amber-800 border-amber-200',
            self::Upgrade    => 'bg-purple-50 text-purple-700 border-purple-200',
        };
    }
}

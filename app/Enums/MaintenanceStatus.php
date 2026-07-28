<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Programado = 'programado';
    case EnProceso  = 'en_proceso';
    case Completado = 'completado';
    case Cancelado  = 'cancelado';

    public function label(): string
    {
        return match ($this) {
            self::Programado => 'Programado',
            self::EnProceso  => 'En Proceso / Taller',
            self::Completado => 'Completado',
            self::Cancelado  => 'Cancelado',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Programado => 'indigo',
            self::EnProceso  => 'yellow',
            self::Completado => 'green',
            self::Cancelado  => 'gray',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Programado => 'bg-indigo-50 text-indigo-700 border-indigo-200',
            self::EnProceso  => 'bg-amber-100 text-amber-800 border-amber-300 animate-pulse',
            self::Completado => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            self::Cancelado  => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }
}

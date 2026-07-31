<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class GlobalEmployeesInventoryTemplateSheet implements WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'Numero de empleado',
            'Empleado',
            'Correo',
            'Departamento',
            'Puesto',
            'Usuario de dominio',
            'Categoria (Desktop/Portatil)',
            'Marca',
            'Modelo',
            'Procesador',
            'Sistema Operativo',
            'Almacenamiento',
            'RAM',
            'Nucleos',
            'Numero de Serie',
            'Mac Ethernet',
            'Mac Wifi',
            'Status',
            'Tag Service',
            'Fecha de compra',
            'garantia Expira',
            'Marca de Celular',
            'Modelo',
            'IMEI',
            'Sistema operativo',
            'Tipo de plan',
            'Costo de plan',
            'Notas',
        ];
    }

    public function title(): string
    {
        return 'Inventario Asignado Global';
    }
}

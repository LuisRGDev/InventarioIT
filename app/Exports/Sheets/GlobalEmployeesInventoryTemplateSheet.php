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
            'Direccion',
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
            'Identificador de BL',
            'Clave de BL',
            'Marca de Celular',
            'Modelo',
            'IMEI',
            'Sistema operativo',
            'Número de Teléfono',
            'Tipo de plan',
            'Costo de plan',
            'Direccion de Extension',
            'Numero de Extension',
            'Notas',
        ];
    }

    public function title(): string
    {
        return 'Inventario Asignado Global';
    }
}

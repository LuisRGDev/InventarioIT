<?php

namespace App\Imports;

use App\Models\JobPosition;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class JobPositionsImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        $maxRows = config('inventory.import_max_rows', 5000);
        if ($rows->count() > $maxRows) {
            throw new \Exception("El archivo contiene {$rows->count()} filas. El máximo permitido es {$maxRows}.");
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                // Remove spaces and make it consistent
                $direction = trim($row['direccion']);
                $area = trim($row['area']);
                $name = trim($row['puesto']);
                $notes = trim($row['notas'] ?? '');

                // Use updateOrCreate to avoid duplicates
                JobPosition::updateOrCreate(
                    [
                        'direction' => $direction,
                        'area' => $area,
                        'name' => $name,
                    ],
                    [
                        'notes' => ! empty($notes) ? $notes : null,
                    ]
                );
            }
        });
    }

    public function rules(): array
    {
        return [
            'direccion' => ['required', 'string', 'max:100'],
            'area' => ['required', 'string', 'max:100'],
            'puesto' => ['required', 'string', 'max:100'],
            'notas' => ['nullable', 'string'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'direccion.required' => 'La columna "direccion" es obligatoria en todas las filas.',
            'area.required' => 'La columna "area" es obligatoria en todas las filas.',
            'puesto.required' => 'La columna "puesto" es obligatoria en todas las filas.',
        ];
    }
}

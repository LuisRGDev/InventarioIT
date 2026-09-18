<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\OfficeExtension;
use App\Models\OfficeExtensionAssignment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class OfficeExtensionsImport implements SkipsEmptyRows, ToCollection, WithHeadingRow, WithValidation
{
    public function collection(Collection $rows)
    {
        $maxRows = config('inventory.import_max_rows', 5000);
        if ($rows->count() > $maxRows) {
            throw new \Exception("El archivo contiene {$rows->count()} filas. El máximo permitido es {$maxRows}.");
        }

        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $number = trim((string) $row['numero_de_extension']);
                $directNumber = trim((string) ($row['numero_directo'] ?? ''));
                $status = strtolower(trim((string) ($row['estatus'] ?? '')));
                $email = trim((string) ($row['correo_empleado'] ?? ''));
                $notes = trim((string) ($row['notas'] ?? ''));

                if (! in_array($status, ['disponible', 'asignada', 'baja'])) {
                    $status = 'disponible';
                }

                $extension = OfficeExtension::updateOrCreate(
                    [
                        'extension_number' => $number,
                    ],
                    [
                        'direct_number' => ! empty($directNumber) ? $directNumber : null,
                        'status' => $status,
                        'notes' => ! empty($notes) ? $notes : null,
                    ]
                );

                if (! empty($email)) {
                    $employee = Employee::where('email', $email)->first();
                    if ($employee) {
                        $currentExtAssignment = $extension->currentAssignment;
                        if (! $currentExtAssignment || $currentExtAssignment->employee_id !== $employee->id) {
                            if ($currentExtAssignment) {
                                $currentExtAssignment->update(['returned_at' => now()]);
                            }
                            OfficeExtensionAssignment::create([
                                'office_extension_id' => $extension->id,
                                'employee_id' => $employee->id,
                                'assigned_at' => now(),
                                'notes' => 'Asignado/Actualizado durante importación masiva de extensiones.',
                            ]);

                            // Asegurarse de que el estatus sea "asignada"
                            if ($extension->status->value !== 'asignada') {
                                $extension->update(['status' => 'asignada']);
                            }
                        }
                    }
                }
            }
        });
    }

    public function rules(): array
    {
        return [
            'numero_de_extension' => ['required', 'string', 'max:50'],
            'numero_directo' => ['nullable', 'string', 'max:50'],
            'estatus' => ['nullable', 'string', 'in:disponible,asignada,baja,DISPONIBLE,ASIGNADA,BAJA'],
            'correo_empleado' => ['nullable', 'email'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'numero_de_extension.required' => 'La columna "numero_de_extension" es obligatoria.',
            'estatus.in' => 'El estatus debe ser disponible, asignada o baja.',
            'correo_empleado.email' => 'El correo debe tener un formato válido.',
        ];
    }
}

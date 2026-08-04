<?php

namespace App\Services;

use App\Enums\PhoneLineStatus;
use App\Exceptions\PhoneLineNotAvailableException;
use App\Models\Employee;
use App\Models\PhoneLine;
use App\Models\PhoneLineAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhoneLineAssignmentService
{
    /**
     * Asignar una línea telefónica a un empleado.
     *
     * @param PhoneLine $phoneLine
     * @param Employee $employee
     * @param array $data Opcional (notas, etc.)
     * @return PhoneLineAssignment
     * @throws PhoneLineNotAvailableException
     */
    public function assign(PhoneLine $phoneLine, Employee $employee, array $data = []): PhoneLineAssignment
    {
        if ($phoneLine->status !== PhoneLineStatus::Disponible) {
            throw new PhoneLineNotAvailableException("La línea {$phoneLine->number} no está disponible (Estatus actual: {$phoneLine->status->label()}).");
        }

        return DB::transaction(function () use ($phoneLine, $employee, $data) {
            // Actualizar estado de la línea
            $phoneLine->update([
                'status' => PhoneLineStatus::EnUso
            ]);

            // Crear asignación
            return PhoneLineAssignment::create([
                'phone_line_id' => $phoneLine->id,
                'employee_id' => $employee->id,
                'assigned_by_user_id' => Auth::id(),
                'assigned_at' => now(),
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }

    /**
     * Retornar una línea telefónica que tiene un empleado.
     *
     * @param PhoneLineAssignment $assignment
     * @param array $data Opcional (notas, etc.)
     * @return PhoneLineAssignment
     */
    public function returnLine(PhoneLineAssignment $assignment, array $data = []): PhoneLineAssignment
    {
        return DB::transaction(function () use ($assignment, $data) {
            $assignment->update([
                'returned_at' => now(),
                'returned_by_user_id' => Auth::id(),
                'notes' => isset($data['notes']) 
                    ? $assignment->notes . "\n[Retorno]: " . $data['notes'] 
                    : $assignment->notes,
            ]);

            $assignment->phoneLine->update([
                'status' => PhoneLineStatus::Disponible
            ]);

            return $assignment;
        });
    }
}

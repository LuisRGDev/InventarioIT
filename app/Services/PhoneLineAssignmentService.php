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
     * @param  array  $data  Opcional (notas, etc.)
     *
     * @throws PhoneLineNotAvailableException
     */
    public function assign(PhoneLine $phoneLine, Employee $employee, array $data = []): PhoneLineAssignment
    {
        return DB::transaction(function () use ($phoneLine, $employee, $data) {
            $phoneLine = PhoneLine::whereKey($phoneLine->id)->lockForUpdate()->firstOrFail();

            if ($phoneLine->status !== PhoneLineStatus::Disponible) {
                throw new PhoneLineNotAvailableException("La línea {$phoneLine->number} no está disponible (Estatus actual: {$phoneLine->status->label()}).");
            }

            // Invariante: un empleado solo tiene una línea telefónica activa
            // a la vez (igual que ExtensionAssignmentService::assign()). Sin
            // esto, un empleado podía terminar con varias líneas activas
            // simultáneas si se le asignaba una nueva sin devolver la
            // anterior desde cualquier punto de entrada distinto de
            // EmployeeController::update().
            $currentAssignment = $employee->currentPhoneLineAssignments()->first();
            if ($currentAssignment) {
                $this->returnLine($currentAssignment, ['notes' => 'Devolución automática por reasignación.']);
            }

            $phoneLine->update([
                'status' => PhoneLineStatus::Asignada,
            ]);

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
     * @param  array  $data  Opcional (notas, etc.)
     */
    public function returnLine(PhoneLineAssignment $assignment, array $data = []): PhoneLineAssignment
    {
        return DB::transaction(function () use ($assignment, $data) {
            $phoneLine = PhoneLine::whereKey($assignment->phone_line_id)->lockForUpdate()->firstOrFail();

            $assignment->update([
                'returned_at' => now(),
                'returned_by_user_id' => Auth::id(),
                'notes' => isset($data['notes'])
                    ? $assignment->notes."\n[Retorno]: ".$data['notes']
                    : $assignment->notes,
            ]);

            $phoneLine->update([
                'status' => PhoneLineStatus::Disponible,
            ]);

            return $assignment;
        });
    }
}

<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\OfficeExtension;
use App\Models\OfficeExtensionAssignment;
use App\Enums\ExtensionStatus;
use App\Exceptions\ExtensionNotAvailableException;
use App\Exceptions\ExtensionAlreadyAssignedException;
use Illuminate\Support\Facades\DB;

class ExtensionAssignmentService
{
    /**
     * @throws ExtensionNotAvailableException
     */
    public function assign(OfficeExtension $extension, Employee $employee, array $options = []): OfficeExtensionAssignment
    {
        return DB::transaction(function () use ($extension, $employee, $options) {
            if ($extension->status !== ExtensionStatus::Disponible) {
                throw new ExtensionNotAvailableException("La extensión {$extension->extension_number} no está disponible.");
            }

            // Return current extension if any
            $currentAssignment = $employee->currentOfficeExtensionAssignments()->first();
            if ($currentAssignment) {
                $this->returnExtension($currentAssignment, ['notes' => 'Devolución automática por reasignación.']);
            }

            $assignment = OfficeExtensionAssignment::create([
                'office_extension_id' => $extension->id,
                'employee_id'         => $employee->id,
                'assigned_at'         => now(),
                'notes'               => $options['notes'] ?? null,
            ]);

            $extension->update(['status' => ExtensionStatus::Asignada->value]);

            return $assignment;
        });
    }

    public function returnExtension(OfficeExtensionAssignment $assignment, array $options = []): OfficeExtensionAssignment
    {
        return DB::transaction(function () use ($assignment, $options) {
            $assignment->update([
                'returned_at' => now(),
                'notes'       => isset($options['notes'])
                    ? ($assignment->notes ? $assignment->notes . "\n" . $options['notes'] : $options['notes'])
                    : $assignment->notes,
            ]);

            $assignment->officeExtension->update(['status' => ExtensionStatus::Disponible->value]);

            return $assignment;
        });
    }
}

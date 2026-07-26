<?php

namespace App\Services;

use App\Enums\DeviceCondition;
use App\Enums\DeviceStatus;
use App\Enums\EmployeeStatus;
use App\Exceptions\DeviceAlreadyAssignedException;
use App\Exceptions\DeviceNotAvailableException;
use App\Exceptions\NoActiveAssignmentException;
use App\Mail\InventoryNotificationMail;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\Employee;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DeviceAssignmentService
{
    /**
     * Asigna un equipo a un empleado.
     *
     * Validaciones de negocio:
     * - El equipo debe estar en estado 'disponible'.
     * - El empleado debe estar 'activo'.
     * - No debe existir ya una asignación activa para ese equipo.
     *
     * @throws DeviceNotAvailableException
     * @throws DeviceAlreadyAssignedException
     */
    public function assign(Device $device, Employee $employee, array $data = []): DeviceAssignment
    {
        return DB::transaction(function () use ($device, $employee, $data) {
            // Recargar con lock para evitar condiciones de carrera
            $device->refresh()->lockForUpdate();

            if ($device->status !== DeviceStatus::Disponible) {
                throw new DeviceNotAvailableException(
                    "El equipo [{$device->serial_number}] no está disponible (estado: {$device->status->label()})."
                );
            }

            if ($device->currentAssignment()->exists()) {
                throw new DeviceAlreadyAssignedException(
                    "El equipo [{$device->serial_number}] ya tiene una asignación activa."
                );
            }

            if ($employee->status !== EmployeeStatus::Activo) {
                throw new \RuntimeException(
                    "El empleado [{$employee->name}] no está activo."
                );
            }

            // Crear la asignación
            $assignment = DeviceAssignment::create([
                'device_id'            => $device->id,
                'employee_id'          => $employee->id,
                'assigned_by_user_id'  => Auth::id(),
                'assigned_at'          => now(),
                'condition_on_delivery' => $data['condition_on_delivery'] ?? DeviceCondition::BuenEstado->value,
                'notes'                => $data['notes'] ?? null,
            ]);

            // Actualizar estado del equipo
            $device->update(['status' => DeviceStatus::Asignado]);

            $this->sendNotification(
                'Nuevo Equipo Asignado',
                "Se ha asignado el equipo {$device->brand} {$device->model} al empleado {$employee->name}.",
                [
                    'Empleado' => $employee->name,
                    'Departamento' => $employee->department,
                    'Equipo' => "{$device->brand} {$device->model} (SN: {$device->serial_number})",
                    'Condición de Entrega' => $assignment->condition_on_delivery instanceof \App\Enums\DeviceCondition ? $assignment->condition_on_delivery->label() : ($assignment->condition_on_delivery ?? 'N/A')
                ]
            );

            return $assignment->load(['device', 'employee', 'assignedBy']);
        });
    }

    /**
     * Registra la devolución de un equipo.
     *
     * Validaciones de negocio:
     * - Debe existir una asignación activa para el equipo.
     * - Si la condición de devolución es 'daniado', el equipo pasa a 'en_reparacion'.
     * - De lo contrario, el nuevo estado es el indicado en $data['new_status'] (default: disponible).
     *
     * @throws NoActiveAssignmentException
     */
    public function returnDevice(Device $device, array $data = []): DeviceAssignment
    {
        return DB::transaction(function () use ($device, $data) {
            $device->refresh()->lockForUpdate();

            $assignment = $device->currentAssignment;

            if (! $assignment) {
                throw new NoActiveAssignmentException(
                    "El equipo [{$device->serial_number}] no tiene una asignación activa."
                );
            }

            $conditionOnReturn = isset($data['condition_on_return'])
                ? DeviceCondition::from($data['condition_on_return'])
                : DeviceCondition::BuenEstado;

            // Regla de negocio: equipo dañado → en reparación automáticamente
            $newStatus = $conditionOnReturn === DeviceCondition::Daniado
                ? DeviceStatus::EnReparacion
                : DeviceStatus::from($data['new_status'] ?? DeviceStatus::Disponible->value);

            // Cerrar la asignación
            $assignment->update([
                'returned_at'          => now(),
                'returned_by_user_id'  => Auth::id(),
                'condition_on_return'  => $conditionOnReturn->value,
                'notes'                => $data['notes'] ?? $assignment->notes,
            ]);

            // Actualizar estado del equipo
            $device->update(['status' => $newStatus]);

            return $assignment->load(['device', 'employee', 'returnedBy']);
        });
    }

    /**
     * Reemplaza el equipo actualmente asignado a un empleado por uno nuevo.
     *
     * Ejecuta returnDevice($oldDevice) + assign($newDevice, $employee)
     * en una sola transacción atómica.
     *
     * Validaciones de negocio:
     * - El equipo viejo debe estar actualmente asignado a ese mismo empleado.
     * - El equipo nuevo debe estar en estado 'disponible'.
     *
     * @throws NoActiveAssignmentException
     * @throws DeviceNotAvailableException
     */
    public function replace(
        Device $oldDevice,
        Device $newDevice,
        Employee $employee,
        array $data = []
    ): array {
        return DB::transaction(function () use ($oldDevice, $newDevice, $employee, $data) {
            // Validar que el equipo viejo esté asignado a este empleado
            $currentAssignment = $oldDevice->currentAssignment;

            if (! $currentAssignment || $currentAssignment->employee_id !== $employee->id) {
                throw new NoActiveAssignmentException(
                    "El equipo [{$oldDevice->serial_number}] no está actualmente asignado a [{$employee->name}]."
                );
            }

            // Devolver el equipo viejo
            $returnData = [
                'condition_on_return' => $data['condition_on_return'] ?? DeviceCondition::BuenEstado->value,
                'new_status'          => $data['old_device_new_status'] ?? DeviceStatus::Disponible->value,
                'notes'               => $data['return_notes'] ?? null,
            ];
            $returnedAssignment = $this->returnDevice($oldDevice, $returnData);

            // Asignar el equipo nuevo
            $assignData = [
                'condition_on_delivery' => $data['condition_on_delivery'] ?? DeviceCondition::BuenEstado->value,
                'notes'                 => $data['assign_notes'] ?? null,
            ];
            $newAssignment = $this->assign($newDevice, $employee, $assignData);

            $this->sendNotification(
                'Reemplazo de Equipo',
                "El empleado {$employee->name} ha recibido un reemplazo de equipo.",
                [
                    'Empleado' => $employee->name,
                    'Equipo Anterior' => "{$oldDevice->brand} {$oldDevice->model} (SN: {$oldDevice->serial_number})",
                    'Nuevo Equipo' => "{$newDevice->brand} {$newDevice->model} (SN: {$newDevice->serial_number})",
                    'Motivo' => $data['reason'] ?? 'N/A'
                ]
            );

            return [
                'returned' => $returnedAssignment,
                'assigned' => $newAssignment,
            ];
        });
    }

    /**
     * Envía notificaciones por correo electrónico.
     */
    protected function sendNotification(string $subject, string $message, array $details): void
    {
        try {
            $emailsStr = env('MAIL_TO_MANAGEMENT');
            if ($emailsStr) {
                $emails = array_map('trim', explode(',', $emailsStr));
                Mail::to($emails)->queue(new InventoryNotificationMail($subject, $message, $details));
            }
        } catch (\Exception $e) {
            // Log exception or handle silently
        }
    }
}

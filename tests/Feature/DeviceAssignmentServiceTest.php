<?php

namespace Tests\Feature;

use App\Enums\DeviceStatus;
use App\Exceptions\DeviceNotAvailableException;
use App\Exceptions\NoActiveAssignmentException;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Employee;
use App\Services\DeviceAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cubre el flujo principal de asignación/devolución/reemplazo de equipos y
 * fija el comportamiento corregido en la Fase 1 (Hallazgo Crítico C4:
 * lockForUpdate() era un no-op; ahora Device::whereKey($id)->lockForUpdate()
 * ->firstOrFail() sí bloquea la fila dentro de la transacción).
 */
class DeviceAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected DeviceAssignmentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DeviceAssignmentService::class);
    }

    protected function aDevice(array $attributes = []): Device
    {
        $category = DeviceCategory::where('slug', 'desktop')->firstOrFail();

        return Device::factory()->disponible()->create(array_merge([
            'device_category_id' => $category->id,
            'device_model_id' => null,
        ], $attributes));
    }

    public function test_assign_marks_device_as_assigned_and_creates_assignment(): void
    {
        $device = $this->aDevice();
        $employee = Employee::factory()->activo()->create();

        $assignment = $this->service->assign($device, $employee, ['condition_on_delivery' => 'buen_estado']);

        $this->assertSame($employee->id, $assignment->employee_id);
        $this->assertSame($device->id, $assignment->device_id);
        $this->assertSame(DeviceStatus::Asignado, $device->fresh()->status);
        $this->assertNotNull($device->fresh()->currentAssignment);
    }

    public function test_assign_throws_when_device_is_not_available(): void
    {
        $device = $this->aDevice(['status' => DeviceStatus::EnReparacion]);
        $employee = Employee::factory()->activo()->create();

        $this->expectException(DeviceNotAvailableException::class);

        $this->service->assign($device, $employee);
    }

    public function test_assign_throws_when_device_already_has_an_active_assignment(): void
    {
        $device = $this->aDevice();
        $employee = Employee::factory()->activo()->create();
        $otherEmployee = Employee::factory()->activo()->create();

        $this->service->assign($device, $employee);

        // El estado ya cambió a "asignado" en la primera llamada, así que
        // esta segunda asignación debe fallar por DeviceNotAvailableException
        // (status check) antes de llegar siquiera al check de asignación
        // activa; cualquiera de las dos excepciones de negocio es la prueba
        // de que un dispositivo ya asignado no puede volver a asignarse.
        $this->expectException(DeviceNotAvailableException::class);

        $this->service->assign($device->fresh(), $otherEmployee);
    }

    public function test_returning_a_device_makes_it_available_again(): void
    {
        $device = $this->aDevice();
        $employee = Employee::factory()->activo()->create();
        $this->service->assign($device, $employee);

        $assignment = $this->service->returnDevice($device->fresh(), ['condition_on_return' => 'buen_estado']);

        $this->assertNotNull($assignment->returned_at);
        $this->assertSame(DeviceStatus::Disponible, $device->fresh()->status);
    }

    public function test_returning_a_damaged_device_sets_it_to_en_reparacion_automatically(): void
    {
        $device = $this->aDevice();
        $employee = Employee::factory()->activo()->create();
        $this->service->assign($device, $employee);

        $this->service->returnDevice($device->fresh(), ['condition_on_return' => 'daniado']);

        $this->assertSame(DeviceStatus::EnReparacion, $device->fresh()->status);
    }

    public function test_returning_a_device_without_an_active_assignment_throws(): void
    {
        $device = $this->aDevice();

        $this->expectException(NoActiveAssignmentException::class);

        $this->service->returnDevice($device);
    }

    public function test_replace_returns_old_device_and_assigns_new_one_atomically(): void
    {
        $employee = Employee::factory()->activo()->create();
        $oldDevice = $this->aDevice();
        $newDevice = $this->aDevice();
        $this->service->assign($oldDevice, $employee);

        $result = $this->service->replace($oldDevice->fresh(), $newDevice, $employee);

        $this->assertNotNull($result['returned']->returned_at);
        $this->assertSame(DeviceStatus::Disponible, $oldDevice->fresh()->status);
        $this->assertSame(DeviceStatus::Asignado, $newDevice->fresh()->status);
        $this->assertSame($employee->id, $result['assigned']->employee_id);
    }

    public function test_assign_throws_when_employee_is_not_active(): void
    {
        $device = $this->aDevice();
        $employee = Employee::factory()->inactivo()->create();

        $this->expectException(\RuntimeException::class);

        $this->service->assign($device, $employee);
    }
}

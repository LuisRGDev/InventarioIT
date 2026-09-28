<?php

namespace Tests\Feature;

use App\Enums\PhoneLineStatus;
use App\Exceptions\PhoneLineNotAvailableException;
use App\Models\Employee;
use App\Models\PhoneLine;
use App\Services\PhoneLineAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Cubre PhoneLineAssignmentService, incluyendo la regresión del Hallazgo
 * Alto H5: assign() no devolvía automáticamente la línea activa previa del
 * empleado (a diferencia de ExtensionAssignmentService), permitiendo que un
 * empleado terminara con varias líneas activas simultáneas.
 */
class PhoneLineAssignmentServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PhoneLineAssignmentService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(PhoneLineAssignmentService::class);
    }

    public function test_assign_marks_line_as_assigned(): void
    {
        $line = PhoneLine::factory()->disponible()->create();
        $employee = Employee::factory()->activo()->create();

        $assignment = $this->service->assign($line, $employee);

        $this->assertSame($employee->id, $assignment->employee_id);
        $this->assertSame(PhoneLineStatus::Asignada, $line->fresh()->status);
    }

    public function test_assign_throws_when_line_is_not_available(): void
    {
        $line = PhoneLine::factory()->create(['status' => PhoneLineStatus::Baja]);
        $employee = Employee::factory()->activo()->create();

        $this->expectException(PhoneLineNotAvailableException::class);

        $this->service->assign($line, $employee);
    }

    public function test_assigning_a_new_line_automatically_returns_the_employees_previous_active_line(): void
    {
        $oldLine = PhoneLine::factory()->disponible()->create();
        $newLine = PhoneLine::factory()->disponible()->create();
        $employee = Employee::factory()->activo()->create();

        $this->service->assign($oldLine, $employee);
        $this->service->assign($newLine, $employee);

        $this->assertSame(PhoneLineStatus::Disponible, $oldLine->fresh()->status);
        $this->assertSame(PhoneLineStatus::Asignada, $newLine->fresh()->status);

        // El empleado debe tener exactamente una línea activa, nunca dos.
        $this->assertSame(1, $employee->currentPhoneLineAssignments()->count());
        $this->assertSame($newLine->id, $employee->currentPhoneLineAssignments()->first()->phone_line_id);
    }

    public function test_return_line_makes_it_available_again(): void
    {
        $line = PhoneLine::factory()->disponible()->create();
        $employee = Employee::factory()->activo()->create();
        $assignment = $this->service->assign($line, $employee);

        $this->service->returnLine($assignment);

        $this->assertSame(PhoneLineStatus::Disponible, $line->fresh()->status);
        $this->assertNotNull($assignment->fresh()->returned_at);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\PhoneLineStatus;
use App\Models\Employee;
use App\Models\OfficeExtension;
use App\Models\PhoneLine;
use App\Models\User;
use App\Services\ExtensionAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PhoneLineAndExtensionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin TI']);
    }

    protected function adminUser(): User
    {
        $user = User::factory()->create();
        $user->assignRole('Admin TI');

        return $user;
    }

    /**
     * Regresión del Hallazgo Alto H4: las condiciones de búsqueda no
     * estaban agrupadas en un closure, así que el filtro de estatus solo
     * aplicaba (con AND) sobre la última condición OR (el whereHas de
     * empleado), dejando pasar coincidencias de "number"/"data_plan" de
     * cualquier estatus.
     */
    public function test_status_filter_applies_to_the_whole_search_not_just_the_last_or_clause(): void
    {
        $admin = $this->adminUser();

        $matchesSearchWrongStatus = PhoneLine::factory()->create([
            'number' => '555-000-1111',
            'status' => PhoneLineStatus::Asignada,
        ]);
        $matchesSearchRightStatus = PhoneLine::factory()->create([
            'number' => '555-000-2222',
            'status' => PhoneLineStatus::Disponible,
        ]);

        $response = $this->actingAs($admin)->get('/phone-lines?search=555-000&status=disponible');

        $response->assertOk();
        $phoneLines = $response->viewData('phoneLines');
        $ids = $phoneLines->pluck('id');

        $this->assertTrue($ids->contains($matchesSearchRightStatus->id));
        $this->assertFalse(
            $ids->contains($matchesSearchWrongStatus->id),
            'El filtro de estatus no debe ser evadido por una coincidencia de búsqueda.'
        );
    }

    /**
     * Regresión del Hallazgo Alto H6: OfficeExtensionController::destroy no
     * tenía el guard de asignación activa que sí tienen Device/PhoneLine,
     * así que eliminar una extensión asignada lanzaba una QueryException
     * sin capturar (la FK es restrictOnDelete desde la migración
     * 2026_09_17_000001).
     */
    public function test_cannot_delete_office_extension_with_active_assignment(): void
    {
        $admin = $this->adminUser();
        $extension = OfficeExtension::factory()->disponible()->create();
        $employee = Employee::factory()->activo()->create();
        app(ExtensionAssignmentService::class)->assign($extension, $employee);

        $response = $this->actingAs($admin)->delete(route('office-extensions.destroy', $extension));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertNotNull($extension->fresh());
        $this->assertNotSoftDeleted($extension);
    }
}

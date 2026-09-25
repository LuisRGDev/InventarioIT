<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Smoke test de las vistas tocadas en la Fase 5 (UI/Responsive) de la
 * auditoría: confirma que compilan y renderizan sin errores tras
 * unificar los modales de importación y ajustar el layout responsive.
 * No verifica apariencia visual (eso requiere un navegador), solo que
 * el Blade es válido y la página responde 200.
 */
class ViewsRenderSmokeTest extends TestCase
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

    public function test_dashboard_renders(): void
    {
        $this->actingAs($this->adminUser())->get('/dashboard')->assertOk();
    }

    public function test_office_extensions_index_renders(): void
    {
        $this->actingAs($this->adminUser())->get('/office-extensions')->assertOk();
    }

    public function test_phone_lines_index_renders(): void
    {
        $this->actingAs($this->adminUser())->get('/phone-lines')->assertOk();
    }

    /**
     * Cubre el header responsive estandarizado en las páginas de listado y
     * creación (sin dependencias de modelo) tras el fix del Hallazgo Alto
     * "header no responsive en ~15 vistas".
     */
    public function test_list_and_create_pages_with_standardized_header_render(): void
    {
        $admin = $this->actingAs($this->adminUser());

        $admin->get('/devices')->assertOk();
        $admin->get('/employees')->assertOk();
        $admin->get('/assignments')->assertOk();
        $admin->get('/phone-lines/create')->assertOk();
        $admin->get('/device-models/create')->assertOk();
        $admin->get('/maintenances/create')->assertOk();
        $admin->get('/maintenances')->assertOk();
        $admin->get('/job-positions')->assertOk();
        $admin->get('/device-models')->assertOk();
        // Rutas con parámetro opcional: cubren replace-device-page.blade.php
        // y return-device-page.blade.php tras el fix de labels sin for/id.
        $admin->get(route('assignments.replace'))->assertOk();
        $admin->get(route('assignments.return'))->assertOk();
    }

    public function test_device_show_page_with_standardized_header_renders(): void
    {
        $category = DeviceCategory::where('slug', 'desktop')->firstOrFail();
        $device = Device::factory()->disponible()->create([
            'device_category_id' => $category->id,
            'device_model_id' => null,
        ]);

        $this->actingAs($this->adminUser())
            ->get(route('devices.show', $device))
            ->assertOk();
    }

    public function test_employee_show_page_with_standardized_header_renders(): void
    {
        $employee = Employee::factory()->activo()->create();

        $this->actingAs($this->adminUser())
            ->get(route('employees.show', $employee))
            ->assertOk();
    }
}

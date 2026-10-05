<?php

namespace Tests\Feature;

use App\Livewire\AssignDevicePage;
use App\Livewire\AssignExtensionPage;
use App\Livewire\AssignPhoneLinePage;
use App\Livewire\ReplaceDevicePage;
use App\Livewire\ReturnDevicePage;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\Employee;
use App\Models\OfficeExtension;
use App\Models\PhoneLine;
use App\Models\User;
use App\Services\DeviceAssignmentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Regresiones de los bugs funcionales del flujo de asignaciones: ids
 * preseleccionados que la pantalla ignoraba, búsquedas de empleados cuyo
 * orWhere escapaba de los filtros (active / has equipos), pantalla de
 * devolución sin selector, y rutas que devolvían 500.
 */
class AssignmentFlowsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin TI']);
        $admin = User::factory()->create();
        $admin->assignRole('Admin TI');
        $this->actingAs($admin);
    }

    private function device(array $attributes = []): Device
    {
        return Device::factory()->disponible()->create(array_merge([
            'device_category_id' => DeviceCategory::where('slug', 'desktop')->firstOrFail()->id,
            'device_model_id' => null,
        ], $attributes));
    }

    private function assigned(Device $device, Employee $employee): void
    {
        app(DeviceAssignmentService::class)->assign($device, $employee, ['condition_on_delivery' => 'buen_estado']);
    }

    // ── Preselección desde la URL ────────────────────────────────────────

    public function test_assign_page_preselects_the_device_from_the_query_string(): void
    {
        $device = $this->device();

        Livewire::withQueryParams(['selectedDeviceId' => $device->id])
            ->test(AssignDevicePage::class)
            ->assertSet('selectedDeviceId', $device->id);
    }

    public function test_device_detail_assign_link_carries_the_device(): void
    {
        $device = $this->device();

        $this->get(route('devices.show', $device))
            ->assertOk()
            ->assertSee('selectedDeviceId='.$device->id, false);
    }

    public function test_assign_extension_page_preselects_the_extension_from_the_query_string(): void
    {
        $extension = OfficeExtension::factory()->create(['status' => 'disponible']);

        Livewire::withQueryParams(['selectedExtensionId' => $extension->id])
            ->test(AssignExtensionPage::class)
            ->assertSet('selectedExtensionId', $extension->id);
    }

    public function test_preselected_device_that_is_no_longer_available_is_discarded(): void
    {
        $device = $this->device();
        $this->assigned($device, Employee::factory()->activo()->create());

        Livewire::withQueryParams(['selectedDeviceId' => $device->id])
            ->test(AssignDevicePage::class)
            ->assertSet('selectedDeviceId', null)
            ->assertSet('errorMessage', 'El equipo seleccionado ya no está disponible para asignar.');
    }

    public function test_preselected_inactive_or_missing_employee_is_discarded(): void
    {
        $inactive = Employee::factory()->inactivo()->create();

        Livewire::withQueryParams(['selectedEmployeeId' => $inactive->id])
            ->test(AssignDevicePage::class)
            ->assertSet('selectedEmployeeId', null);

        Livewire::withQueryParams(['selectedEmployeeId' => 999999])
            ->test(AssignPhoneLinePage::class)
            ->assertSet('selectedEmployeeId', null);
    }

    public function test_preselected_phone_line_that_is_not_available_is_discarded(): void
    {
        $line = PhoneLine::factory()->create(['status' => 'asignada']);

        Livewire::withQueryParams(['selectedPhoneId' => $line->id])
            ->test(AssignPhoneLinePage::class)
            ->assertSet('selectedPhoneId', null);
    }

    // ── Búsqueda de empleados: el OR no debe escapar de los filtros ──────

    public function test_assign_pages_do_not_list_inactive_employees_matching_by_email(): void
    {
        Employee::factory()->inactivo()->create(['name' => 'Pedro Baja', 'email' => 'ana.pedro@x.com', 'employee_code' => 'ZZ2']);
        Employee::factory()->activo()->create(['name' => 'Ana Activa', 'email' => 'a1@x.com', 'employee_code' => 'ZZ1']);

        foreach ([AssignDevicePage::class, AssignPhoneLinePage::class, AssignExtensionPage::class] as $page) {
            $names = Livewire::test($page)->set('employeeSearch', 'ana')->instance()->employees()->pluck('name')->all();

            $this->assertSame(['Ana Activa'], $names, $page);
        }
    }

    public function test_replace_page_only_lists_employees_that_have_devices(): void
    {
        Employee::factory()->activo()->create(['name' => 'Luis SinEquipo', 'email' => 'ana.luis@x.com']);
        $withDevice = Employee::factory()->activo()->create(['name' => 'Rosa ConEquipo', 'email' => 'ana.rosa@x.com']);
        $this->assigned($this->device(), $withDevice);

        $names = Livewire::test(ReplaceDevicePage::class)->set('employeeSearch', 'ana')->instance()->employees()->pluck('name')->all();

        $this->assertSame(['Rosa ConEquipo'], $names);
    }

    // ── Devolución sin equipo: lista con buscador ────────────────────────

    public function test_return_page_without_a_device_lists_assigned_devices_and_filters_them(): void
    {
        $maria = Employee::factory()->activo()->create(['name' => 'María Pérez']);
        $juan = Employee::factory()->activo()->create(['name' => 'Juan López']);
        $dMaria = $this->device(['brand' => 'Dell', 'model' => 'Latitude']);
        $dJuan = $this->device(['brand' => 'Lenovo', 'model' => 'ThinkPad']);
        $free = $this->device(['brand' => 'HP', 'model' => 'EliteBook']);
        $this->assigned($dMaria, $maria);
        $this->assigned($dJuan, $juan);

        $this->get(route('assignments.return'))
            ->assertOk()
            ->assertSee('Selecciona el equipo a devolver')
            ->assertDontSee('No se pudo localizar el equipo');

        $component = Livewire::test(ReturnDevicePage::class);
        $ids = $component->instance()->assignedDevices()->pluck('id')->all();
        $this->assertEqualsCanonicalizing([$dMaria->id, $dJuan->id], $ids);
        $this->assertNotContains($free->id, $ids);

        $filtered = $component->set('deviceSearch', 'lópez')->instance()->assignedDevices()->pluck('id')->all();
        $this->assertSame([$dJuan->id], $filtered);
    }

    public function test_return_page_picker_selects_a_device_and_can_go_back(): void
    {
        $employee = Employee::factory()->activo()->create(['name' => 'María Pérez']);
        $device = $this->device();
        $this->assigned($device, $employee);

        Livewire::test(ReturnDevicePage::class)
            ->call('selectDevice', $device->id)
            ->assertSet('deviceId', $device->id)
            ->assertSee('María Pérez')
            ->call('clearDevice')
            ->assertSet('deviceId', null)
            ->assertSee('Selecciona el equipo a devolver');
    }

    public function test_return_page_with_an_unknown_device_offers_to_pick_another(): void
    {
        Livewire::test(ReturnDevicePage::class, ['device' => 999999])
            ->assertSee('Equipo no encontrado')
            ->assertSee('Elegir otro equipo');
    }

    // ── Vistas ───────────────────────────────────────────────────────────

    public function test_employee_without_devices_does_not_show_the_replace_action(): void
    {
        $employee = Employee::factory()->activo()->create();

        $this->get(route('employees.show', $employee))
            ->assertOk()
            ->assertDontSee(route('assignments.replace', $employee->id), false);
    }

    public function test_employee_with_devices_shows_the_replace_action(): void
    {
        $employee = Employee::factory()->activo()->create();
        $this->assigned($this->device(), $employee);

        $this->get(route('employees.show', $employee))
            ->assertSee(route('assignments.replace', $employee->id), false);
    }

    public function test_list_pages_offer_a_quick_assign_for_available_items(): void
    {
        $device = $this->device();
        $line = PhoneLine::factory()->create(['status' => 'disponible']);
        $extension = OfficeExtension::factory()->create(['status' => 'disponible']);

        $this->get(route('devices.index'))->assertSee('selectedDeviceId='.$device->id, false);
        $this->get(route('phone-lines.index'))->assertSee('selectedPhoneId='.$line->id, false);
        $this->get(route('office-extensions.index'))->assertSee('selectedExtensionId='.$extension->id, false);
    }

    // ── Rutas que devolvían 500 ──────────────────────────────────────────

    public function test_device_categories_index_renders(): void
    {
        $this->get(route('device-categories.index'))->assertOk();
    }

    public function test_routes_without_a_detail_page_no_longer_exist(): void
    {
        $extension = OfficeExtension::factory()->create();

        // 405 (la URI sigue existiendo para PUT/DELETE) o 404: lo que importa
        // es que el GET ya no es un 500 por método/vista inexistente.
        foreach (['/office-extensions/'.$extension->id, '/device-models/1', '/job-positions/1'] as $uri) {
            $this->assertContains($this->get($uri)->getStatusCode(), [404, 405], $uri);
        }
    }

    // ── Login: recargar la página no debe dar 429 ────────────────────────

    public function test_login_page_is_not_rate_limited_on_repeated_page_loads(): void
    {
        auth()->logout();

        for ($i = 0; $i < 12; $i++) {
            $this->get('/login')->assertOk();
        }
    }
}

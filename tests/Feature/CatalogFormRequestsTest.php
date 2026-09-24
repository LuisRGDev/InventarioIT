<?php

namespace Tests\Feature;

use App\Models\DeviceCategory;
use App\Models\DeviceModel;
use App\Models\JobPosition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Cubre el Hallazgo Medio M2: DeviceCategoryController, DeviceModelController
 * y JobPositionController usaban $request->validate() inline en vez de
 * FormRequests dedicados, con las reglas duplicadas entre store()/update().
 * Se extrajeron a FormRequests; estos tests confirman que la validación y
 * las respuestas de éxito/error se preservaron.
 */
class CatalogFormRequestsTest extends TestCase
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

    public function test_device_category_store_validates_and_creates(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->post('/device-categories', [])
            ->assertSessionHasErrors('name');

        $response = $this->actingAs($admin)->post('/device-categories', [
            'name' => 'Servidor de Prueba',
        ]);

        $response->assertRedirect(route('device-categories.index'));
        $this->assertDatabaseHas('device_categories', ['name' => 'Servidor de Prueba']);
    }

    public function test_device_category_update_ignores_its_own_name_when_checking_uniqueness(): void
    {
        $admin = $this->adminUser();
        $category = DeviceCategory::where('slug', 'desktop')->firstOrFail();

        // Reenviar el mismo nombre en un update no debe disparar el error de unicidad.
        $response = $this->actingAs($admin)->put(route('device-categories.update', $category), [
            'name' => $category->name,
        ]);

        $response->assertRedirect(route('device-categories.index'));
        $response->assertSessionDoesntHaveErrors('name');
    }

    public function test_device_model_store_validates_required_fields(): void
    {
        $admin = $this->adminUser();
        $category = DeviceCategory::where('slug', 'desktop')->firstOrFail();

        $this->actingAs($admin)->post('/device-models', [])
            ->assertSessionHasErrors(['device_category_id', 'brand', 'model']);

        $response = $this->actingAs($admin)->post('/device-models', [
            'device_category_id' => $category->id,
            'brand' => 'Dell',
            'model' => 'OptiPlex 7020',
        ]);

        $response->assertRedirect(route('device-models.index'));
        $this->assertDatabaseHas('device_models', ['brand' => 'Dell', 'model' => 'OptiPlex 7020']);
    }

    public function test_device_model_update_persists_changes(): void
    {
        $admin = $this->adminUser();
        $category = DeviceCategory::where('slug', 'desktop')->firstOrFail();
        $model = DeviceModel::create([
            'device_category_id' => $category->id,
            'brand' => 'HP',
            'model' => 'ProDesk 400',
        ]);

        $response = $this->actingAs($admin)->put(route('device-models.update', $model), [
            'device_category_id' => $category->id,
            'brand' => 'HP',
            'model' => 'ProDesk 600',
        ]);

        $response->assertRedirect(route('device-models.index'));
        $this->assertSame('ProDesk 600', $model->fresh()->model);
    }

    public function test_job_position_store_validates_required_fields(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->post('/job-positions', [])
            ->assertSessionHasErrors(['direction', 'area', 'name']);

        $response = $this->actingAs($admin)->post('/job-positions', [
            'direction' => 'Finanzas',
            'area' => 'Contabilidad',
            'name' => 'Analista Contable',
        ]);

        $response->assertRedirect(route('job-positions.index'));
        $this->assertDatabaseHas('job_positions', ['name' => 'Analista Contable']);
    }

    public function test_job_position_update_persists_changes(): void
    {
        $admin = $this->adminUser();
        $position = JobPosition::create([
            'direction' => 'Finanzas',
            'area' => 'Contabilidad',
            'name' => 'Analista Contable',
        ]);

        $response = $this->actingAs($admin)->put(route('job-positions.update', $position), [
            'direction' => 'Finanzas',
            'area' => 'Contabilidad',
            'name' => 'Analista Contable Senior',
        ]);

        $response->assertRedirect(route('job-positions.index'));
        $this->assertSame('Analista Contable Senior', $position->fresh()->name);
    }
}

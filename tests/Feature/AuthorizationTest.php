<?php

namespace Tests\Feature;

use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\DeviceModel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Nota: se crea la categoría de forma explícita (en vez de dejar que
 * Device::factory()/DeviceModel::factory() generen cada una la suya) porque
 * ambas factories llaman independientemente a
 * fake()->unique()->randomElement(...) sobre la misma lista corta de
 * nombres, lo que puede colisionar contra la unicidad de "slug" en
 * device_categories. Es una fragilidad preexistente de esas factories, no
 * relacionada con el RBAC que este test verifica.
 */

/**
 * Verifica el modelo de permisos por rol introducido en la Fase 1 de la
 * auditoría (Hallazgo Crítico C1): Admin TI (todo), Técnico (operar sin
 * eliminar ni gestionar catálogos), Solo lectura (solo ver/listar/exportar).
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Admin TI']);
        Role::firstOrCreate(['name' => 'Técnico']);
        Role::firstOrCreate(['name' => 'Solo lectura']);
    }

    protected function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    protected function aDevice(): Device
    {
        $category = DeviceCategory::factory()->create(['name' => 'Categoría RBAC '.uniqid()]);
        $model = DeviceModel::factory()->create(['device_category_id' => $category->id]);

        return Device::factory()->disponible()->create([
            'device_category_id' => $category->id,
            'device_model_id' => $model->id,
        ]);
    }

    public function test_user_without_role_cannot_access_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertForbidden();
    }

    public function test_solo_lectura_can_view_but_not_write_or_delete(): void
    {
        $user = $this->userWithRole('Solo lectura');
        $device = $this->aDevice();

        $this->actingAs($user)->get('/devices')->assertOk();
        $this->actingAs($user)->get(route('devices.show', $device))->assertOk();

        $this->actingAs($user)->get('/devices/create')->assertForbidden();
        $this->actingAs($user)->post('/devices', [])->assertForbidden();
        $this->actingAs($user)->delete(route('devices.destroy', $device))->assertForbidden();
    }

    public function test_tecnico_can_write_but_not_delete_nor_manage_catalogs(): void
    {
        $user = $this->userWithRole('Técnico');
        $device = $this->aDevice();

        // Puede ver y acceder a los formularios de creación/edición.
        $this->actingAs($user)->get('/devices/create')->assertOk();

        // No puede eliminar registros.
        $this->actingAs($user)->delete(route('devices.destroy', $device))->assertForbidden();

        // No puede gestionar catálogos (categorías/modelos/puestos).
        $this->actingAs($user)->post('/device-categories', [
            'name' => 'Categoría de prueba',
        ])->assertForbidden();
    }

    public function test_admin_ti_can_delete_and_manage_catalogs(): void
    {
        $user = $this->userWithRole('Admin TI');
        $device = $this->aDevice();

        $response = $this->actingAs($user)->delete(route('devices.destroy', $device));
        $response->assertRedirect(route('devices.index'));
        $this->assertSoftDeleted($device);

        $response = $this->actingAs($user)->post('/device-categories', [
            'name' => 'Categoría de prueba RBAC',
        ]);
        $response->assertRedirect(route('device-categories.index'));
        $this->assertDatabaseHas('device_categories', ['name' => 'Categoría de prueba RBAC']);
    }
}

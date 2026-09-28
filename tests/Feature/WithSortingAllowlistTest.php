<?php

namespace Tests\Feature;

use App\Livewire\DeviceTable;
use App\Livewire\EmployeeTable;
use App\Livewire\MaintenanceTable;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Regresión del Hallazgo Medio M1: WithSorting::sortByField() valida $sortBy
 * contra $allowedSortColumns, pero los render() de DeviceTable/EmployeeTable/
 * MaintenanceTable usaban la propiedad pública $sortBy directamente en vez
 * de getSortBy(), dejando el allowlist sin efecto real: $sortBy es una
 * propiedad pública de Livewire que un cliente puede fijar a cualquier valor
 * en el payload de actualización sin pasar por sortByField(). Antes del fix,
 * esto producía una QueryException ("no such column") al intentar ordenar
 * por una columna arbitraria; ahora debe caer silenciosamente a 'created_at'.
 */
class WithSortingAllowlistTest extends TestCase
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

    public function test_device_table_ignores_a_disallowed_sort_column(): void
    {
        Livewire::actingAs($this->adminUser())
            ->test(DeviceTable::class)
            ->set('sortBy', 'notas; DROP TABLE devices;--')
            ->assertOk();
    }

    public function test_employee_table_ignores_a_disallowed_sort_column(): void
    {
        Livewire::actingAs($this->adminUser())
            ->test(EmployeeTable::class)
            ->set('sortBy', 'columna_inexistente')
            ->assertOk();
    }

    public function test_maintenance_table_ignores_a_disallowed_sort_column(): void
    {
        Livewire::actingAs($this->adminUser())
            ->test(MaintenanceTable::class)
            ->set('sortBy', 'columna_inexistente')
            ->assertOk();
    }
}

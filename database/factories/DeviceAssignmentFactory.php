<?php

namespace Database\Factories;

use App\Enums\DeviceCondition;
use App\Models\Device;
use App\Models\DeviceAssignment;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceAssignmentFactory extends Factory
{
    protected $model = DeviceAssignment::class;

    public function definition(): array
    {
        $assignedAt = fake()->dateTimeBetween('-2 years', 'now');

        return [
            'device_id' => Device::factory(),
            'employee_id' => Employee::factory(),
            'assigned_by_user_id' => User::factory(),
            'returned_by_user_id' => null,
            'assigned_at' => $assignedAt,
            'returned_at' => null,
            'condition_on_delivery' => fake()->randomElement([
                DeviceCondition::Nuevo,
                DeviceCondition::BuenEstado,
            ]),
            'condition_on_return' => null,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'returned_at' => null,
            'returned_by_user_id' => null,
            'condition_on_return' => null,
        ]);
    }

    public function returned(): static
    {
        return $this->state(fn (array $attributes) => [
            'returned_at' => fake()->dateTimeBetween($attributes['assigned_at'], 'now'),
            'returned_by_user_id' => User::factory(),
            'condition_on_return' => fake()->randomElement(DeviceCondition::cases()),
        ]);
    }

    public function withGoodCondition(): static
    {
        return $this->state(fn () => [
            'condition_on_delivery' => DeviceCondition::Nuevo,
            'condition_on_return' => DeviceCondition::BuenEstado,
        ]);
    }

    public function withDamage(): static
    {
        return $this->state(fn () => [
            'condition_on_return' => DeviceCondition::Daniado,
        ]);
    }
}

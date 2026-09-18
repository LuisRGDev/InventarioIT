<?php

namespace Database\Factories;

use App\Enums\ExtensionStatus;
use App\Models\OfficeExtension;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeExtensionFactory extends Factory
{
    protected $model = OfficeExtension::class;

    public function definition(): array
    {
        return [
            'extension_number' => fake()->numerify('####'),
            'direct_number' => fake()->optional(0.7)->numerify('+## ###-###-####'),
            'status' => fake()->randomElement(ExtensionStatus::cases()),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function disponible(): static
    {
        return $this->state(fn () => ['status' => ExtensionStatus::Disponible]);
    }

    public function asignada(): static
    {
        return $this->state(fn () => ['status' => ExtensionStatus::Asignada]);
    }

    public function baja(): static
    {
        return $this->state(fn () => ['status' => ExtensionStatus::Baja]);
    }
}

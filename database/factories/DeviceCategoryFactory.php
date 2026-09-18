<?php

namespace Database\Factories;

use App\Models\DeviceCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceCategoryFactory extends Factory
{
    protected $model = DeviceCategory::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement([
                'Portátil',
                'Desktop',
                'Monitor',
                'Impresora',
                'Smartphone',
                'Tablet',
                'Servidor',
                'Accesorio',
                'Red',
                'Almacenamiento',
            ]),
            'description' => fake()->sentence(),
        ];
    }

    public function portatil(): static
    {
        return $this->state(fn () => ['name' => 'Portátil']);
    }

    public function desktop(): static
    {
        return $this->state(fn () => ['name' => 'Desktop']);
    }

    public function monitor(): static
    {
        return $this->state(fn () => ['name' => 'Monitor']);
    }

    public function smartphone(): static
    {
        return $this->state(fn () => ['name' => 'Smartphone']);
    }

    public function printer(): static
    {
        return $this->state(fn () => ['name' => 'Impresora']);
    }
}

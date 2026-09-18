<?php

namespace Database\Factories;

use App\Enums\PhoneLineStatus;
use App\Models\PhoneLine;
use Illuminate\Database\Eloquent\Factories\Factory;

class PhoneLineFactory extends Factory
{
    protected $model = PhoneLine::class;

    public function definition(): array
    {
        return [
            'number' => fake()->numerify('###-###-####'),
            'provider' => fake()->randomElement(['Telcel', 'AT&T', 'Movistar', 'Altán', 'Iusacell']),
            'data_plan' => fake()->randomElement([
                'Sin datos',
                '2 GB',
                '5 GB',
                '10 GB',
                '15 GB',
                '20 GB',
                'Ilimitado',
            ]),
            'plan_cost' => fake()->randomFloat(2, 150, 800),
            'status' => fake()->randomElement(PhoneLineStatus::cases()),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function disponible(): static
    {
        return $this->state(fn () => ['status' => PhoneLineStatus::Disponible]);
    }

    public function asignada(): static
    {
        return $this->state(fn () => ['status' => PhoneLineStatus::Asignada]);
    }

    public function baja(): static
    {
        return $this->state(fn () => ['status' => PhoneLineStatus::Baja]);
    }

    public function withDataPlan(): static
    {
        return $this->state(fn () => [
            'data_plan' => fake()->randomElement(['10 GB', '15 GB', '20 GB', 'Ilimitado']),
            'plan_cost' => fake()->randomFloat(2, 350, 800),
        ]);
    }

    public function withoutDataPlan(): static
    {
        return $this->state(fn () => [
            'data_plan' => 'Sin datos',
            'plan_cost' => fake()->randomFloat(2, 150, 250),
        ]);
    }
}

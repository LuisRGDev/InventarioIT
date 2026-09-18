<?php

namespace Database\Factories;

use App\Enums\EmployeeStatus;
use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    public function definition(): array
    {
        $name = fake()->name();

        return [
            'employee_code' => strtoupper(fake()->bothify('??##')),
            'domain_account' => fake()->userName(),
            'name' => $name,
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->optional(0.8)->numerify('+## ###-###-####'),
            'department' => fake()->randomElement([
                'Tecnología',
                'Recursos Humanos',
                'Finanzas',
                'Operaciones',
                'Marketing',
                'Ventas',
                'Legal',
                'Administración',
                'Gerencia',
                'Soporte TI',
            ]),
            'position' => fake()->randomElement([
                'Analista',
                'Ingeniero',
                'Desarrollador',
                'Gerente',
                'Director',
                'Asistente',
                'Coordinador',
                'Técnico',
                'Jefe de Departamento',
                'Consultor',
            ]),
            'status' => EmployeeStatus::Activo,
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function activo(): static
    {
        return $this->state(fn () => ['status' => EmployeeStatus::Activo]);
    }

    public function inactivo(): static
    {
        return $this->state(fn () => ['status' => EmployeeStatus::Inactivo]);
    }

    public function baja(): static
    {
        return $this->state(fn () => ['status' => EmployeeStatus::Baja]);
    }
}

<?php

namespace Database\Factories;

use App\Models\JobPosition;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobPositionFactory extends Factory
{
    protected $model = JobPosition::class;

    public function definition(): array
    {
        return [
            'direction' => fake()->randomElement([
                'Gerencia General',
                'Dirección de Tecnología',
                'Dirección de Operaciones',
                'Dirección de Recursos Humanos',
                'Dirección Financiera',
                'Dirección Comercial',
                'Dirección Administrativa',
            ]),
            'area' => fake()->randomElement([
                'Desarrollo',
                'Infraestructura',
                'Soporte',
                'Seguridad',
                'Planeación',
                'Control',
                'Atención al Cliente',
                'Logística',
                'Compras',
                'Legal',
            ]),
            'name' => fake()->randomElement([
                'Ingeniero de Sistemas',
                'Analista de Soporte TI',
                'Desarrollador Full Stack',
                'Administrador de Redes',
                'Especialista en Seguridad',
                'Jefe de Proyectos',
                'Coordinador de TI',
                'Técnico en Computación',
                'Director de Tecnología',
                'Gerente de Infraestructura',
                'Analista de Datos',
                'Ingeniero DevOps',
            ]),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function tecnologia(): static
    {
        return $this->state(fn () => [
            'direction' => 'Dirección de Tecnología',
            'area' => fake()->randomElement(['Desarrollo', 'Infraestructura', 'Soporte', 'Seguridad']),
        ]);
    }

    public function rrhh(): static
    {
        return $this->state(fn () => [
            'direction' => 'Dirección de Recursos Humanos',
            'area' => fake()->randomElement(['Planeación', 'Control', 'Atención al Cliente']),
        ]);
    }
}

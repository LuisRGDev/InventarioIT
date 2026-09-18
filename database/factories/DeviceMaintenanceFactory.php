<?php

namespace Database\Factories;

use App\Enums\MaintenanceStatus;
use App\Enums\MaintenanceType;
use App\Models\Device;
use App\Models\DeviceMaintenance;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceMaintenanceFactory extends Factory
{
    protected $model = DeviceMaintenance::class;

    public function definition(): array
    {
        $type = fake()->randomElement(MaintenanceType::cases());

        return [
            'device_id' => Device::factory(),
            'user_id' => User::factory(),
            'type' => $type,
            'status' => MaintenanceStatus::Programado,
            'title' => $this->generateTitle($type),
            'description' => fake()->paragraph(),
            'resolution_notes' => null,
            'scheduled_at' => fake()->dateTimeBetween('now', '+3 months'),
            'started_at' => null,
            'completed_at' => null,
            'next_due_at' => fake()->optional(0.4)->dateTimeBetween('+3 months', '+1 year'),
        ];
    }

    public function programado(): static
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::Programado,
            'started_at' => null,
            'completed_at' => null,
        ]);
    }

    public function enProceso(): static
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::EnProceso,
            'started_at' => fake()->dateTimeBetween('-1 week', 'now'),
            'completed_at' => null,
        ]);
    }

    public function completado(): static
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::Completado,
            'started_at' => fake()->dateTimeBetween('-2 weeks', '-1 day'),
            'completed_at' => fake()->dateTimeBetween('-1 day', 'now'),
            'resolution_notes' => fake()->paragraph(),
        ]);
    }

    public function cancelado(): static
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::Cancelado,
            'resolution_notes' => fake()->sentence(),
        ]);
    }

    public function preventivo(): static
    {
        return $this->state(fn () => [
            'type' => MaintenanceType::Preventivo,
        ]);
    }

    public function correctivo(): static
    {
        return $this->state(fn () => [
            'type' => MaintenanceType::Correctivo,
        ]);
    }

    public function upgrade(): static
    {
        return $this->state(fn () => [
            'type' => MaintenanceType::Upgrade,
        ]);
    }

    private function generateTitle(MaintenanceType $type): string
    {
        return match ($type) {
            MaintenanceType::Preventivo => fake()->randomElement([
                'Revisión preventiva trimestral',
                'Limpieza de hardware',
                'Actualización de firmware',
                'Revisión de seguridad',
                'Mantenimiento preventivo anual',
            ]),
            MaintenanceType::Correctivo => fake()->randomElement([
                'Reparación de pantalla',
                'Cambio de disco duro',
                'Reemplazo de batería',
                'Reparación de teclado',
                'Solución de sobrecalentamiento',
                'Reparación de puerto USB',
            ]),
            MaintenanceType::Upgrade => fake()->randomElement([
                'Actualización de RAM',
                'Cambio a SSD NVMe',
                'Actualización de SO a Windows 11',
                'Instalación de software especializado',
                'Ampliación de almacenamiento',
            ]),
        };
    }
}

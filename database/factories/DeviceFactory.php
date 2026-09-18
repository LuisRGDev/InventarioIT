<?php

namespace Database\Factories;

use App\Enums\DeviceStatus;
use App\Models\Device;
use App\Models\DeviceCategory;
use App\Models\DeviceModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceFactory extends Factory
{
    protected $model = Device::class;

    public function definition(): array
    {
        $brand = fake()->randomElement(['Dell', 'Lenovo', 'HP', 'Apple', 'ASUS', 'Acer', 'Samsung', 'Microsoft']);
        $model = fake()->randomElement(['Latitude 5540', 'ThinkPad T14s', 'EliteBook 840', 'MacBook Pro 14"', 'ZenBook 14', 'Swift 3', 'Surface Pro 9', 'Galaxy Book3']);

        return [
            'device_category_id' => DeviceCategory::factory(),
            'device_model_id' => DeviceModel::factory(),
            'serial_number' => strtoupper(fake()->bothify('??#####-#####')),
            'service_tag' => fake()->bothify('???#####'),
            'computer_name' => strtoupper(fake()->bothify('CTI-??-###')),
            'bitlocker_identifier' => fake()->optional(0.7)->uuid(),
            'bitlocker_key' => fake()->optional(0.7)->hexify('????????-????-????-????-????????????'),
            'mac_address_ethernet' => fake()->optional(0.8)->macAddress(),
            'mac_address_wifi' => fake()->optional(0.8)->macAddress(),
            'imei' => fake()->optional(0.5)->numerify('###############'),
            'brand' => $brand,
            'model' => $model,
            'status' => fake()->randomElement(DeviceStatus::cases()),
            'purchase_date' => fake()->dateTimeBetween('-3 years', 'now'),
            'warranty_expires_at' => fake()->optional(0.6)->dateTimeBetween('+6 months', '+4 years'),
            'specs' => [
                'cpu' => fake()->randomElement(['Intel Core i5', 'Intel Core i7', 'AMD Ryzen 5', 'AMD Ryzen 7', 'Apple M2']),
                'ram' => fake()->randomElement(['8 GB', '16 GB', '32 GB']),
                'storage' => fake()->randomElement(['256 GB SSD', '512 GB SSD', '1 TB SSD']),
            ],
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function disponible(): static
    {
        return $this->state(fn () => ['status' => DeviceStatus::Disponible]);
    }

    public function asignado(): static
    {
        return $this->state(fn () => ['status' => DeviceStatus::Asignado]);
    }

    public function enReparacion(): static
    {
        return $this->state(fn () => ['status' => DeviceStatus::EnReparacion]);
    }

    public function obsoleto(): static
    {
        return $this->state(fn () => ['status' => DeviceStatus::Obsoleto]);
    }

    public function baja(): static
    {
        return $this->state(fn () => ['status' => DeviceStatus::Baja]);
    }

    public function laptop(): static
    {
        return $this->state(fn () => [
            'brand' => fake()->randomElement(['Dell', 'Lenovo', 'HP', 'Apple']),
            'model' => fake()->randomElement(['Latitude 5540', 'ThinkPad T14s', 'EliteBook 840', 'MacBook Pro 14"']),
        ]);
    }

    public function desktop(): static
    {
        return $this->state(fn () => [
            'brand' => fake()->randomElement(['Dell', 'Lenovo', 'HP']),
            'model' => fake()->randomElement(['OptiPlex 7010', 'ThinkCentre M90', 'ProDesk 400']),
            'mac_address_ethernet' => fake()->macAddress(),
            'mac_address_wifi' => null,
        ]);
    }

    public function smartphone(): static
    {
        return $this->state(fn () => [
            'brand' => fake()->randomElement(['Samsung', 'Apple', 'Google']),
            'model' => fake()->randomElement(['Galaxy S24', 'iPhone 15', 'Pixel 8']),
            'imei' => fake()->numerify('###############'),
            'mac_address_ethernet' => null,
            'computer_name' => null,
            'service_tag' => null,
            'specs' => null,
        ]);
    }
}

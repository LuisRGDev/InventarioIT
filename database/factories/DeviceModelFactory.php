<?php

namespace Database\Factories;

use App\Models\DeviceCategory;
use App\Models\DeviceModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeviceModelFactory extends Factory
{
    protected $model = DeviceModel::class;

    public function definition(): array
    {
        $brand = fake()->randomElement(['Dell', 'Lenovo', 'HP', 'Apple', 'ASUS', 'Acer', 'Samsung', 'Microsoft', 'Cisco']);
        $model = fake()->randomElement(['Latitude', 'ThinkPad', 'EliteBook', 'MacBook', 'ZenBook', 'Swift', 'Surface', 'Galaxy', 'Webex']);

        return [
            'device_category_id' => DeviceCategory::factory(),
            'brand' => $brand,
            'model' => $model,
            'variant' => fake()->optional(0.4)->randomElement(['Pro', 'Air', 'Ultra', 'Slim', 'Plus', 'Max']),
            'cpu' => fake()->randomElement([
                'Intel Core i5-1340P',
                'Intel Core i7-1360P',
                'AMD Ryzen 5 7530U',
                'AMD Ryzen 7 7840U',
                'Apple M2',
                'Apple M3 Pro',
                'Intel Core i9-13900H',
            ]),
            'cores' => fake()->randomElement([4, 6, 8, 10, 12]),
            'ram' => fake()->randomElement(['8 GB', '16 GB', '32 GB', '64 GB']),
            'storage' => fake()->randomElement(['256 GB SSD', '512 GB SSD', '1 TB SSD', '2 TB SSD']),
            'os' => fake()->randomElement(['Windows 11 Pro', 'Windows 11 Home', 'macOS Sonoma', 'Ubuntu 22.04', 'Sin SO']),
            'notes' => fake()->optional(0.3)->sentence(),
        ];
    }

    public function laptop(): static
    {
        return $this->state(fn () => [
            'cpu' => fake()->randomElement(['Intel Core i5-1340P', 'Intel Core i7-1360P', 'AMD Ryzen 5 7530U']),
            'ram' => fake()->randomElement(['8 GB', '16 GB', '32 GB']),
            'storage' => fake()->randomElement(['256 GB SSD', '512 GB SSD', '1 TB SSD']),
        ]);
    }
}

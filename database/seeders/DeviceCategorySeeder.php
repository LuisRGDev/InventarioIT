<?php

namespace Database\Seeders;

use App\Models\DeviceCategory;
use Illuminate\Database\Seeder;

class DeviceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Portátil',    'slug' => 'portatil',    'description' => 'Computadoras portátiles'],
            ['name' => 'Desktop',     'slug' => 'desktop',     'description' => 'Computadoras de escritorio'],
            ['name' => 'Smartphone',  'slug' => 'smartphone',  'description' => 'Teléfonos inteligentes corporativos'],
        ];

        foreach ($categories as $category) {
            DeviceCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}

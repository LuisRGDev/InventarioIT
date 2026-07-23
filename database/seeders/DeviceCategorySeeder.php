<?php

namespace Database\Seeders;

use App\Models\DeviceCategory;
use Illuminate\Database\Seeder;

class DeviceCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Laptop',       'slug' => 'laptop',       'description' => 'Computadoras portátiles'],
            ['name' => 'Desktop',      'slug' => 'desktop',      'description' => 'Computadoras de escritorio'],
            ['name' => 'Smartphone',   'slug' => 'smartphone',   'description' => 'Teléfonos inteligentes corporativos'],
            ['name' => 'Monitor',      'slug' => 'monitor',      'description' => 'Pantallas y monitores'],
            ['name' => 'Periférico',   'slug' => 'periferico',   'description' => 'Teclados, mouse, auriculares, webcams'],
            ['name' => 'Impresora',    'slug' => 'impresora',    'description' => 'Impresoras y escáneres'],
            ['name' => 'Red',          'slug' => 'red',          'description' => 'Switches, routers, access points'],
            ['name' => 'Almacenamiento', 'slug' => 'almacenamiento', 'description' => 'Discos externos, USB, NAS'],
        ];

        foreach ($categories as $category) {
            DeviceCategory::firstOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}

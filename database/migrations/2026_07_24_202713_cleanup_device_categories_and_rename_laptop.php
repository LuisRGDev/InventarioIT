<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DeviceCategory;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Eliminar categorías que no sean portatil, desktop, smartphone (incluyendo soft deletes)
        DeviceCategory::whereNotIn('slug', ['portatil', 'desktop', 'smartphone', 'laptop'])->forceDelete();

        // 2. Renombrar Laptop a Portátil
        DeviceCategory::where('slug', 'laptop')->update([
            'name' => 'Portátil',
            'slug' => 'portatil'
        ]);

        // Asegurarnos de que las 3 categorías existan (por si no han corrido el seeder)
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No hay marcha atrás para la eliminación manual de la base de datos de esta forma.
    }
};

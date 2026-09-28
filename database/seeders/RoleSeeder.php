<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'Admin TI']);
        Role::firstOrCreate(['name' => 'Técnico']);
        Role::firstOrCreate(['name' => 'Solo lectura']);

        // Cuenta administradora de prueba: solo en local/testing. En producción
        // no se crea ninguna cuenta por defecto; el primer Admin TI debe
        // provisionarse manualmente (php artisan tinker o un seeder dedicado).
        if (app()->environment(['local', 'testing'])) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@itam.local'],
                [
                    'name' => 'Administrador TI',
                    'password' => bcrypt('password'),
                ]
            );

            $admin->assignRole($adminRole);
        }
    }
}

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
        $adminRole    = Role::firstOrCreate(['name' => 'Admin TI']);
        $tecnicoRole  = Role::firstOrCreate(['name' => 'Técnico']);
        $readonlyRole = Role::firstOrCreate(['name' => 'Solo lectura']);

        // Crear usuario administrador de prueba
        $admin = User::firstOrCreate(
            ['email' => 'admin@itam.local'],
            [
                'name'     => 'Administrador TI',
                'password' => bcrypt('password'),
            ]
        );

        $admin->assignRole($adminRole);
    }
}

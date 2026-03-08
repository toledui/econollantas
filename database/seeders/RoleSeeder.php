<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Users\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'super_admin', 'description' => 'Acceso total al sistema'],
            ['name' => 'admin', 'description' => 'Administrador de plataforma'],
            ['name' => 'user', 'description' => 'Usuario final / Estudiante'],
            ['name' => 'manager', 'description' => 'Jefe de sucursal / departamento'],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }
    }
}

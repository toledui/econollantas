<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Users\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Ventas', 'active' => true],
            ['name' => 'Servicio Técnico', 'active' => true],
            ['name' => 'Administración', 'active' => true],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['name' => $dept['name']], $dept);
        }
    }
}

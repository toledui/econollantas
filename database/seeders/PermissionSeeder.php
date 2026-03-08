<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Users\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'admins.create', 'group' => 'administracion'],
            ['name' => 'admins.delete', 'group' => 'administracion'],
            ['name' => 'courses.view', 'group' => 'cursos'],
            ['name' => 'courses.create', 'group' => 'cursos'],
            ['name' => 'courses.edit', 'group' => 'cursos'],
            ['name' => 'courses.delete', 'group' => 'cursos'],
            ['name' => 'courses.assign', 'group' => 'cursos'],
            ['name' => 'scorecard.edit', 'group' => 'scorecard'],
            ['name' => 'library.view', 'group' => 'biblioteca'],
            ['name' => 'library.create', 'group' => 'biblioteca'],
            ['name' => 'library.edit', 'group' => 'biblioteca'],
            ['name' => 'library.delete', 'group' => 'biblioteca'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }
}

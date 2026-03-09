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
            ['name' => 'users.view', 'group' => 'usuarios'],
            ['name' => 'users.create', 'group' => 'usuarios'],
            ['name' => 'users.edit', 'group' => 'usuarios'],
            ['name' => 'users.delete', 'group' => 'usuarios'],
            ['name' => 'departments.view', 'group' => 'departamentos'],
            ['name' => 'departments.create', 'group' => 'departamentos'],
            ['name' => 'departments.edit', 'group' => 'departamentos'],
            ['name' => 'departments.delete', 'group' => 'departamentos'],
            ['name' => 'branches.view', 'group' => 'sucursales'],
            ['name' => 'branches.create', 'group' => 'sucursales'],
            ['name' => 'branches.edit', 'group' => 'sucursales'],
            ['name' => 'branches.delete', 'group' => 'sucursales'],
            ['name' => 'announcements.view', 'group' => 'avisos'],
            ['name' => 'announcements.create', 'group' => 'avisos'],
            ['name' => 'announcements.edit', 'group' => 'avisos'],
            ['name' => 'announcements.delete', 'group' => 'avisos'],
            ['name' => 'settings.view', 'group' => 'configuracion'],
            ['name' => 'settings.edit', 'group' => 'configuracion'],
            ['name' => 'reports.view', 'group' => 'reportes'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission['name']], $permission);
        }
    }
}

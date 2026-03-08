<?php

namespace Database\Seeders;

use App\Modules\Users\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            DepartmentSeeder::class,
            BranchSeeder::class,
        ]);

        $branch = \App\Modules\Branches\Models\Branch::where('name', 'Sucursal Norte')->first();

        $admin = User::firstOrCreate(['email' => 'admin@econollantas.com'], [
            'name' => 'Super Admin',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'status' => 'active',
            'primary_branch_id' => $branch->id,
        ]);

        $superAdminRole = \App\Modules\Users\Models\Role::where('name', 'super_admin')->first();
        if ($superAdminRole) {
            $superAdminRole->permissions()->sync(\App\Modules\Users\Models\Permission::all());
        }

        $adminRole = \App\Modules\Users\Models\Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->permissions()->sync(\App\Modules\Users\Models\Permission::whereIn('group', ['cursos', 'biblioteca'])->get());
        }

        $admin->roles()->sync($superAdminRole);
    }
}

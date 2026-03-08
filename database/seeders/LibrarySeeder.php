<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Library\Models\LibraryCategory;
use App\Modules\Library\Models\ResourceType;
use App\Modules\Users\Models\Role;
use App\Modules\Users\Models\Permission;

class LibrarySeeder extends Seeder
{
    public function run(): void
    {
        // Categorías
        $categories = [
            ['name' => 'Ventas', 'description' => 'Recursos relacionados con ventas y comercialización'],
            ['name' => 'Marketing', 'description' => 'Materiales de marketing y publicidad'],
            ['name' => 'Operaciones', 'description' => 'Documentos operativos y procedimientos'],
            ['name' => 'Recursos Humanos', 'description' => 'Políticas y guías de RH'],
            ['name' => 'Finanzas', 'description' => 'Reportes y materiales financieros'],
            ['name' => 'Tecnología', 'description' => 'Manuales técnicos y de TI'],
            ['name' => 'Capacitación', 'description' => 'Material de entrenamiento general'],
            ['name' => 'General', 'description' => 'Contenido de uso general'],
        ];

        foreach ($categories as $cat) {
            LibraryCategory::firstOrCreate(['name' => $cat['name']], array_merge($cat, ['active' => true]));
        }

        // Tipos de recurso
        $types = [
            ['name' => 'Manual'],
            ['name' => 'Presentación'],
            ['name' => 'Video'],
            ['name' => 'Infografía'],
            ['name' => 'Reporte'],
            ['name' => 'Política'],
            ['name' => 'Guía Rápida'],
            ['name' => 'Formato'],
        ];

        foreach ($types as $type) {
            ResourceType::firstOrCreate(['name' => $type['name']]);
        }

        // Assign ALL permissions to super_admin role
        $superAdmin = Role::where('name', 'super_admin')->first();
        if ($superAdmin) {
            $allPermissions = Permission::pluck('id');
            $superAdmin->permissions()->syncWithoutDetaching($allPermissions);
        }
    }
}

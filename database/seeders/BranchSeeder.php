<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Branches\Models\Branch;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        Branch::firstOrCreate(['name' => 'Sucursal Norte'], [
            'name' => 'Sucursal Norte',
            'code' => 'NRT-01',
            'active' => true,
        ]);
    }
}

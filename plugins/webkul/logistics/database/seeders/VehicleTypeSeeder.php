<?php

namespace Webkul\Logistics\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Logistics\Models\VehicleType;

class VehicleTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'MOTORBIKE', 'name' => 'Motorbike', 'capacity_kg' => 50, 'capacity_m3' => 0.2],
            ['code' => 'VAN', 'name' => 'Van', 'capacity_kg' => 1000, 'capacity_m3' => 8],
            ['code' => 'RIGID', 'name' => 'Rigid truck', 'capacity_kg' => 8000, 'capacity_m3' => 40],
            ['code' => 'TRAILER', 'name' => 'Articulated trailer', 'capacity_kg' => 24000, 'capacity_m3' => 80],
        ];

        foreach ($rows as $sort => $row) {
            VehicleType::withoutGlobalScopes()->updateOrCreate(
                ['code' => $row['code'], 'company_id' => null],
                [
                    'name'        => $row['name'],
                    'capacity_kg' => $row['capacity_kg'],
                    'capacity_m3' => $row['capacity_m3'],
                    'is_active'   => true,
                    'sort'        => $sort + 1,
                ],
            );
        }
    }
}

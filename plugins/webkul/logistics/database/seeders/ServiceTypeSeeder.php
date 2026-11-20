<?php

namespace Webkul\Logistics\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Logistics\Enums\TransportMode;
use Webkul\Logistics\Models\ServiceType;

class ServiceTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'LOCAL', 'name' => 'Local delivery', 'product_reference' => 'LOG-DELIVERY'],
            ['code' => 'HAULAGE', 'name' => 'Haulage', 'product_reference' => 'LOG-FREIGHT'],
            ['code' => 'COURIER', 'name' => 'Courier', 'product_reference' => 'LOG-DELIVERY'],
            ['code' => 'DISTRIBUTION', 'name' => 'Distribution', 'product_reference' => 'LOG-FREIGHT'],
        ];

        foreach ($rows as $sort => $row) {
            ServiceType::withoutGlobalScopes()->updateOrCreate(
                ['code' => $row['code'], 'company_id' => null],
                [
                    'name'              => $row['name'],
                    'product_reference' => $row['product_reference'],
                    'transport_mode'    => TransportMode::ROAD,
                    'is_active'         => true,
                    'sort'              => $sort + 1,
                ],
            );
        }
    }
}

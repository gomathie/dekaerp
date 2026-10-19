<?php

namespace Webkul\Logistics\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Logistics\Models\PackageType;

class PackageTypeSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            'PALLET' => 'Pallet',
            'CARTON' => 'Carton',
            'CRATE'  => 'Crate',
            'DRUM'   => 'Drum',
            'BAG'    => 'Bag',
            'LOOSE'  => 'Loose',
        ];

        $sort = 0;

        foreach ($rows as $code => $name) {
            PackageType::withoutGlobalScopes()->updateOrCreate(
                ['code' => $code, 'company_id' => null],
                ['name' => $name, 'is_active' => true, 'sort' => ++$sort],
            );
        }
    }
}

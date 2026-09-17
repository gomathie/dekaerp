<?php

namespace Webkul\Logistics\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Only shared configuration is seeded (company_id null, keyed by code, no fixed
     * ids). Nothing is created per company: that happens when a company enables
     * Logistics (CompanyProvisioner).
     */
    public function run(): void
    {
        $this->call([
            ServiceTypeSeeder::class,
            VehicleTypeSeeder::class,
            PackageTypeSeeder::class,
            ExpenseCategorySeeder::class,
        ]);
    }
}

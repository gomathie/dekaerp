<?php

namespace Webkul\Logistics\Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Logistics\Models\ExpenseCategory;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            ['code' => 'FUEL', 'name' => 'Fuel', 'requires_receipt' => true, 'is_subcontracting' => false],
            ['code' => 'TOLLS', 'name' => 'Tolls', 'requires_receipt' => true, 'is_subcontracting' => false],
            ['code' => 'LOADING', 'name' => 'Loading', 'requires_receipt' => false, 'is_subcontracting' => false],
            ['code' => 'UNLOADING', 'name' => 'Unloading', 'requires_receipt' => false, 'is_subcontracting' => false],
            ['code' => 'PARKING', 'name' => 'Parking', 'requires_receipt' => false, 'is_subcontracting' => false],
            ['code' => 'ALLOWANCE', 'name' => 'Driver allowance', 'requires_receipt' => false, 'is_subcontracting' => false],
            ['code' => 'SUBCONTRACTOR', 'name' => 'Subcontractor / carrier', 'requires_receipt' => true, 'is_subcontracting' => true],
            ['code' => 'REPAIRS', 'name' => 'Repairs', 'requires_receipt' => true, 'is_subcontracting' => false],
            ['code' => 'OTHER', 'name' => 'Other', 'requires_receipt' => false, 'is_subcontracting' => false],
        ];

        foreach ($rows as $sort => $row) {
            ExpenseCategory::withoutGlobalScopes()->updateOrCreate(
                ['code' => $row['code'], 'company_id' => null],
                [
                    'name'              => $row['name'],
                    'requires_receipt'  => $row['requires_receipt'],
                    'is_subcontracting' => $row['is_subcontracting'],
                    'is_active'         => true,
                    'sort'              => $sort + 1,
                ],
            );
        }
    }
}

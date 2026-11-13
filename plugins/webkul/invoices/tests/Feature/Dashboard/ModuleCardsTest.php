<?php

use Webkul\Inventory\Filament\OverviewCard;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * Every "Across the company" card runs its real queries (2026-11-13). The
 * registry reports and hides a card whose query fails, so a wrong column
 * would otherwise vanish from the dashboard silently; here it fails a test.
 */
dataset('module cards', [
    'inventory'   => ['inventories', OverviewCard::class],
    'projects'    => ['projects', Webkul\Project\Filament\OverviewCard::class],
    'recruitment' => ['recruitments', Webkul\Recruitment\Filament\OverviewCard::class],
    'employees'   => ['time-off', Webkul\TimeOff\Filament\OverviewCard::class],
    'logistics'   => ['logistics', Webkul\Logistics\Filament\OverviewCard::class],
]);

it('builds the module card from real queries', function (string $plugin, string $card): void {
    TestBootstrapHelper::ensurePluginInstalled($plugin);

    CompanyHelper::actingAsCompanyUser(Company::factory()->create(), []);

    $data = $card::data();

    expect($data['label'])->toBeString()->not->toBeEmpty()
        ->and($data['items'])->toHaveCount(3)
        ->and(array_column($data['items'], 'value'))->each->toBeString();
})->with('module cards');

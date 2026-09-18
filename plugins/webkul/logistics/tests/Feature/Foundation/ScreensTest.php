<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Logistics\Filament\Clusters\Configurations\Pages\ManageCompanySettings;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource\Pages\ManageExpenseCategories;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource\Pages\ManagePackageTypes;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource\Pages\ManageServiceTypes;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource\Pages\ManageVehicleTypes;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\ServiceType;
use Webkul\Logistics\Support\LogisticsAccess;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();

    // See ShipmentResourceTest: panel routes are missing when a file runs alone.
    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
});

it('renders the configuration lists for users with permission', function (string $page, string $subject) {
    $company = LogisticsHelper::company();

    FilamentHelper::actingAsCompanyUser($company, ["view_any_logistics_{$subject}"]);

    Livewire::test($page)->assertOk();
})->with([
    'service types'      => [ManageServiceTypes::class, 'service::type'],
    'vehicle types'      => [ManageVehicleTypes::class, 'vehicle::type'],
    'package types'      => [ManagePackageTypes::class, 'package::type'],
    'expense categories' => [ManageExpenseCategories::class, 'expense::category'],
]);

it('forbids the configuration lists without permission', function () {
    FilamentHelper::actingAsCompanyUser(LogisticsHelper::company(), []);

    Livewire::test(ManageServiceTypes::class)->assertForbidden();
});

it('shows shared service types to a company user', function () {
    $company = LogisticsHelper::company();

    FilamentHelper::actingAsCompanyUser($company, ['view_any_logistics_service::type']);

    $shared = ServiceType::query()->whereNull('company_id')->orderBy('sort')->first();

    Livewire::test(ManageServiceTypes::class)->assertCanSeeTableRecords([$shared]);
});

it('enables Logistics and saves settings from the settings page', function () {
    $company = LogisticsHelper::company();

    FilamentHelper::actingAsCompanyUser($company, ['page_logistics_manage_company_settings']);

    Livewire::test(ManageCompanySettings::class)
        ->assertOk()
        ->assertActionVisible('enable')
        ->callAction('enable')
        ->assertHasNoActionErrors();

    LogisticsAccess::flush();

    expect(LogisticsAccess::enabledFor($company->id))->toBeTrue();

    Livewire::test(ManageCompanySettings::class)
        ->fillForm([
            'free_waiting_minutes' => 45,
            'require_pod_photo'    => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $setting = CompanySetting::forCompany($company->id);

    expect($setting->free_waiting_minutes)->toBe(45)
        ->and($setting->require_pod_photo)->toBeTrue()
        ->and($setting->is_enabled)->toBeTrue();
});

it('forbids the settings page without its permission', function () {
    FilamentHelper::actingAsCompanyUser(LogisticsHelper::company(), []);

    Livewire::test(ManageCompanySettings::class)->assertForbidden();
});

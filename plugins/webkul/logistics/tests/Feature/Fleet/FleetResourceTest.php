<?php

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource\Pages\ListDrivers;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Pages\CreateVehicle;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource\Pages\ListVehicles;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Vehicle;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();

    URL::resolveMissingNamedRoutesUsing(fn (): string => '#');
});

it('keeps vehicle registration numbers unique per company only', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    Vehicle::factory()->create([
        'company_id'       => $a->id,
        'registration_no'  => 'KDA-001',
    ]);

    FilamentHelper::actingAsCompanyUser([$a, $b], ['view_any_logistics_vehicle', 'create_logistics_vehicle']);

    Livewire::test(CreateVehicle::class)
        ->assertOk()
        ->fillForm([
            'company_id'       => $a->id,
            'registration_no'  => 'KDA-001',
            'ownership'        => 'owned',
            'is_active'        => true,
        ])
        ->call('create')
        ->assertHasFormErrors(['registration_no']);

    Livewire::test(CreateVehicle::class)
        ->assertOk()
        ->fillForm([
            'company_id'       => $b->id,
            'registration_no'  => 'KDA-001',
            'ownership'        => 'owned',
            'is_active'        => true,
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Vehicle::withoutGlobalScopes()->where('registration_no', 'KDA-001')->count())->toBe(2);
});

it('marks expired and soon-expiring driver licences at the 30 day boundary', function () {
    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $expired = Driver::factory()->create([
        'company_id'          => $company->id,
        'license_expires_at'  => now()->subDay()->toDateString(),
    ]);
    $boundary = Driver::factory()->create([
        'company_id'          => $company->id,
        'license_expires_at'  => now()->addDays(30)->toDateString(),
    ]);
    $clear = Driver::factory()->create([
        'company_id'          => $company->id,
        'license_expires_at'  => now()->addDays(31)->toDateString(),
    ]);

    expect($expired->isLicenseExpired())->toBeTrue()
        ->and($expired->licenseExpiresWithin(30))->toBeFalse()
        ->and($boundary->isLicenseExpired())->toBeFalse()
        ->and($boundary->licenseExpiresWithin(30))->toBeTrue()
        ->and($clear->licenseExpiresWithin(30))->toBeFalse();

    FilamentHelper::actingAsCompanyUser($company, ['view_any_logistics_driver', 'update_logistics_driver']);

    Livewire::test(ListDrivers::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$expired, $boundary, $clear])
        ->assertCanRenderTableColumn('license_expires_at')
        ->assertCanRenderTableColumn('license_number');

    FilamentHelper::actingAsCompanyUser($company, ['view_any_logistics_driver']);

    Livewire::test(ListDrivers::class)
        ->assertOk()
        ->assertTableColumnHidden('license_number');
});

it('isolates fleet records by company', function () {
    $a = LogisticsHelper::enable(LogisticsHelper::company());
    $b = LogisticsHelper::enable(LogisticsHelper::company());

    $myVehicle = Vehicle::factory()->create(['company_id' => $a->id]);
    $theirVehicle = Vehicle::factory()->create(['company_id' => $b->id]);
    $myDriver = Driver::factory()->create(['company_id' => $a->id]);
    $theirDriver = Driver::factory()->create(['company_id' => $b->id]);

    FilamentHelper::actingAsCompanyUser($a, ['view_any_logistics_vehicle', 'view_any_logistics_driver']);

    Livewire::test(ListVehicles::class)
        ->assertCanSeeTableRecords([$myVehicle])
        ->assertCanNotSeeTableRecords([$theirVehicle]);

    Livewire::test(ListDrivers::class)
        ->assertCanSeeTableRecords([$myDriver])
        ->assertCanNotSeeTableRecords([$theirDriver]);
});

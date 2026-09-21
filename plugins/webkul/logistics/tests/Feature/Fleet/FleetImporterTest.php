<?php

use Webkul\Employee\Models\Employee;
use Webkul\Logistics\Models\Driver;
use Webkul\Logistics\Models\Vehicle;
use Webkul\Logistics\Services\FleetImporter;
use Webkul\Maintenance\Models\Equipment;

require_once __DIR__.'/../../Helpers/LogisticsHelper.php';

beforeEach(function () {
    LogisticsHelper::install();
});

it('imports employees and maintenance equipment idempotently', function () {
    TestBootstrapHelper::ensurePluginInstalled('maintenance');

    $company = LogisticsHelper::enable(LogisticsHelper::company());

    $employeeA = Employee::query()->create([
        'name'        => 'Ada Driver',
        'work_phone'  => '+254700000001',
        'company_id'  => $company->id,
        'is_active'   => true,
    ]);
    $employeeB = Employee::query()->create([
        'name'         => 'Biko Driver',
        'mobile_phone' => '+254700000002',
        'company_id'   => $company->id,
        'is_active'    => true,
    ]);

    $equipmentA = Equipment::query()->create([
        'name'              => 'Truck 14',
        'serial_no'         => 'TRK-014',
        'effective_date'    => now()->toDateString(),
        'company_id'        => $company->id,
    ]);
    $equipmentB = Equipment::query()->create([
        'name'              => 'Van 2',
        'partner_ref'       => 'VAN-002',
        'effective_date'    => now()->toDateString(),
        'company_id'        => $company->id,
    ]);

    $importer = app(FleetImporter::class);

    expect($importer->driversFromEmployees([$employeeA->id, $employeeB->id]))->toBe([
        'created' => 2,
        'skipped' => 0,
    ])->and($importer->driversFromEmployees([$employeeA->id, $employeeB->id]))->toBe([
        'created' => 0,
        'skipped' => 2,
    ])->and(Driver::withoutGlobalScopes()->whereIn('employee_id', [$employeeA->id, $employeeB->id])->count())->toBe(2);

    expect($importer->vehiclesFromMaintenanceEquipment([$equipmentA->id, $equipmentB->id]))->toBe([
        'created' => 2,
        'skipped' => 0,
    ])->and($importer->vehiclesFromMaintenanceEquipment([$equipmentA->id, $equipmentB->id]))->toBe([
        'created' => 0,
        'skipped' => 2,
    ])->and(Vehicle::withoutGlobalScopes()->whereIn('equipment_id', [$equipmentA->id, $equipmentB->id])->count())->toBe(2);

    expect(Vehicle::withoutGlobalScopes()->where('registration_no', 'TRK-014')->exists())->toBeTrue()
        ->and(Vehicle::withoutGlobalScopes()->where('registration_no', 'VAN-002')->exists())->toBeTrue();
});

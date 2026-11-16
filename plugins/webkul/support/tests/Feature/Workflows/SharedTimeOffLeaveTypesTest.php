<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

require_once __DIR__.'/../../Helpers/TestBootstrapHelper.php';

/**
 * The default time-off leave types must be visible to every company.
 *
 * CompanyScope reads a null company_id as shared, so the seeder inserts the
 * defaults with no company and every tenant sees them. Databases seeded before
 * 2026-08-04 got the first company instead, and
 * time-off/2026_08_04_100000_share_default_time_off_leave_types is the repair
 * that commit b9ac266e6 shipped alongside the seeder change.
 *
 * That migration was written but left out of the plugin's hasMigrations() list,
 * so nothing ever ran it: on an older database the defaults stayed owned by the
 * first company and no other company could see them. It is registered as of
 * 2026-10-23, and this pins both halves - the seeder's end state and the
 * migration's repair.
 *
 * It lives in the support suite rather than the time-off plugin because
 * time-off has no tests and no phpunit.xml testsuite, so anything placed there
 * would never execute - which is how the migration went unnoticed in the first
 * place.
 */
$sharedNames = [
    'Paid Time Off',
    'Sick Time Off',
    'Compensatory Days test',
    'Unpaid',
];

$companyOwnedNames = [
    'Training Time Off',
    'Parental Leaves',
];

$migrationPath = 'plugins/webkul/time-off/database/migrations/2026_08_04_100000_share_default_time_off_leave_types.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('time-off');
});

it('installs time-off with its leave types table and default rows', function () use ($sharedNames, $companyOwnedNames) {
    // Guards every assertion below: with no rows, a "company_id is null" check
    // passes by counting nothing.
    expect(Schema::hasTable('time_off_leave_types'))->toBeTrue();

    foreach (array_merge($sharedNames, $companyOwnedNames) as $name) {
        expect(DB::table('time_off_leave_types')->where('name', $name)->exists())
            ->toBeTrue("the seeder no longer creates the '{$name}' leave type; update this test and the migration's name list together");
    }
});

it('seeds the default leave types with no company so every tenant sees them', function () use ($sharedNames) {
    $owned = DB::table('time_off_leave_types')
        ->whereIn('name', $sharedNames)
        ->whereNotNull('company_id')
        ->pluck('name')
        ->all();

    expect($owned)->toBe([]);
});

it('nulls the company on defaults a pre-2026-08-04 database left owned', function () use ($sharedNames, $migrationPath) {
    $companyId = DB::table('companies')->min('id');

    expect($companyId)->not->toBeNull();

    // Put the table back into the state an older database is in.
    $dirtied = DB::table('time_off_leave_types')
        ->whereIn('name', $sharedNames)
        ->update(['company_id' => $companyId]);

    expect($dirtied)->toBe(count($sharedNames));

    (require base_path($migrationPath))->up();

    $stillOwned = DB::table('time_off_leave_types')
        ->whereIn('name', $sharedNames)
        ->whereNotNull('company_id')
        ->pluck('name')
        ->all();

    expect($stillOwned)->toBe([]);
});

it('leaves deliberately company-owned leave types alone', function () use ($companyOwnedNames, $migrationPath) {
    $companyId = DB::table('companies')->min('id');

    DB::table('time_off_leave_types')
        ->whereIn('name', $companyOwnedNames)
        ->update(['company_id' => $companyId]);

    (require base_path($migrationPath))->up();

    $names = DB::table('time_off_leave_types')
        ->whereIn('name', $companyOwnedNames)
        ->whereNull('company_id')
        ->pluck('name')
        ->all();

    expect($names)->toBe([]);
});

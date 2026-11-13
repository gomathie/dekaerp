<?php

use Webkul\Attendance\Filament\Clusters\Reporting;
use Webkul\Attendance\Filament\Pages\ManageSettings;
use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Support\Enums\NavigationGroup;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

/**
 * Attendance stays its own plugin but its menu sits inside Employees (user,
 * 2026-11-13: merging the plugin was judged too costly; the menu move gives
 * the "part of Employees" feel). After Employees' own entries (sort 1-4).
 */
it('lists attendance entries inside the Employees menu, after its own entries', function (string $class, int $sort) {
    expect($class::getNavigationGroup())->toBe(NavigationGroup::Employee)
        ->and($class::getNavigationSort())->toBe($sort);
})->with([
    [AttendanceResource::class, 5],
    [Reporting::class, 6],
    [ManageSettings::class, 7],
]);

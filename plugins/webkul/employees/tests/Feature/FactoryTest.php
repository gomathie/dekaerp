<?php

use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\DepartureReason;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\WorkLocation;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    // The employees plugin is not part of erp:install, so its tables only exist
    // once it is installed. In the Logistics suite they appear as a side effect
    // of logistics:install pulling in its dependencies; here it has to be asked
    // for directly.
    TestBootstrapHelper::ensurePluginInstalled('employees');
});

/**
 * These are guard tests, not coverage.
 *
 * Nothing in this repository could create an Employee through its factory: four
 * separate defects sat in these factories unnoticed because no suite ran them.
 * They were found from the Logistics side, where a test needed an employee to
 * bill a reimbursement to. Each test below fails loudly if one comes back.
 */
it('creates an employee through its factory without recursing', function () {
    // DepartmentFactory used to default manager_id to Employee::factory(), while
    // EmployeeFactory defaults department_id to Department::factory(). Each
    // employee made a department that made an employee, until PHP's stack ran
    // out - this test used to die with "Maximum call stack size reached".
    $employee = Employee::factory()->create();

    expect($employee->exists)->toBeTrue()
        ->and($employee->name)->not->toBeEmpty();
});

it('creates a department without a manager by default', function () {
    $department = Department::factory()->create();

    expect($department->exists)->toBeTrue()
        // Null on purpose: it is what keeps the factories from recursing.
        // Attach a manager explicitly where a test needs one.
        ->and($department->manager_id)->toBeNull();
});

it('creates a work location that can be read back', function () {
    // location_type is cast to the WorkLocation enum, and the factory used to
    // put a random word in it, so hydrating the record threw
    // "not a valid backing value for enum".
    $location = WorkLocation::factory()->create();

    expect($location->fresh()->location_type)->toBeInstanceOf(
        Webkul\Employee\Enums\WorkLocation::class,
    );
});

it('creates a departure reason with the columns the table actually has', function () {
    // The factory used to insert `sequence`, which does not exist - the column
    // is `sort` - and a word into the integer reason_code.
    $reason = DepartureReason::factory()->create();

    expect($reason->exists)->toBeTrue()
        ->and($reason->fresh()->name)->not->toBeEmpty();
});

it('creates an employee with a department, a job and a work location', function () {
    // The whole default graph at once: this is what Employee::factory() builds
    // when nothing is overridden, and it is the combination that was unusable.
    $employee = Employee::factory()->create();

    expect($employee->fresh()->exists)->toBeTrue();
});

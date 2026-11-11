<?php

use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Policies\AttendancePolicy;
use Webkul\Employee\Models\Department;

require_once __DIR__.'/../Helpers/AttendanceHelper.php';

beforeEach(function (): void {
    AttendanceHelper::install();
});

/**
 * AT-4 manager views (AT-D4): a department manager sees their own and their
 * team's attendance; anyone else without the all-attendance permission sees
 * only their own; and someone with neither a team nor a permission cannot open
 * the list. The list query and the policy share visibleEmployeeIds(), so the
 * screen and a direct record URL agree.
 */
it('shows a manager their team and themselves, and nobody else', function (): void {
    $company = AttendanceHelper::enable(AttendanceHelper::company());
    $managerUser = CompanyHelper::actingAsCompanyUser($company, []);

    $manager = AttendanceHelper::employee($company, ['user_id' => $managerUser->id, 'department_id' => null]);
    $team = Department::factory()->create(['company_id' => $company->id, 'manager_id' => $manager->id]);
    $otherDepartment = Department::factory()->create(['company_id' => $company->id]);

    $member = AttendanceHelper::employee($company, ['user_id' => null, 'department_id' => $team->id]);
    $outsider = AttendanceHelper::employee($company, ['user_id' => null, 'department_id' => $otherDepartment->id]);

    $own = Attendance::factory()->create(['employee_id' => $manager->id]);
    $teamRow = Attendance::factory()->create(['employee_id' => $member->id]);
    $outsiderRow = Attendance::factory()->create(['employee_id' => $outsider->id]);

    $visible = AttendanceResource::getEloquentQuery()->pluck('id')->all();

    expect($visible)->toContain($own->id, $teamRow->id)
        ->not->toContain($outsiderRow->id)
        ->and(app(AttendancePolicy::class)->viewAny($managerUser))->toBeTrue()
        ->and(app(AttendancePolicy::class)->view($managerUser, $teamRow))->toBeTrue()
        ->and(app(AttendancePolicy::class)->view($managerUser, $outsiderRow))->toBeFalse();
});

it('shows an employee without a team only their own rows, and refuses the list to someone with neither', function (): void {
    $company = AttendanceHelper::enable(AttendanceHelper::company());
    $worker = CompanyHelper::actingAsCompanyUser($company, []);

    $self = AttendanceHelper::employee($company, ['user_id' => $worker->id, 'department_id' => null]);
    $colleague = AttendanceHelper::employee($company, ['user_id' => null, 'department_id' => null]);

    $ownRow = Attendance::factory()->create(['employee_id' => $self->id]);
    $colleagueRow = Attendance::factory()->create(['employee_id' => $colleague->id]);

    expect(AttendanceResource::getEloquentQuery()->pluck('id')->all())->toBe([$ownRow->id])
        ->and(app(AttendancePolicy::class)->view($worker, $colleagueRow))->toBeFalse()
        // No team and no permission: the list itself is not offered.
        ->and(app(AttendancePolicy::class)->viewAny($worker))->toBeFalse();
});

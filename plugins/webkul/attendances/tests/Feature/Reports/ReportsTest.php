<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Webkul\Attendance\Filament\Clusters\Reporting\Pages\Absence;
use Webkul\Attendance\Filament\Clusters\Reporting\Pages\Lateness;
use Webkul\Attendance\Filament\Clusters\Reporting\Pages\Overtime;
use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\CompanyProvisioner;

require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

beforeEach(function (): void {
    AttendanceHelper::install();
});

it('generates the page permission each attendance report asks for', function (): void {
    // Every other report test grants the permission by hand, so they would all
    // still pass if Shield generated a different name than the page checks for.
    // In production that is a report nobody can open, however many boxes their
    // role has ticked.
    FilamentHelper::bootAdminPanel();

    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)
        ->toContain('page_attendance_lateness')
        ->toContain('page_attendance_overtime')
        ->toContain('page_attendance_absence')
        // A cluster is excluded on purpose: it guards itself with the company switch.
        ->not->toContain('page_attendance_reporting');
});

it('hides a report from a user without its page permission', function (): void {
    $company = AttendanceHelper::enable(AttendanceHelper::company());

    CompanyHelper::actingAsCompanyUser($company);

    expect(Lateness::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_attendance_lateness']);

    expect(Lateness::canAccess())->toBeTrue()
        ->and(Overtime::canAccess())->toBeFalse();
});

it('hides every report from a company that has not enabled attendance', function (): void {
    $company = AttendanceHelper::company();

    CompanyHelper::actingAsCompanyUser($company, [
        'page_attendance_lateness',
        'page_attendance_overtime',
        'page_attendance_absence',
    ]);

    expect(Lateness::canAccess())->toBeFalse()
        ->and(Overtime::canAccess())->toBeFalse()
        ->and(Absence::canAccess())->toBeFalse();
});

it('lists only the viewer own rows without the view-any permission', function (): void {
    $company = AttendanceHelper::company();

    $user = CompanyHelper::actingAsCompanyUser($company);
    $mine = AttendanceHelper::employee($company, ['user_id' => $user->id]);
    $colleague = AttendanceHelper::employee($company);

    $own = Attendance::factory()->create([
        'company_id'  => $company->id,
        'employee_id' => $mine->id,
    ]);

    $other = Attendance::factory()->create([
        'company_id'  => $company->id,
        'employee_id' => $colleague->id,
    ]);

    $ids = AttendanceResource::getEloquentQuery()->pluck('id')->all();

    expect($ids)->toContain($own->id)
        ->and($ids)->not->toContain($other->id);
});

it('lists every attendance in the company to a view-any user', function (): void {
    $company = AttendanceHelper::company();

    CompanyHelper::actingAsCompanyUser($company, ['view_any_attendance_attendance']);

    $first = Attendance::factory()->create([
        'company_id'  => $company->id,
        'employee_id' => AttendanceHelper::employee($company)->id,
    ]);

    $second = Attendance::factory()->create([
        'company_id'  => $company->id,
        'employee_id' => AttendanceHelper::employee($company)->id,
    ]);

    $ids = AttendanceResource::getEloquentQuery()->pluck('id')->all();

    expect($ids)->toContain($first->id)->toContain($second->id);
});

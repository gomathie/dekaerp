<?php

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Livewire\Livewire;
use Webkul\Attendance\Filament\Pages\ManageSettings;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Attendance\Support\AttendanceAccess;

require_once __DIR__.'/../Helpers/AttendanceHelper.php';

beforeEach(function (): void {
    AttendanceHelper::install();
    FilamentHelper::bootAdminPanel();
});

/**
 * 2026-11-12 (user: Attendance installed but not in the sidebar). The
 * per-company switch had no screen, so no company could turn Attendance on.
 * The settings page is reachable while Attendance is off, needs its own
 * generated page permission, and switches the company on and off.
 */
it('generates the page permission the settings page checks', function (): void {
    $generated = collect(FilamentShield::getPages())
        ->flatMap(fn (array $page): array => array_keys($page['permissions']))
        ->all();

    expect($generated)->toContain('page_attendance_manage_settings');
});

it('opens only with its permission, even while attendance is off', function (): void {
    $company = AttendanceHelper::company();

    CompanyHelper::actingAsCompanyUser($company, []);
    expect(ManageSettings::canAccess())->toBeFalse();

    CompanyHelper::actingAsCompanyUser($company, ['page_attendance_manage_settings']);
    expect(ManageSettings::canAccess())->toBeTrue()
        ->and(AttendanceAccess::enabledFor($company->id))->toBeFalse();
});

it('switches attendance on and off for the current company and saves its rules', function (): void {
    $company = AttendanceHelper::company();
    CompanyHelper::actingAsCompanyUser($company, ['page_attendance_manage_settings']);

    Livewire::test(ManageSettings::class)
        ->assertOk()
        ->callAction('enable');

    AttendanceAccess::flush();
    expect(AttendanceAccess::enabledFor($company->id))->toBeTrue();

    Livewire::test(ManageSettings::class)
        ->fillForm([
            'auto_checkout_after_hours'  => 10,
            'capture_location'           => 'optional',
            'late_tolerance_minutes'     => 5,
            'overtime_tolerance_minutes' => 15,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $setting = CompanySetting::forCompany($company->id);

    expect($setting->auto_checkout_after_hours)->toBe(10)
        ->and($setting->late_tolerance_minutes)->toBe(5)
        ->and($setting->overtime_tolerance_minutes)->toBe(15);

    Livewire::test(ManageSettings::class)->callAction('disable');

    AttendanceAccess::flush();
    expect(AttendanceAccess::enabledFor($company->id))->toBeFalse();
});

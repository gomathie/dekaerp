<?php

use Livewire\Livewire;
use Webkul\Attendance\Filament\AttendanceBannerContribution;
use Webkul\Attendance\Livewire\ClockButton;
use Webkul\Attendance\Models\Attendance;
use Webkul\Support\Filament\Contributions\DashboardBannerRegistry;

require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

beforeEach(function (): void {
    AttendanceHelper::install();

    DashboardBannerRegistry::flush();
    AttendanceBannerContribution::register();
});

afterEach(function (): void {
    DashboardBannerRegistry::flush();
});

it('greets the signed-in person by first name with their company', function (): void {
    $company = AttendanceHelper::company(['name' => 'TRACE365']);
    $user = CompanyHelper::actingAsCompanyUser($company);

    $user->forceFill(['name' => 'Ama Mensah'])->saveQuietly();
    auth()->guard('web')->setUser($user->refresh());

    $html = view('support::dashboard-banner')->render();

    expect($html)->toContain('Ama')
        ->toContain('TRACE365');
});

it('renders a contributed banner section once per key', function (): void {
    DashboardBannerRegistry::section('demo', fn (): string => '<span>ONCE</span>');
    DashboardBannerRegistry::section('demo', fn (): string => '<span>ONCE</span>');

    expect((string) DashboardBannerRegistry::renderedSections())->toBe('<span>ONCE</span>');
});

it('shows the attendance control only when attendance is enabled and the person has an employee record', function (): void {
    $company = AttendanceHelper::company();
    $user = CompanyHelper::actingAsCompanyUser($company);

    // No employee record yet, and the company has not switched attendance on.
    expect(AttendanceBannerContribution::appliesToViewer())->toBeFalse();

    AttendanceHelper::employee($company, ['user_id' => $user->id]);
    AttendanceHelper::enable($company);

    expect(AttendanceBannerContribution::appliesToViewer())->toBeTrue();
});

it('clocks the signed-in employee in and out from the banner control', function (): void {
    $company = AttendanceHelper::company();
    $user = CompanyHelper::actingAsCompanyUser($company);

    AttendanceHelper::employee($company, ['user_id' => $user->id]);
    AttendanceHelper::enable($company);

    Livewire::test(ClockButton::class)
        ->assertOk()
        ->call('clockIn');

    expect(Attendance::withoutGlobalScopes()->whereNull('check_out_at')->count())->toBe(1);

    Livewire::test(ClockButton::class)
        ->assertOk()
        ->call('clockOut');

    expect(Attendance::withoutGlobalScopes()->whereNotNull('check_out_at')->count())->toBe(1);
});

it('shows no clock control to a person with no employee record in the active company', function (): void {
    $company = AttendanceHelper::company();
    CompanyHelper::actingAsCompanyUser($company);
    AttendanceHelper::enable($company);

    Livewire::test(ClockButton::class)
        ->assertOk()
        ->assertDontSee(__('attendances::attendance.actions.clock-in'));
});

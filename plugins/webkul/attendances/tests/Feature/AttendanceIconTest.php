<?php

use Webkul\Attendance\Filament\Resources\AttendanceResource;

require_once __DIR__.'/../Helpers/AttendanceHelper.php';

beforeEach(function (): void {
    AttendanceHelper::install();
});

/**
 * The provider declared ->icon('attendance') but resources/svg had no
 * attendance.svg, so the module showed no icon (user, 2026-11-12). The
 * attendance list uses the same outline icon.
 */
it('has its own outline icon, used by the module and its list', function (): void {
    expect(base_path('resources/svg/attendance.svg'))->toBeFile()
        ->and((string) svg('icon-attendance')->toHtml())->toContain('<svg')
        ->and(AttendanceResource::getNavigationIcon())->toBe('icon-attendance');
});

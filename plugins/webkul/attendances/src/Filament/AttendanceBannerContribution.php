<?php

namespace Webkul\Attendance\Filament;

use Illuminate\Support\Facades\Blade;
use Webkul\Attendance\Livewire\ClockButton;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Filament\Contributions\DashboardBannerRegistry;
use Webkul\Support\Services\CompanyContext;

/**
 * Attendance's part of the dashboard welcome banner.
 *
 * `support` owns the banner and must not reference this plugin, so the clock
 * control and the work location are contributed through the banner registry.
 * The key is stable, which makes a repeated `packageBooted()` a replacement
 * rather than a second copy.
 */
class AttendanceBannerContribution
{
    public const SECTION = 'attendances.clock';

    public static function register(): void
    {
        DashboardBannerRegistry::section(static::SECTION, function (): string {
            if (! static::appliesToViewer()) {
                return '';
            }

            return Blade::render("@livewire('attendance-clock')");
        });

        DashboardBannerRegistry::location(fn (): ?string => static::workLocation());
    }

    /**
     * The control is drawn only where it can actually be used: attendance
     * switched on for the company being worked in and an employee record there.
     */
    protected static function appliesToViewer(): bool
    {
        $user = filament()->auth()->user();
        $companyId = app(CompanyContext::class)->currentId();

        if (! $user || ! $companyId || ! AttendanceAccess::enabledFor($companyId)) {
            return false;
        }

        return Employee::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('company_id', $companyId)
            ->exists();
    }

    protected static function workLocation(): ?string
    {
        $user = filament()->auth()->user();
        $companyId = app(CompanyContext::class)->currentId();

        if (! $user || ! $companyId) {
            return null;
        }

        $employee = Employee::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->where('company_id', $companyId)
            ->first();

        // Shown to the person themselves, never to a manager.
        return $employee?->workLocation?->name;
    }
}

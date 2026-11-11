<?php

namespace Webkul\Attendance\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Attendance\Models\CompanySetting;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Scopes\CompanyScope;

/**
 * Turns attendance on or off for one company. Safe to run again.
 */
class CompanyProvisioner
{
    public function provision(Company $company): CompanySetting
    {
        return DB::transaction(function () use ($company): CompanySetting {
            $setting = CompanySetting::withoutGlobalScope(CompanyScope::class)
                ->firstOrNew(['company_id' => $company->id]);

            $setting->save();

            AttendanceAccess::flush();

            return $setting;
        });
    }

    public function enable(Company $company): CompanySetting
    {
        $setting = $this->provision($company);

        if (! $setting->is_enabled) {
            $setting->forceFill([
                'is_enabled' => true,
                'enabled_at' => now(),
                'enabled_by_id' => Auth::id(),
                'disabled_at' => null,
            ])->save();
        }

        AttendanceAccess::flush();

        return $setting->refresh();
    }

    public function disable(Company $company): CompanySetting
    {
        $setting = $this->provision($company);

        if ($setting->is_enabled) {
            $setting->forceFill([
                'is_enabled' => false,
                'disabled_at' => now(),
            ])->save();
        }

        AttendanceAccess::flush();

        return $setting->refresh();
    }
}

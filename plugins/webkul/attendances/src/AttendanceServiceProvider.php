<?php

namespace Webkul\Attendance;

use Filament\Panel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Policies\AttendancePolicy;
use Webkul\Attendance\Services\CompanyProvisioner;
use Webkul\Attendance\Support\AttendanceAccess;
use Webkul\Attendance\Support\UninstallGuard;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Support\Models\Company;

class AttendanceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'attendances';

    public static string $viewNamespace = 'attendances';

    protected static bool $companyProvisioningRegistered = false;

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasConfigFile('attendances')
            ->hasMigrations([
                '2026_11_11_000001_create_attendances_company_settings_table',
                '2026_11_11_000002_create_attendances_attendances_table',
                '2026_11_11_000003_create_attendances_corrections_table',
            ])
            ->runsMigrations()
            ->hasDependencies(['employees'])
            ->hasSeeder('Webkul\\Attendance\\Database\\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {
                $command
                    ->startWith(function (): void {
                        UninstallGuard::ensureSafe();
                    })
                    ->endWith(function (): void {
                        AttendanceAccess::flush();
                    });
            })
            ->icon('attendance');
    }

    public function packageBooted(): void
    {
        Gate::policy(Attendance::class, AttendancePolicy::class);

        if (static::$companyProvisioningRegistered) {
            return;
        }

        static::$companyProvisioningRegistered = true;

        Company::created(function (Company $company): void {
            // Provision only after the global install; the default-off row is
            // infrastructure and does not grant attendance to the new company.
            if (! Package::isPluginInstalled(static::$name) || ! Schema::hasTable('attendances_company_settings')) {
                return;
            }

            app(CompanyProvisioner::class)->provision($company);
        });
    }

    public function packageRegistered(): void
    {
        $this->app->scoped(AttendanceAccess::class);

        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(AttendancePlugin::make());
        });

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command('attendances:auto-check-out')
                ->everyFifteenMinutes()
                ->withoutOverlapping()
                ->when(fn (): bool => Package::isPluginInstalled(static::$name));
        });
    }
}

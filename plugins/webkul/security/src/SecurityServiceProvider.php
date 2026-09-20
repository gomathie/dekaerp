<?php

namespace Webkul\Security;

use Filament\Panel;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Gate;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Security\Facades\Bouncer as BouncerFacade;
use Webkul\Security\Models\User;
use Webkul\Security\Services\MultiCompanyAdminService;

class SecurityServiceProvider extends PackageServiceProvider
{
    public static string $name = 'security';

    public static string $viewNamespace = 'security';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('web')
            ->hasRoute('api')
            ->runsMigrations()
            ->hasMigrations([
                '2024_11_11_112529_create_user_invitations_table',
                '2024_11_12_125715_create_teams_table',
                '2024_11_12_130019_create_user_team_table',
                '2024_12_10_101127_add_default_company_id_column_to_users_table',
                '2024_12_13_130906_add_partner_id_to_users_table',
                '2025_08_01_071239_alter_teams_table',
                '2025_08_01_073954_alter_users_table',
                '2025_08_21_082229_alter_roles_table',
                '2025_08_21_101646_alter_users_table',
                '2026_01_23_074142_add_multi_factor_auth_columns_in_users_table',
                '2026_09_18_000001_add_administration_context_to_user_invitations_table',
                '2026_09_18_000002_provision_multi_company_admin_role',
                '2026_09_20_150533_repair_multi_company_admin_install_state',
            ])
            ->hasSettings([
                '2024_11_05_042358_create_user_settings',
                '2025_07_29_064223_create_currency_settings',
            ])
            ->runsSettings();
    }

    public function packageBooted(): void
    {
        require_once __DIR__.'/Helpers/helpers.php';

        // A Multi-Company Admin (DEKA staff administering tenants) is granted every
        // ability except the ones MultiCompanyAdminService denies, so a staff admin
        // is created by assigning one role and picking companies, with no permission
        // ticking. Two limits keep that from becoming a super admin:
        //
        //  - Only bare permission checks are granted here. A check that carries a
        //    model or class ($arguments) falls through to its policy, so the
        //    per-company containment in UserPolicy, CompanyPolicy and the
        //    Logistics policies still decides those.
        //  - Row visibility is unchanged: bypass_company_scope is denied, so the
        //    company scope still limits every query to their assigned tenants.
        Gate::before(function ($user, string $ability, array $arguments = []) {
            // is_active is checked here too: this callback runs before
            // User::hasPermissionTo(), which is where a deactivated user is
            // normally stopped, so granting without it would re-admit them.
            if (! $user instanceof User || ! $user->is_active || ! $user->isMultiCompanyAdmin()) {
                return null;
            }

            if (app(MultiCompanyAdminService::class)->deniesAbility($ability)) {
                return false;
            }

            return $arguments === [] ? true : null;
        });

        Gate::before(function ($user, string $ability) {
            if ($ability !== 'bypass_ownership_scope') {
                return null;
            }

            if ($user && method_exists($user, 'hasRole') && $user->hasRole(array_filter([
                config('filament-shield.super_admin.name'),
                'super_admin',
            ]))) {
                return true;
            }

            return null;
        });
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(SecurityPlugin::make());
        });

        $loader = AliasLoader::getInstance();

        $loader->alias('bouncer', BouncerFacade::class);

        $this->app->singleton('bouncer', Bouncer::class);
        $this->app->singleton(PermissionRegistrar::class);
    }
}

<?php

namespace Webkul\PluginManager;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\Facades\Event;
use Webkul\PluginManager\Console\Commands\FindMissingTranslations;
use Webkul\PluginManager\Console\Commands\InstallERP;
use Webkul\PluginManager\Http\Middleware\EnsurePluginIsEntitled;
use Webkul\PluginManager\Services\CompanyPluginProvisioner;
use Webkul\PluginManager\Support\PluginAccess;
use Webkul\Support\Models\Company;

class PluginManagerServiceProvider extends PackageServiceProvider
{
    public static string $name = 'plugin-manager';

    public static string $viewNamespace = 'plugin-manager';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasViews()
            ->hasTranslations()
            ->hasConfigFile('plugin-manager')
            ->hasMigrations([
                '2024_11_05_105102_create_plugins_table',
                '2026_10_22_000001_create_company_plugin_table',
            ])
            ->hasSeeder('Webkul\\PluginManager\\Database\\Seeders\\PluginSeeder')
            ->runsMigrations()
            ->runsSeeders()
            ->hasCommands([
                InstallERP::class,
                FindMissingTranslations::class,
            ]);
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();

        $this->app->make(PermissionManager::class)->managePermissions();

        Event::listen('aureus.installed', 'Webkul\PluginManager\Listeners\Installer@installed');

        // Adds the per-company module grants to the company form. Contributed
        // rather than edited into support's CompanyResource, so the base plugin
        // never has to reference this one.
        CompanyContributions::register();

        // A new company starts with the plugins every existing company has, so
        // introducing per-company availability changes nothing by itself and
        // removing access stays the deliberate act. On the model event rather
        // than the create page so seeders, factories and console commands
        // behave the same as the UI.
        Company::created(function (Company $company): void {
            app(CompanyPluginProvisioner::class)->grantInstalledPlugins($company);
        });
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(PluginManagerPlugin::make());
        });

        $this->app->singleton(PermissionManager::class, fn () => new PermissionManager);

        // Applied as `plugin.entitlement:<plugin>` to every plugin's API route
        // file - see PackageServiceProvider. Aliased rather than referenced by
        // class so a plugin's route file stays readable and the loader stays
        // decoupled.
        $this->app['router']->aliasMiddleware('plugin.entitlement', EnsurePluginIsEntitled::class);

        // Scoped, not singleton: the grants are read once per request or queued
        // job and must not outlive it, or a job would act on another request's
        // view of who may use what.
        $this->app->scoped(PluginAccess::class);
    }

    public function registerCustomCss()
    {
        FilamentAsset::register([
            Css::make('plugins', __DIR__.'/../resources/dist/plugin.css'),
        ], 'plugins');
    }
}

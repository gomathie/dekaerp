<?php

namespace Webkul\Website;

use Filament\Panel;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Website\Http\Responses\LogoutResponse;

class WebsiteServiceProvider extends PackageServiceProvider
{
    public static string $name = 'website';

    public static string $viewNamespace = 'website';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            // Both remaining migrations are what makes the portal possible, not
            // website content: one adds password, remember_token,
            // email_verified_at and is_active to partners_partners so a partner
            // can authenticate at all, the other records last_login_at.
            // create_website_pages_table went with the CMS on 2026-10-22, and
            // the table itself is dropped by
            // database/migrations/2026_10_21_100000_drop_website_and_blogs_tables.php.
            ->hasMigrations([
                '2025_03_10_064655_alter_partners_partners_table',
                '2026_08_03_100000_add_last_login_at_to_partners_partners_table',
            ])
            ->runsMigrations()
            // No seeder and no settings any more: the seeder only created
            // website pages, and the settings held the public site's contact
            // details and social links.
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('website');
    }

    public function packageBooted(): void
    {
        $this->registerCustomCss();

        if (! Package::isPluginInstalled(self::$name)) {
            Route::get('/', function () {
                return redirect()->route('filament.admin.auth.login');
            });

            return;
        }

        PortalContributions::register();

        Event::listen(Login::class, function (Login $event): void {
            if ($event->guard !== 'customer') {
                return;
            }

            $event->user->forceFill(['last_login_at' => now()])->saveQuietly();
        });
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(WebsitePlugin::make());
        });

        $this->app->bind(\Filament\Auth\Http\Responses\Contracts\LogoutResponse::class, LogoutResponse::class);
    }

    public function registerCustomCss()
    {
        FilamentAsset::register([
            Css::make('website', __DIR__.'/../resources/dist/website.css'),
        ], 'website');
    }
}

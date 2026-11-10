<?php

namespace Webkul\PlatformBilling;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PlatformBillingPlugin implements Plugin
{
    public function getId(): string
    {
        return 'platform-billing';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        // Core plugins are not rows in `plugins` (Plugin::getAllPluginPackages
        // skips them), so isPluginInstalled() is always false here and would
        // leave the billing routes unregistered. Do not read the current
        // company either: a cached route list would then belong to whichever
        // tenant happened to be active.
        $panel->when($panel->getId() === 'admin', function (Panel $panel): void {
            $panel->discoverClusters(
                in: __DIR__.'/Filament/Clusters',
                for: 'Webkul\\PlatformBilling\\Filament\\Clusters',
            );
        });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}

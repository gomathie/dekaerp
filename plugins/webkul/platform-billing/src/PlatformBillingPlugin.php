<?php

namespace Webkul\PlatformBilling;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Webkul\PluginManager\Package;

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
        // Installed is global. Do not read the current company here: route
        // caching would freeze one tenant's answer for every company.
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

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

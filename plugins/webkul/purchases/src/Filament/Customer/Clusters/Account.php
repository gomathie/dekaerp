<?php

namespace Webkul\Purchase\Filament\Customer\Clusters;

use Filament\Clusters\Cluster;
use Filament\Facades\Filament;

/**
 * Customer "Account" cluster. Relocated here from the (removed) website plugin
 * because the purchase customer-portal resources are grouped under it.
 */
class Account extends Cluster
{
    protected static ?int $navigationSort = 1000;

    public static function getNavigationLabel(): string
    {
        return __('Account');
    }

    public static function canAccessClusteredComponents(): bool
    {
        return Filament::auth()->check();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
}

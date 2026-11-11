<?php

namespace Webkul\PlatformBilling\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;

class Billing extends Cluster
{
    protected static ?string $slug = 'billing';

    protected static ?int $navigationSort = 90;

    /**
     * Platform billing is not a company module. A module check would open
     * this cluster to every user of an entitled company. Trial days and the
     * commercial switch stay on the company form, where an assigned
     * Multi-Company Admin is allowed. Plans and subscriptions are Super Admin.
     */
    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->is_active && $user->isSuperAdmin();
    }

    public static function getNavigationLabel(): string
    {
        return __('platform-billing::filament/billing.cluster');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }
}

<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource\Pages\Concerns;

use Webkul\Purchase\Support\PurchasesAccess;

/**
 * For the Bills and Purchases tabs shown on every contact screen (unified
 * contacts, phase 3; user 2026-11-11), not only on the Purchases vendor.
 *
 * Two guards, both needed. The company must have Purchases switched on, and
 * the user must be allowed to list what the tab shows: a user who may open a
 * contact but not Purchases would otherwise read that contact's purchase
 * orders or bills through the tab. The permission is the list screen's own
 * canViewAny(), so no second rule exists.
 */
trait ShownOnContactScreens
{
    public static function canAccess(array $parameters = []): bool
    {
        return PurchasesAccess::enabledForCurrent()
            && static::listResourceAllowsViewing()
            && parent::canAccess($parameters);
    }

    abstract protected static function listResourceAllowsViewing(): bool;
}

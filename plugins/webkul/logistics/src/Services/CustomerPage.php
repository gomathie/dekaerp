<?php

namespace Webkul\Logistics\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\ShipmentCharge;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Scopes\CompanyScope;

/**
 * What the customer page may show about Logistics.
 *
 * The partner page is another plugin's screen. CompanyScope follows whoever
 * is looking, so a shared contact (no company of its own) would otherwise
 * list every selected company's shipments. This query drops only that scope
 * and then names the one company the rows must belong to.
 */
class CustomerPage
{
    public static function visible(Partner $partner): bool
    {
        $user = auth()->user();

        if (! $user instanceof User || ! $user->can('view_any_logistics_shipment')) {
            return false;
        }

        $companyId = static::companyId($partner);

        return $companyId !== null && LogisticsAccess::enabledFor($companyId);
    }

    /**
     * Charge amounts are money. The same permission hides the margin widget
     * and the profitability report; the customer page follows it.
     */
    public static function showsCharges(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->can('view_financials_logistics_shipment');
    }

    /**
     * @return Collection<int, Shipment>
     */
    public static function recentShipments(Partner $partner): Collection
    {
        if (! static::visible($partner)) {
            return new Collection;
        }

        $companyId = static::companyId($partner);

        if ($companyId === null) {
            return new Collection;
        }

        return static::shipments($partner, $companyId)
            ->latest('id')
            ->limit(8)
            ->get();
    }

    /**
     * @return Collection<int, ShipmentCharge>
     */
    public static function openCharges(Partner $partner): Collection
    {
        if (! static::visible($partner) || ! static::showsCharges()) {
            return new Collection;
        }

        $companyId = static::companyId($partner);

        if ($companyId === null) {
            return new Collection;
        }

        return ShipmentCharge::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $companyId)
            ->whereIn('shipment_id', static::shipments($partner, $companyId)->select('id'))
            ->uninvoiced()
            ->with([
                'currency:id,symbol',
                'shipment' => fn ($query) => $query->withoutGlobalScope(CompanyScope::class)->select('id', 'name'),
            ])
            ->latest('id')
            ->limit(8)
            ->get();
    }

    public static function shipmentUrl(Shipment $shipment): string
    {
        return ShipmentResource::getUrl('view', ['record' => $shipment]);
    }

    /**
     * A partner that belongs to a company shows that company's jobs.
     * A shared contact belongs to nobody, so the only safe set is the
     * company the viewer is working in. No current company means none.
     */
    public static function companyId(Partner $partner): ?int
    {
        if ($partner->company_id !== null) {
            return (int) $partner->company_id;
        }

        return current_company_id();
    }

    /**
     * @return Builder<Shipment>
     */
    protected static function shipments(Partner $partner, int $companyId): Builder
    {
        return Shipment::withoutGlobalScope(CompanyScope::class)
            ->where('customer_id', $partner->getKey())
            ->where('company_id', $companyId);
    }
}

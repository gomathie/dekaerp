<?php

namespace Webkul\Inventory\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\DeliveryResource;
use Webkul\Inventory\Filament\Clusters\Operations\Resources\ReceiptResource;
use Webkul\Inventory\Filament\Pages\Overview;
use Webkul\Inventory\Models\Operation;
use Webkul\Inventory\Models\OrderPoint;
use Webkul\Inventory\Models\ProductQuantity;

/**
 * The Inventory figures on the combined Overview's "At a glance" row (user,
 * 2026-11-11: key inventory information belongs on the overview too).
 *
 * Receipts and deliveries still to be done, each with how many are late. These
 * are today's open work, not a period's totals, so the Overview's date and
 * product filters do not apply. "Open" and "late" use the same states and
 * deadline rule as the Inventory overview cards (OperationTypeCardWidget), so
 * the numbers agree with that page.
 *
 * Visible exactly when the Inventory overview page is (module switch plus its
 * Shield page permission). Not discovered on its own.
 */
class InventoryHeadlineWidget extends StatsOverviewWidget
{
    // No 5-second refresh (Filament default): every open dashboard re-ran all
    // its queries 12 times a minute (Sentry DEKAERP-11). Filters reload it.
    protected ?string $pollingInterval = null;

    protected static bool $isLazy = false;

    protected static bool $isDiscovered = false;

    public static function canView(): bool
    {
        return Overview::canAccess();
    }

    /**
     * This section's figures for the Overview's "At a glance" row. Filters are
     * accepted for the shared contract and deliberately unused (open work is
     * as of today).
     *
     * @param  array<string, mixed>  $filters
     * @return array<int, Stat>
     */
    public function headlineStats(array $filters): array
    {
        return $this->getStats();
    }

    protected function getStats(): array
    {
        $receipts = $this->open(OperationTypeEnum::INCOMING);
        $deliveries = $this->open(OperationTypeEnum::OUTGOING);

        $lateReceipts = $this->late(clone $receipts);
        $lateDeliveries = $this->late(clone $deliveries);

        return [
            Stat::make(__('inventories::filament/widgets/inventory-headline.to-receive'), number_format((clone $receipts)->count()))
                ->url(ReceiptResource::canViewAny() ? ReceiptResource::getUrl('index') : null)
                ->icon('heroicon-o-inbox-arrow-down')
                ->description(trans_choice('inventories::filament/widgets/inventory-headline.late', $lateReceipts, ['count' => $lateReceipts]))
                ->color($lateReceipts > 0 ? 'warning' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('inventories::filament/widgets/inventory-headline.to-deliver'), number_format((clone $deliveries)->count()))
                ->url(DeliveryResource::canViewAny() ? DeliveryResource::getUrl('index') : null)
                ->icon('heroicon-o-truck')
                ->description(trans_choice('inventories::filament/widgets/inventory-headline.late', $lateDeliveries, ['count' => $lateDeliveries]))
                ->color($lateDeliveries > 0 ? 'danger' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('inventories::filament/widgets/inventory-headline.low-stock'), number_format($lowStock = $this->lowStockCount()))
                ->icon('heroicon-o-exclamation-triangle')
                ->description(__('inventories::filament/widgets/inventory-headline.low-stock-hint'))
                ->color($lowStock > 0 ? 'warning' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('inventories::filament/widgets/inventory-headline.stock-value'), compact_money($this->stockValue(), current_company()?->currency?->name))
                ->icon('heroicon-o-cube')
                ->description(__('inventories::filament/widgets/inventory-headline.stock-value-hint'))
                ->color('primary')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
    }

    /**
     * Reorder rules whose product is at or below the rule's minimum (user,
     * 2026-11-11, recommendation accepted). On hand is the product's quantity
     * across the company's internal locations, the same stock the rule
     * replenishes. Rules with no minimum are ignored. Company-scoped through
     * OrderPoint; the stock subquery is pinned to the rule's own company.
     */
    protected function lowStockCount(): int
    {
        $onHand = ProductQuantity::query()
            ->withoutGlobalScopes()
            ->selectRaw('COALESCE(SUM(inventories_product_quantities.quantity), 0)')
            ->join('inventories_locations', 'inventories_locations.id', '=', 'inventories_product_quantities.location_id')
            ->where('inventories_locations.type', LocationType::INTERNAL)
            ->whereColumn('inventories_product_quantities.product_id', 'inventories_order_points.product_id')
            ->whereColumn('inventories_product_quantities.company_id', 'inventories_order_points.company_id');

        return OrderPoint::query()
            ->where('product_min_qty', '>', 0)
            ->whereRaw('('.$onHand->toSql().') <= inventories_order_points.product_min_qty', $onHand->getBindings())
            ->distinct()
            ->count('product_id');
    }

    /**
     * On-hand quantity in internal locations times each product's cost price,
     * in company currency (user, 2026-11-11, recommendation accepted: cost
     * price, not a valuation layer, which this ERP does not keep yet). The card
     * says so in its description. Company-scoped through ProductQuantity.
     */
    protected function stockValue(): float
    {
        return (float) (ProductQuantity::query()
            ->join('inventories_locations', 'inventories_locations.id', '=', 'inventories_product_quantities.location_id')
            ->join('products_products', 'products_products.id', '=', 'inventories_product_quantities.product_id')
            ->where('inventories_locations.type', LocationType::INTERNAL)
            ->selectRaw('SUM(inventories_product_quantities.quantity * COALESCE(products_products.cost, 0)) as stock_value')
            ->value('stock_value') ?? 0);
    }

    /** Not done and not cancelled, of one operation type (company-scoped). */
    protected function open(OperationTypeEnum $type): Builder
    {
        return Operation::query()
            ->whereNotIn('state', [OperationState::DONE, OperationState::CANCELED])
            ->whereHas('operationType', fn (Builder $query) => $query->where('type', $type));
    }

    /** Same rule as OperationTypeCardWidget::getLateCount(). */
    protected function late(Builder $query): int
    {
        return $query
            ->whereIn('state', [OperationState::ASSIGNED, OperationState::WAITING, OperationState::CONFIRMED])
            ->where(function (Builder $query): void {
                $query->where('has_deadline_issue', true)
                    ->orWhereDate('deadline', '<', today())
                    ->orWhereDate('scheduled_at', '<', today());
            })
            ->count();
    }
}

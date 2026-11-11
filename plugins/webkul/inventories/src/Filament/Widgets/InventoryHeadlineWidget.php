<?php

namespace Webkul\Inventory\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Inventory\Enums\OperationState;
use Webkul\Inventory\Enums\OperationType as OperationTypeEnum;
use Webkul\Inventory\Filament\Pages\Overview;
use Webkul\Inventory\Models\Operation;

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
                ->icon('heroicon-o-inbox-arrow-down')
                ->description(trans_choice('inventories::filament/widgets/inventory-headline.late', $lateReceipts, ['count' => $lateReceipts]))
                ->color($lateReceipts > 0 ? 'warning' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),

            Stat::make(__('inventories::filament/widgets/inventory-headline.to-deliver'), number_format((clone $deliveries)->count()))
                ->icon('heroicon-o-truck')
                ->description(trans_choice('inventories::filament/widgets/inventory-headline.late', $lateDeliveries, ['count' => $lateDeliveries]))
                ->color($lateDeliveries > 0 ? 'danger' : 'success')
                ->extraAttributes(['class' => 'overview-headline-stat']),
        ];
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

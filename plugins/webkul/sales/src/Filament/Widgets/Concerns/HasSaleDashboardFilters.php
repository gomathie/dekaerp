<?php

namespace Webkul\Sale\Filament\Widgets\Concerns;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;

trait HasSaleDashboardFilters
{
    use InteractsWithPageFilters;

    /**
     * The page's filters, also read under the combined Overview's names.
     * The Overview shares one product, category and date filter across
     * sections, using the Invoices and Purchases names (product_id,
     * category_id, start_date, end_date); without this the Sales widgets
     * would ignore them there (2026-11-11).
     *
     * @return array<string, mixed>
     */
    protected function saleDashboardFilters(): array
    {
        $filters = $this->pageFilters ?? [];

        $filters['products'] = $filters['products'] ?? $filters['product_id'] ?? null;
        $filters['categories'] = $filters['categories'] ?? $filters['category_id'] ?? null;
        $filters['startDate'] = $filters['startDate'] ?? $filters['start_date'] ?? null;
        $filters['endDate'] = $filters['endDate'] ?? $filters['end_date'] ?? null;

        return $filters;
    }

    protected function applyFilters(Builder $query, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $filters = $this->saleDashboardFilters();

        [$defaultStart, $defaultEnd] = $this->periodRange();

        $query->whereDate($query->qualifyColumn('date_order'), '>=', $start ?? $defaultStart);
        $query->whereDate($query->qualifyColumn('date_order'), '<=', $end ?? $defaultEnd);

        if (! empty($filters['countries'])) {
            $query->whereHas('partner', fn (Builder $q) => $q->whereIn('country_id', $filters['countries']));
        }

        if (! empty($filters['products'])) {
            $query->whereHas('lines', fn (Builder $q) => $q->whereIn('product_id', $filters['products']));
        }

        if (! empty($filters['categories'])) {
            $query->whereHas('lines.product', fn (Builder $q) => $q->whereIn('category_id', $filters['categories']));
        }

        if (! empty($filters['teams'])) {
            $query->whereIn($query->qualifyColumn('team_id'), $filters['teams']);
        }

        if (! empty($filters['salesPersons'])) {
            $query->whereIn($query->qualifyColumn('user_id'), $filters['salesPersons']);
        }

        return $query;
    }

    protected function applyConfirmedScope(Builder $query, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->applyFilters($query, $start, $end)
            ->where($query->qualifyColumn('state'), OrderState::SALE->value);
    }

    protected function saleOrders(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return $this->applyConfirmedScope(Order::query(), $start, $end);
    }

    protected function quotations(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = Order::query();

        return $this->applyFilters($query, $start, $end)
            ->whereIn($query->qualifyColumn('state'), [OrderState::DRAFT->value, OrderState::SENT->value]);
    }

    protected function confirmedLines(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        return OrderLine::query()
            ->whereHas('order', fn (Builder $query) => $this->applyConfirmedScope($query, $start, $end));
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function periodRange(): array
    {
        $filters = $this->saleDashboardFilters();

        $start = ! empty($filters['startDate'])
            ? Carbon::parse($filters['startDate'])->startOfDay()
            : now()->startOfYear();

        $end = ! empty($filters['endDate'])
            ? Carbon::parse($filters['endDate'])->endOfDay()
            : now()->endOfYear();

        return [$start, $end];
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function previousPeriodRange(): array
    {
        [$start, $end] = $this->periodRange();

        $length = $start->diffInDays($end) + 1;

        return [(clone $start)->subDays($length), (clone $end)->subDays($length)];
    }
}

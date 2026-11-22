<?php

namespace Webkul\Invoice\Filament\Widgets\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Invoice\Models\Invoice;
use Webkul\Support\Filament\Concerns\HasDashboardMetrics;

trait HasInvoiceDashboardFilters
{
    use HasDashboardMetrics;

    protected function dashboardDateColumn(): string
    {
        return 'invoice_date';
    }

    protected function dashboardValueColumn(): string
    {
        return 'amount_total_signed';
    }

    protected function applyFilters(Builder $query, string $partnerFilter, ?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $filters = $this->dashboardFilters();

        [$defaultStart, $defaultEnd] = $this->periodRange();

        $query->whereBetween($query->qualifyColumn('invoice_date'), [$start ?? $defaultStart, $end ?? $defaultEnd]);

        if (! empty($filters['salesperson_id'])) {
            $query->whereIn($query->qualifyColumn('invoice_user_id'), (array) $filters['salesperson_id']);
        }

        if (! empty($filters['product_id'])) {
            $query->whereHas('lines', fn (Builder $q) => $q
                ->where('display_type', 'product')
                ->whereIn('product_id', (array) $filters['product_id']));
        }

        if (! empty($filters['category_id'])) {
            $query->whereHas('lines.product', fn (Builder $q) => $q->whereIn('category_id', (array) $filters['category_id']));
        }

        if (! empty($filters[$partnerFilter])) {
            $query->whereIn($query->qualifyColumn('partner_id'), (array) $filters[$partnerFilter]);
        }

        if (! empty($filters['payment_state'])) {
            $query->whereIn($query->qualifyColumn('payment_state'), (array) $filters['payment_state']);
        }

        return $query;
    }

    protected function customerInvoices(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = Invoice::query();

        return $this->applyFilters($query, 'customer_id', $start, $end)
            ->where($query->qualifyColumn('move_type'), MoveType::OUT_INVOICE)
            ->where($query->qualifyColumn('state'), MoveState::POSTED);
    }

    protected function vendorBills(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = Invoice::query();

        return $this->applyFilters($query, 'vendor_id', $start, $end)
            ->where($query->qualifyColumn('move_type'), MoveType::IN_INVOICE)
            ->where($query->qualifyColumn('state'), MoveState::POSTED);
    }

    /**
     * Invoices and credit notes, for money totals (dashboard plan DB-2 and
     * DB-8, 2026-11-22: revenue is invoices less credit notes). A credit
     * note's signed amount is negative, so summing both nets it out; counts
     * keep using customerInvoices().
     */
    protected function customerInvoicesNet(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = Invoice::query();

        return $this->applyFilters($query, 'customer_id', $start, $end)
            ->whereIn($query->qualifyColumn('move_type'), [MoveType::OUT_INVOICE, MoveType::OUT_REFUND])
            ->where($query->qualifyColumn('state'), MoveState::POSTED);
    }

    /** Bills and vendor refunds, for money totals (DB-2), like customerInvoicesNet(). */
    protected function vendorBillsNet(?Carbon $start = null, ?Carbon $end = null): Builder
    {
        $query = Invoice::query();

        return $this->applyFilters($query, 'vendor_id', $start, $end)
            ->whereIn($query->qualifyColumn('move_type'), [MoveType::IN_INVOICE, MoveType::IN_REFUND])
            ->where($query->qualifyColumn('state'), MoveState::POSTED);
    }

    /**
     * Not paid or only partly paid (DB-2, 2026-11-22). "Unpaid" used to count
     * not_paid alone, so a half-paid invoice was in neither paid nor unpaid.
     */
    protected function unpaidOnly(Builder $query): Builder
    {
        return $query->whereIn($query->qualifyColumn('payment_state'), ['not_paid', 'partial']);
    }
}

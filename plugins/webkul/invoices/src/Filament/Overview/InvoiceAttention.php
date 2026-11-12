<?php

namespace Webkul\Invoice\Filament\Overview;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\InvoiceResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\BillResource;
use Webkul\Invoice\Models\Invoice;

/**
 * What needs someone today in Invoices (user, 2026-11-13: "Needs attention"
 * on the Overview and a one-line summary in the greeting, after finance
 * dashboards on Dribbble). Today's state, not the dashboard's date filter:
 * an invoice overdue since last quarter still needs chasing.
 */
class InvoiceAttention
{
    /**
     * @return list<array{label: string, count: int, url: ?string, tone: string}>
     */
    public static function items(): array
    {
        $today = now()->toDateString();

        $overdueInvoices = static::open(MoveType::OUT_INVOICE)
            ->whereDate('invoice_date_due', '<', $today)
            ->count();

        $overdueBills = static::open(MoveType::IN_INVOICE)
            ->whereDate('invoice_date_due', '<', $today)
            ->count();

        $billsDueSoon = static::open(MoveType::IN_INVOICE)
            ->whereDate('invoice_date_due', '>=', $today)
            ->whereDate('invoice_date_due', '<=', now()->addDays(7)->toDateString())
            ->count();

        $invoiceUrl = InvoiceResource::canViewAny() ? InvoiceResource::getUrl('index') : null;
        $billUrl = BillResource::canViewAny() ? BillResource::getUrl('index') : null;

        return [
            [
                'label' => trans_choice('invoices::filament/pages/overview.attention.overdue-invoices', $overdueInvoices, ['count' => $overdueInvoices]),
                'count' => $overdueInvoices,
                'url'   => $invoiceUrl,
                'tone'  => 'warning',
            ],
            [
                'label' => trans_choice('invoices::filament/pages/overview.attention.overdue-bills', $overdueBills, ['count' => $overdueBills]),
                'count' => $overdueBills,
                'url'   => $billUrl,
                'tone'  => 'danger',
            ],
            [
                'label' => trans_choice('invoices::filament/pages/overview.attention.bills-due-soon', $billsDueSoon, ['count' => $billsDueSoon]),
                'count' => $billsDueSoon,
                'url'   => $billUrl,
                'tone'  => 'info',
            ],
        ];
    }

    /**
     * @return list<array{label: string, url: string, icon: string}>
     */
    public static function actions(): array
    {
        $actions = [];

        if (InvoiceResource::canCreate()) {
            $actions[] = [
                'label' => __('invoices::filament/pages/overview.actions.new-invoice'),
                'url'   => InvoiceResource::getUrl('create'),
                'icon'  => 'heroicon-m-document-plus',
            ];
        }

        if (BillResource::canCreate()) {
            $actions[] = [
                'label' => __('invoices::filament/pages/overview.actions.new-bill'),
                'url'   => BillResource::getUrl('create'),
                'icon'  => 'heroicon-m-receipt-percent',
            ];
        }

        return $actions;
    }

    /** Posted and not fully paid; company scope applies through the model. */
    protected static function open(MoveType $type): Builder
    {
        return Invoice::query()
            ->where('move_type', $type)
            ->where('state', MoveState::POSTED)
            ->whereIn('payment_state', ['not_paid', 'partial']);
    }
}

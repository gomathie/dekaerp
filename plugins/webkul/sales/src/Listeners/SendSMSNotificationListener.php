<?php

namespace Webkul\Sale\Listeners;

use Illuminate\Support\Collection;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Events\MovePaid;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Package;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Services\Msg91Service;

class SendSMSNotificationListener
{
    public function __construct(private Msg91Service $sms) {}

    public function handle(MovePaid $event): void
    {
        if (! Package::isPluginInstalled('sales')) {
            return;
        }

        // Payment events may be dispatched by a worker with no company session;
        // the ordinary scoped relation otherwise makes the customer's order
        // look absent and silently skips the notification.
        $order = Order::withoutGlobalScopes()
            ->whereHas('accountMoves', fn ($q) => $q->withoutGlobalScopes()->where('accounts_account_moves.id', $event->move->id))
            ->first();

        if (! $order) {
            return;
        }

        $invoices = $order->accountMoves()
            ->withoutGlobalScopes()
            ->get()
            ->where('state', 'posted');

        [$totalInvoiced, $totalRemaining, $currency] = $this->totals($invoices, $order);
        $totalPaid = $totalInvoiced - $totalRemaining;
        $customer = Partner::withoutGlobalScopes()->find($order->partner_id);
        $customerName = $customer?->name ?? 'Customer';

        $customerMessage = "Dear {$customerName}, we have received your payment for order {$order->name}. "
            .'Amount paid: '.money($totalPaid, $currency).'. '
            .($totalRemaining > 0
                ? 'Outstanding balance: '.money($totalRemaining, $currency).'. Please clear the remaining amount at your earliest convenience.'
                : 'Your account is fully settled. Thank you!')
            .' - '.config('app.name');

        $adminMessage = "Payment received | Order: {$order->name} | Customer: {$customerName} | "
            .'Invoiced: '.money($totalInvoiced, $currency).' | Paid: '.money($totalPaid, $currency).' | Remaining: '.money($totalRemaining, $currency);

        $customerMobile = $customer?->mobile ?? $customer?->phone;

        if ($customerMobile) {
            $this->sms->send($customerMobile, $customerMessage);
        }

        $adminMobile = config('services.msg91.admin_mobile');

        if ($adminMobile) {
            $this->sms->send($adminMobile, $adminMessage);
        }
    }

    /**
     * Invoiced and remaining amounts in one money (dashboard plan DB-7,
     * 2026-11-23). This summed each invoice's own-currency amount and
     * labelled the result with the order's currency, so a USD invoice and a
     * GHS invoice on one order were added as one figure, and a credit note
     * counted as more invoiced instead of less.
     *
     * One currency on every document: their own amounts, a credit note
     * deducted. More than one: the company-currency amounts each document
     * stored at its own rate (`_signed`, already negative for a credit note).
     *
     * @return array{0: float, 1: float, 2: ?string}
     */
    protected function totals(Collection $invoices, Order $order): array
    {
        if ($invoices->pluck('currency_id')->unique()->count() <= 1) {
            $sign = fn (Move $move): int => $move->move_type === MoveType::OUT_REFUND ? -1 : 1;

            return [
                (float) $invoices->sum(fn (Move $move): float => $sign($move) * (float) $move->amount_total),
                (float) $invoices->sum(fn (Move $move): float => $sign($move) * (float) $move->amount_residual),
                $invoices->first()?->currency?->name ?? $order->currency?->name ?? config('app.currency'),
            ];
        }

        return [
            (float) $invoices->sum('amount_total_signed'),
            (float) $invoices->sum('amount_residual_signed'),
            $order->company?->currency?->name ?? config('app.currency'),
        ];
    }
}

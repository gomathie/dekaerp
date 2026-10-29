<?php

namespace Webkul\Sale\Listeners;

use Webkul\Account\Events\MovePaid;
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

        $totalInvoiced = $invoices->sum('amount_total');
        $totalRemaining = $invoices->sum('amount_residual');
        $totalPaid = $totalInvoiced - $totalRemaining;

        $currency = $order->currency?->code ?? config('app.currency');
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
}

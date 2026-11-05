<?php

namespace Webkul\Purchase\Livewire;

use Filament\Pages\SimplePage;
use Webkul\Purchase\Models\Order;
use Webkul\Purchase\Support\PurchasesAccess;

class RespondQuotation extends SimplePage
{
    protected string $view = 'purchases::livewire.respond-quotation';

    public int $order;

    public string $action;

    public function mount(): void
    {
        $order = Order::findOrFail($this->order);

        // A signed link outlives the module: it was emailed while Purchases was
        // on, and its signature proves only that we issued it. Revoking
        // Purchases for the order's company (SAAS-05B) must stop it writing to
        // the order, so it answers like any link that no longer works - 404,
        // as a revoked invitation does - before anything is updated.
        abort_unless(
            PurchasesAccess::enabledForCompany($order->company_id === null ? null : (int) $order->company_id),
            404,
        );

        if ($this->action === 'accept') {
            $order->update([
                'mail_reception_confirmed' => true,
            ]);

            $order->addMessage([
                'causer_type' => $order->partner->getMorphClass(),
                'causer_id'   => $order->partner->id,
                'body'        => 'The RFQ has been acknowledged by vendor.',
                'type'        => 'comment',
            ]);
        } else {
            $order->update([
                'mail_reception_declined' => true,
            ]);

            $order->addMessage([
                'causer_type' => $order->partner->getMorphClass(),
                'causer_id'   => $order->partner->id,
                'body'        => 'The RFQ has been declined by vendor.',
                'type'        => 'comment',
            ]);
        }
    }

    public function getHeading(): string
    {
        if ($this->action === 'accept') {
            return 'Quotation Accepted';
        }

        return 'Quotation Declined';
    }
}

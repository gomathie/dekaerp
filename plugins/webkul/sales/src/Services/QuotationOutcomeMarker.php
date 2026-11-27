<?php

namespace Webkul\Sale\Services;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Enums\QuotationOutcome;
use Webkul\Sale\Models\Order;
use Webkul\Security\Models\User;

/**
 * Marks a quotation won or lost without changing its state, so sales
 * figures (which count confirmed orders) stay as they are.
 */
class QuotationOutcomeMarker
{
    public function markWon(Order $order, ?User $actor = null): Order
    {
        $this->authorize($order, $actor);

        // Accepted means confirmed. A draft is not won.
        if ($order->state !== OrderState::SALE) {
            throw ValidationException::withMessages([
                'outcome' => __('sales::enums/quotation-outcome.won-only-accepted'),
            ]);
        }

        $order->forceFill(['outcome' => QuotationOutcome::Won])->save();

        return $order;
    }

    public function markLost(Order $order, ?User $actor = null): Order
    {
        $this->authorize($order, $actor);

        $declined = $order->state === OrderState::CANCEL;
        $expired = in_array($order->state, [OrderState::DRAFT, OrderState::SENT], true)
            && $order->validity_date !== null
            && $order->validity_date->lt(today());

        // Declined (cancelled) or past its validity date, still open.
        if (! $declined && ! $expired) {
            throw ValidationException::withMessages([
                'outcome' => __('sales::enums/quotation-outcome.lost-only-declined'),
            ]);
        }

        $order->forceFill(['outcome' => QuotationOutcome::Lost])->save();

        return $order;
    }

    protected function authorize(Order $order, ?User $actor): void
    {
        $actor ??= auth()->user();

        // The button is not the only caller, so the company permission is checked again here.
        if (! $actor instanceof User || ! $actor->hasPermissionInCompany('update_sale_order', (int) $order->company_id)) {
            throw new AuthorizationException;
        }
    }
}

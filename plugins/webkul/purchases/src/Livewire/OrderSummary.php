<?php

namespace Webkul\Purchase\Livewire;

use Livewire\Component;
use Webkul\Support\Models\Currency;
use Webkul\Support\Support\CompanyCurrencyEquivalent;

class OrderSummary extends Component
{
    public $subtotal = 0;

    public $totalDiscount = 0;

    public $totalTax = 0;

    public $grandTotal = 0;

    public $amountTax = 0;

    public $currency = null;

    public $record = null;

    protected $listeners = ['itemUpdated' => 'refreshSummary'];

    public function refreshSummary($totals)
    {
        $this->subtotal = $totals['subtotal'];
        $this->totalTax = $totals['totalTax'];
        $this->grandTotal = $totals['grandTotal'];
        $this->amountTax = $totals['totalTax'];

        // Without this the summary keeps whatever currency it was initialised
        // with, so an order in a non-default currency showed converted totals
        // against the wrong symbol.
        if (array_key_exists('currency_id', $totals) && $totals['currency_id']) {
            $this->currency = Currency::find($totals['currency_id']);
        }
    }

    public function render()
    {
        // The company-currency value under a foreign-currency total (TRACE365,
        // 2026-11-23: purchases stay in USD with the cedi value beside them).
        // A saved order in the same currency keeps its own rate.
        $sameCurrency = $this->record && (int) $this->record->currency_id === (int) ($this->currency?->id ?? 0);

        return view('purchases::livewire/order-summary', [
            'companyEquivalent' => CompanyCurrencyEquivalent::line(
                (float) $this->grandTotal,
                $this->currency,
                $this->record?->company ?? current_company(),
                $this->record?->ordered_at,
                $sameCurrency ? (float) $this->record->currency_rate : null,
            ),
        ]);
    }
}

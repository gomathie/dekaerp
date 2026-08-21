@php
    $record  = $this->getRecord();
    $invoice = $this->getInvoice();
    $currency = $record->currency;
    $symbol  = $currency?->symbol ?? '';
    $fmt = fn ($amount) => $symbol . number_format((float) $amount, 2);
@endphp

<x-filament-panels::page>
    <style>
        .ds-receipt {
            width: 80mm;
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            color: #000;
            font-family: 'Courier New', ui-monospace, monospace;
            font-size: 12px;
            line-height: 1.45;
            padding: 12px 14px;
            border: 1px dashed #cbd5e1;
        }
        .ds-receipt h1 { font-size: 16px; font-weight: 700; text-align: center; margin: 0 0 2px; }
        .ds-receipt .ds-muted { text-align: center; font-size: 11px; }
        .ds-receipt hr { border: none; border-top: 1px dashed #000; margin: 8px 0; }
        .ds-receipt table { width: 100%; border-collapse: collapse; }
        .ds-receipt th, .ds-receipt td { text-align: left; padding: 1px 0; vertical-align: top; }
        .ds-receipt td.num, .ds-receipt th.num { text-align: right; white-space: nowrap; }
        .ds-receipt .ds-row { display: flex; justify-content: space-between; }
        .ds-receipt .ds-total { font-weight: 700; font-size: 14px; }
        .ds-receipt .ds-center { text-align: center; }

        @media print {
            /* Hide the entire admin chrome and show only the receipt. */
            body * { visibility: hidden !important; }
            .ds-print-area, .ds-print-area * { visibility: visible !important; }
            .ds-print-area { position: absolute; left: 0; top: 0; width: 100%; }
            .ds-no-print { display: none !important; }
            .ds-receipt { border: none; width: 80mm; }
        }
    </style>

    <div class="ds-no-print" style="margin-bottom: 1rem; display:flex; gap:.5rem;">
        <x-filament::button icon="heroicon-o-printer" onclick="window.print()">
            {{ __('Print Receipt') }}
        </x-filament::button>

        <x-filament::button color="gray" tag="a" :href="\Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource::getUrl('index')">
            {{ __('Back to Direct Sales') }}
        </x-filament::button>
    </div>

    <div class="ds-print-area"
        @if ($this->shouldAutoPrint())
            x-data x-init="$nextTick(() => window.print())"
        @endif
    >
        <div class="ds-receipt">
            <h1>{{ $record->company?->name ?? config('app.name') }}</h1>
            @if ($record->company?->email)
                <div class="ds-muted">{{ $record->company->email }}</div>
            @endif
            <div class="ds-muted">{{ __('Sales Receipt') }}</div>

            <hr>

            <div class="ds-row"><span>{{ __('Receipt') }}:</span><span>{{ $invoice?->name ?? $record->name }}</span></div>
            <div class="ds-row"><span>{{ __('Order') }}:</span><span>{{ $record->name }}</span></div>
            <div class="ds-row"><span>{{ __('Date') }}:</span><span>{{ ($record->date_order ?? now())->format('Y-m-d H:i') }}</span></div>
            <div class="ds-row"><span>{{ __('Cashier') }}:</span><span>{{ $record->user?->name ?? '—' }}</span></div>
            <div class="ds-row"><span>{{ __('Customer') }}:</span><span>{{ $record->partner?->name ?? __('Walk-In Customer') }}</span></div>

            <hr>

            <table>
                <thead>
                    <tr>
                        <th>{{ __('Item') }}</th>
                        <th class="num">{{ __('Qty') }}</th>
                        <th class="num">{{ __('Price') }}</th>
                        <th class="num">{{ __('Total') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($record->lines as $line)
                        <tr>
                            <td colspan="4">{{ $line->name ?? $line->product?->name }}</td>
                        </tr>
                        <tr>
                            <td></td>
                            <td class="num">{{ rtrim(rtrim(number_format((float) $line->product_qty, 2), '0'), '.') }}</td>
                            <td class="num">{{ $fmt($line->price_unit) }}</td>
                            <td class="num">{{ $fmt($line->price_subtotal) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <hr>

            <div class="ds-row"><span>{{ __('Subtotal') }}</span><span>{{ $fmt($record->amount_untaxed) }}</span></div>
            <div class="ds-row"><span>{{ __('Tax') }}</span><span>{{ $fmt($record->amount_tax) }}</span></div>
            <div class="ds-row ds-total"><span>{{ __('TOTAL') }}</span><span>{{ $fmt($record->amount_total) }}</span></div>

            <hr>

            <div class="ds-row"><span>{{ __('Paid') }}</span><span>{{ $fmt($this->getPaidAmount()) }}</span></div>
            <div class="ds-row"><span>{{ __('Balance Due') }}</span><span>{{ $fmt($invoice?->amount_residual ?? 0) }}</span></div>
            @if ($invoice)
                <div class="ds-row"><span>{{ __('Payment Status') }}</span><span>{{ ucfirst(str_replace('_', ' ', (string) ($invoice->payment_state?->value ?? $invoice->payment_state))) }}</span></div>
            @endif

            <hr>

            <div class="ds-center ds-muted">{{ __('Thank you for your purchase!') }}</div>
        </div>
    </div>
</x-filament-panels::page>

<?php

namespace Webkul\Account\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Livewire\Component;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Filament\Resources\RefundResource;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\PartialReconcile;
use Webkul\Support\Models\Currency;
use Webkul\Support\Support\CompanyCurrencyEquivalent;

class InvoiceSummary extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $record = null;

    public $subtotal = 0;

    public $totalDiscount = 0;

    public $totalTax = 0;

    public $grandTotal = 0;

    public $amountTax = 0;

    public $rounding = 0;

    public $currency = null;

    public $reconcilablePayments = null;

    public $reconciledPayments = null;

    protected $listeners = [
        'itemUpdated'           => 'refreshSummary',
        'refreshInvoiceSummary' => 'refreshFromRecord',
    ];

    public function refreshSummary($totals)
    {
        $this->subtotal = $totals['subtotal'];
        $this->totalTax = $totals['totalTax'];
        $this->grandTotal = $totals['grandTotal'];
        $this->amountTax = $totals['totalTax'];
        $this->rounding = $totals['rounding'];

        // Follow the invoice's currency when it changes (2026-11-19).
        if (! empty($totals['currencyId']) && (int) $totals['currencyId'] !== (int) ($this->currency?->id ?? 0)) {
            $this->currency = Currency::find($totals['currencyId']);
        }
    }

    public function refreshFromRecord()
    {
        $this->record?->refresh();
    }

    public function reconcileAction(): Action
    {
        return Action::make('reconcile')
            ->label(__('accounts::filament/resources/invoice.summary.actions.reconcile.label'))
            ->icon('heroicon-o-check-circle')
            ->size('xs')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $lines = MoveLine::where('id', $arguments['lineId'])->get();

                $lines = $lines->merge($this->record->lines->filter(fn ($line) => $line->account_id == $lines->first()->account_id && ! $line->reconciled
                ));

                AccountFacade::reconcile($lines);
            })
            ->after(fn () => $this->js('window.location.reload()'));
    }

    public function unReconcileAction(): Action
    {
        return Action::make('unReconcile')
            ->label(__('accounts::filament/resources/invoice.summary.actions.unreconcile.label'))
            ->icon('heroicon-o-x-circle')
            ->size('xs')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $partialReconcile = PartialReconcile::find($arguments['partial_id']);

                AccountFacade::unReconcile($partialReconcile);
            })
            ->after(fn () => $this->js('window.location.reload()'));
    }

    public function getResourceUrl($record): ?string
    {
        return match ($record['move_type']) {
            MoveType::OUT_INVOICE => InvoiceResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::IN_INVOICE  => BillResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::OUT_REFUND  => CreditNoteResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::IN_REFUND   => RefundResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::ENTRY       => PaymentResource::getUrl('view', ['record' => $record['account_payment_id']]),
        };
    }

    public function render()
    {
        $this->reconcilablePayments = $this->record?->getReconcilablePayments();

        $this->reconciledPayments = $this->record?->getReconciledPayments();

        // The company-currency value under a foreign-currency total (TRACE365,
        // 2026-11-23). A saved document in the same currency keeps its own rate.
        $sameCurrency = $this->record && (int) $this->record->currency_id === (int) ($this->currency?->id ?? 0);

        return view('accounts::livewire/invoice-summary', [
            'companyEquivalent' => CompanyCurrencyEquivalent::line(
                (float) $this->grandTotal,
                $this->currency,
                $this->record?->company ?? current_company(),
                $this->record?->invoice_date,
                $sameCurrency ? (float) $this->record->invoice_currency_rate : null,
            ),
        ]);
    }
}

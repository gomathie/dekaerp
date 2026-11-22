<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListInvoices extends ListRecords
{
    use HasTableViews;

    protected static string $resource = InvoiceResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'draft' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.draft'))
                ->favorite()
                ->icon('heroicon-s-stop')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', MoveState::DRAFT)),
            'posted' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.posted'))
                ->favorite()
                ->icon('heroicon-s-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', MoveState::POSTED)),
            'cancelled' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.cancelled'))
                ->favorite()
                ->icon('heroicon-s-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', MoveState::CANCEL)),
            'not_secured' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.not-secured'))
                ->favorite()
                ->icon('heroicon-s-shield-exclamation')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('inalterable_hash')),
            'to_check' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.to-check'))
                ->icon('heroicon-s-check-badge')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->whereNot('state', MoveState::DRAFT)
                        ->where('checked', false);
                }),
            'to_pay' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.to-pay'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('state', MoveState::POSTED)
                        ->whereIn('payment_state', [
                            PaymentState::NOT_PAID,
                            PaymentState::PARTIAL,
                        ]);
                }),
            'unpaid' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.unpaid'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('state', MoveState::POSTED)
                        ->where('amount_residual', '>', 0)
                        ->whereNotIn('payment_state', [
                            PaymentState::PAID,
                            PaymentState::IN_PAYMENT,
                        ]);
                }),
            'in_payment' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.in-payment'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('state', MoveState::POSTED)
                        ->where('payment_state', PaymentState::IN_PAYMENT);
                }),
            'overdue' => PresetView::make(__('accounts::filament/resources/invoice/pages/list-invoice.tabs.overdue'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(function (Builder $query) {
                    $query->where('state', MoveState::POSTED)
                        ->where('payment_state', PaymentState::NOT_PAID)
                        ->where('invoice_date_due', '<', today());
                }),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            // "New direct sale" (user, 2026-11-22): the same invoice form,
            // opened with the walk-in customer, "Paid now" and (with Sales)
            // the order and delivery boxes already set.
            Action::make('newDirectSale')
                ->label(__('accounts::filament/resources/invoice/pages/create-invoice.direct-sale.button'))
                ->icon('heroicon-o-banknotes')
                ->color('gray')
                ->visible(fn (): bool => static::offersDirectSale() && static::getResource()::canCreate())
                ->url(fn (): string => static::getResource()::getUrl('create', ['direct_sale' => 1])),
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    /**
     * Customer invoices only: credit notes and refunds reuse this list, and a
     * direct sale is never one of those.
     */
    protected static function offersDirectSale(): bool
    {
        $create = static::getResource()::getPages()['create'] ?? null;

        return $create !== null && is_a($create->getPage(), CreateInvoice::class, true);
    }
}

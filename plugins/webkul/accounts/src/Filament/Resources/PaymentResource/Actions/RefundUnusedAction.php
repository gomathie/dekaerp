<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Payment;
use Webkul\Account\Services\QuickPayment;

/**
 * Pay a customer's unused amount back through a chosen way (U100).
 */
class RefundUnusedAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'refundUnused';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/payment/actions/refund-unused.title'))
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('gray')
            ->modalHeading(__('accounts::filament/resources/payment/actions/refund-unused.title'))
            ->modalDescription(__('accounts::filament/resources/payment/actions/refund-unused.description'))
            ->modalSubmitActionLabel(__('accounts::filament/resources/payment/actions/refund-unused.title'))
            ->visible(fn (Payment $record): bool => $record->payment_type === PaymentType::RECEIVE
                && ($record->unusedAmount() ?? 0) > 0
                && QuickPayment::mayRefundUnused($record))
            ->schema([
                Placeholder::make('unused')
                    ->label(__('accounts::filament/resources/payment/actions/refund-unused.amount'))
                    ->content(function (): string {
                        $payment = $this->getRecord();

                        if (! $payment instanceof Payment) {
                            return '';
                        }

                        return (string) money($payment->unusedAmount() ?? 0, $payment->currency?->name);
                    }),
                Select::make('journal_id')
                    ->label(__('accounts::filament/resources/payment/actions/refund-unused.journal-field'))
                    ->options(function (): array {
                        $payment = $this->getRecord();

                        if (! $payment instanceof Payment) {
                            return [];
                        }

                        return Journal::withoutGlobalScopes()
                            ->where('company_id', $payment->company_id)
                            ->whereIn('id', QuickPayment::receivableJournalIds((int) $payment->company_id))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->default(function (): ?int {
                        $payment = $this->getRecord();

                        if (! $payment instanceof Payment) {
                            return null;
                        }

                        $journals = QuickPayment::receivableJournalIds((int) $payment->company_id);
                        $current = (int) $payment->journal_id;

                        return in_array($current, $journals, true) ? $current : ($journals[0] ?? null);
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('date')
                    ->label(__('accounts::filament/resources/payment/actions/refund-unused.date'))
                    ->native(false)
                    ->default(now())
                    ->required(),
            ])
            ->action(function (Payment $record, array $data): void {
                $refund = QuickPayment::refundUnused($record, (int) $data['journal_id'], (string) $data['date']);

                Notification::make()
                    ->success()
                    ->title(__('accounts::filament/resources/payment/actions/refund-unused.saved'))
                    ->body($refund->name)
                    ->send();
            });
    }
}

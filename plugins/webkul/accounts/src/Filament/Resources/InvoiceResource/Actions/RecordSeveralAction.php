<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Partner;
use Webkul\Account\Services\QuickPayment;

/**
 * One payment for several of a customer's open invoices (U95).
 */
class RecordSeveralAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'recordSeveral';
    }

    /**
     * The open invoices, each starting at its open balance. Shared with
     * the customer payment form so the two screens cannot drift.
     */
    public static function allocationsRepeater(): Repeater
    {
        return Repeater::make('allocations')
            ->label(__('accounts::filament/resources/invoice/actions/record-several.invoices'))
            ->addable(false)
            ->deletable(false)
            ->reorderable(false)
            ->schema([
                Hidden::make('invoice_id'),
                Hidden::make('open'),
                Placeholder::make('name')
                    ->label(__('accounts::filament/resources/invoice/actions/record-several.invoice'))
                    ->content(fn (Get $get): string => (string) $get('name')),
                TextInput::make('amount')
                    ->label(__('accounts::filament/resources/invoice/actions/record-several.amount-field'))
                    ->numeric()
                    ->minValue(0)
                    ->required()
                    ->hintAction(
                        Action::make('payInFull')
                            ->label(__('accounts::filament/resources/invoice/actions/record-several.pay-in-full'))
                            ->action(function (Set $set, Get $get): void {
                                $set('amount', $get('open'));
                            }),
                    ),
            ])
            ->columns(2);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/record-several.title'))
            ->icon('heroicon-o-banknotes')
            ->color('primary')
            ->modalHeading(__('accounts::filament/resources/invoice/actions/record-several.title'))
            ->modalDescription(__('accounts::filament/resources/invoice/actions/record-several.description'))
            ->modalSubmitActionLabel(__('accounts::filament/resources/invoice/actions/record-several.title'))
            ->schema([
                Select::make('partner_id')
                    ->label(__('accounts::filament/resources/invoice/actions/record-several.customer'))
                    ->options(fn (): array => Partner::query()->orderBy('name')->limit(50)->pluck('name', 'id')->all())
                    ->getSearchResultsUsing(fn (string $search): array => Partner::query()
                        ->where('name', 'like', '%'.$search.'%')
                        ->orderBy('name')
                        ->limit(50)
                        ->pluck('name', 'id')
                        ->all())
                    ->getOptionLabelUsing(fn ($value): ?string => Partner::query()->find($value)?->name)
                    ->searchable()
                    ->required()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state): void {
                        $rows = $state ? QuickPayment::allocationRows((int) $state) : [];
                        $set('allocations', $rows);

                        $companyId = $state ? QuickPayment::companyIdForPartner((int) $state) : null;
                        $journals = $companyId ? QuickPayment::receivableJournalIds($companyId) : [];
                        $set('journal_id', $companyId
                            ? (QuickPayment::rememberedJournalId($companyId, $journals, PaymentType::RECEIVE) ?? ($journals[0] ?? null))
                            : null);
                    }),
                Placeholder::make('no_invoices')
                    ->hiddenLabel()
                    ->content(__('accounts::filament/resources/invoice/actions/record-several.none'))
                    ->visible(fn (Get $get): bool => filled($get('partner_id')) && ($get('allocations') ?? []) === []),
                static::allocationsRepeater()
                    ->visible(fn (Get $get): bool => filled($get('allocations'))),
                Select::make('journal_id')
                    ->label(__('accounts::filament/resources/invoice/actions/record-several.journal-field'))
                    ->options(function (Get $get): array {
                        $companyId = $get('partner_id')
                            ? QuickPayment::companyIdForPartner((int) $get('partner_id'))
                            : null;

                        if ($companyId === null) {
                            return [];
                        }

                        return Journal::query()
                            ->whereIn('id', QuickPayment::receivableJournalIds($companyId))
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->required()
                    ->searchable()
                    ->preload(),
                DatePicker::make('date')
                    ->label(__('accounts::filament/resources/invoice/actions/record-several.date'))
                    ->native(false)
                    ->default(now())
                    ->required(),
            ])
            ->action(function (array $data): void {
                $amounts = [];

                foreach ($data['allocations'] ?? [] as $row) {
                    if (! is_array($row) || ! isset($row['invoice_id'])) {
                        continue;
                    }

                    $amounts[(int) $row['invoice_id']] = (float) ($row['amount'] ?? 0);
                }

                $payment = QuickPayment::payCustomerInvoices(
                    (int) $data['partner_id'],
                    $amounts,
                    (int) $data['journal_id'],
                    (string) $data['date'],
                );

                Notification::make()
                    ->success()
                    ->title(__('accounts::filament/resources/invoice/actions/record-several.saved'))
                    ->body($payment->name)
                    ->send();
            });
    }
}

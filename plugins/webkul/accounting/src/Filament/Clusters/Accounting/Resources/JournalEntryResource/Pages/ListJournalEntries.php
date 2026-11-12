<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Services\PeriodLock;
use Webkul\Accounting\Filament\Clusters\Accounting\Resources\JournalEntryResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListJournalEntries extends ListRecords
{
    use HasTableViews;

    protected static string $resource = JournalEntryResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'draft' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/list-journal-entries.tabs.draft'))
                ->favorite()
                ->icon('heroicon-s-stop')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', MoveState::DRAFT)),

            'posted' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/list-journal-entries.tabs.posted'))
                ->favorite()
                ->setAsDefault()
                ->icon('heroicon-s-play')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', MoveState::POSTED)),

            'sales' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/list-journal-entries.tabs.sales'))
                ->favorite()
                ->icon('heroicon-s-receipt-percent')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', function (Builder $query) {
                    $query->where('type', JournalType::SALE);
                })),

            'purchases' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/list-journal-entries.tabs.purchases'))
                ->favorite()
                ->icon('heroicon-s-receipt-refund')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', function (Builder $query) {
                    $query->where('type', JournalType::PURCHASE);
                })),

            'reversed' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/list-journal-entries.tabs.reversed'))
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('payment_state', PaymentState::REVERSED)),

            'bank' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/list-journal-entries.tabs.bank'))
                ->favorite()
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', function (Builder $query) {
                    $query->where('type', JournalType::BANK);
                })),

            'cash' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/list-journal-entries.tabs.cash'))
                ->favorite()
                ->icon('heroicon-s-banknotes')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', function (Builder $query) {
                    $query->where('type', JournalType::CASH);
                })),

            'credit' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/list-journal-entries.tabs.credit'))
                ->favorite()
                ->icon('heroicon-s-credit-card')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', function (Builder $query) {
                    $query->where('type', JournalType::CREDIT_CARD);
                })),

            'misc' => PresetView::make(__('accounting::filament/clusters/accounting/resources/journal-entry/pages/list-journal-entries.tabs.misc'))
                ->favorite()
                ->icon('heroicon-s-ellipsis-horizontal')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('journal', function (Builder $query) {
                    $query->where('type', JournalType::GENERAL);
                })),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            $this->lockDateAction(),
            CreateAction::make()
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    /**
     * Accounting plan #7, A2: set or clear the current company's lock date.
     * Visibility is a convenience; PeriodLock::setLockDate() re-checks who may.
     */
    protected function lockDateAction(): Action
    {
        $lock = app(PeriodLock::class);

        return Action::make('lockDate')
            ->label(__('accounts::account-manager.lock-date.action'))
            ->icon('heroicon-o-lock-closed')
            ->color('gray')
            ->visible(fn (): bool => current_company_id() !== null
                && $lock->mayChange(Auth::user(), (int) current_company_id()))
            ->modalDescription(function () use ($lock): string {
                $date = $lock->lockDateFor(current_company_id());

                return $date
                    ? __('accounts::account-manager.lock-date.current', ['date' => $date->toDateString()])
                    : __('accounts::account-manager.lock-date.none');
            })
            ->fillForm(fn (): array => ['lock_date' => $lock->lockDateFor(current_company_id())?->toDateString()])
            ->schema([
                DatePicker::make('lock_date')
                    ->label(__('accounts::account-manager.lock-date.field'))
                    ->helperText(__('accounts::account-manager.lock-date.helper'))
                    ->maxDate(now())
                    ->native(false),
            ])
            ->action(function (array $data) use ($lock): void {
                $lock->setLockDate((int) current_company_id(), Auth::user(), $data['lock_date'] ?? null);

                Notification::make()
                    ->success()
                    ->title(__('accounts::account-manager.lock-date.saved'))
                    ->send();
            });
    }
}

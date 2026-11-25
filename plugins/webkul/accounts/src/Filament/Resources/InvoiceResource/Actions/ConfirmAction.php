<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Throwable;
use Webkul\Account\Enums\AutoPost;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\SaleCurrencyQuestion;

class ConfirmAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.confirm';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/confirm-action.title'))
            ->color('primary')
            // A foreign-currency invoice or bill shows what it will book in
            // the company currency before posting (plan #10 MC-2 step 5).
            // Without a summary (company currency, journal entries) there is
            // no dialog, so those confirm with one click, as before.
            ->requiresConfirmation(fn (Move $record): bool => filled(app(SaleCurrencyQuestion::class)->postingSummary($record)))
            ->modalDescription(fn (Move $record): ?string => app(SaleCurrencyQuestion::class)->postingSummary($record))
            // "Offers today's rate" (MC-2 step 5): a back-dated document can
            // be posted at the latest rate instead, by choice, never silently.
            // A second button rather than a form field: an action with a form
            // always opens a dialog, and company-currency documents must keep
            // confirming with one click.
            ->extraModalFooterActions(fn (Move $record): array => app(SaleCurrencyQuestion::class)->latestRateOffer($record) === null ? [] : [
                Action::make('postAtLatestRate')
                    ->label(__('accounts::filament/resources/invoice/actions/confirm-action.currency-summary.use-latest'))
                    ->color('gray')
                    ->cancelParentActions()
                    ->action(fn (Component $livewire) => $this->post($record, $livewire, atLatestRate: true)),
            ])
            ->action(fn (Move $record, Component $livewire) => $this->post($record, $livewire))
            ->hidden(function (Move $record) {
                return
                    $record->state !== MoveState::DRAFT
                    || (
                        $record->auto_post !== AutoPost::NO
                        && $record->date > now()
                    );
            });
    }

    /**
     * Posts the document, after putting the latest rate on it when that was
     * chosen. One transaction: a failed post also undoes the rate change.
     */
    protected function post(Move $record, Component $livewire, bool $atLatestRate = false): void
    {
        try {
            DB::transaction(function () use (&$record, $atLatestRate): void {
                if ($atLatestRate) {
                    $record = app(SaleCurrencyQuestion::class)->applyLatestRate($record, (int) Auth::id());
                }

                $record->checked = $record->journal->auto_check_on_post;

                $record = AccountFacade::confirmMove($record);
            });

            $livewire->refreshFormData(['state', 'parent_state']);

            $livewire->dispatch('refreshInvoiceSummary');
        } catch (Throwable $e) {
            Notification::make()
                ->warning()
                ->title(__('accounts::filament/resources/invoice/actions/confirm-action.notification.error.title'))
                ->body($e->getMessage())
                ->send();

            $this->halt();
        }
    }
}

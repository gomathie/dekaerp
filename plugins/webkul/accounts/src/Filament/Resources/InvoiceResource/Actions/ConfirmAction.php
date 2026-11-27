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
use Webkul\Account\Services\QuickPayment;
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
            // A customer with unused credit is asked, with Apply as the
            // answer the main button takes (U97). Without either there is
            // no dialog, so company-currency invoices still confirm with
            // one click.
            ->requiresConfirmation(fn (Move $record): bool => filled($this->confirmDescription($record)))
            ->modalDescription(fn (Move $record): ?string => $this->confirmDescription($record))
            ->modalSubmitActionLabel(fn (Move $record): string => QuickPayment::unusedCreditQuestion($record) === null
                ? __('accounts::filament/resources/invoice/actions/confirm-action.title')
                : __('accounts::filament/resources/invoice/actions/confirm-action.unused-credit.apply'))
            // "Offers today's rate" (MC-2 step 5): a back-dated document can
            // be posted at the latest rate instead, by choice, never silently.
            // A second button rather than a form field: an action with a form
            // always opens a dialog, and company-currency documents must keep
            // confirming with one click. That rate button still applies the
            // credit: Apply is the default, and "Don't apply" is the opt-out.
            ->extraModalFooterActions(function (Move $record): array {
                $actions = [];

                if (QuickPayment::unusedCreditQuestion($record) !== null) {
                    $actions[] = Action::make('confirmWithoutCredit')
                        ->label(__('accounts::filament/resources/invoice/actions/confirm-action.unused-credit.dont-apply'))
                        ->color('gray')
                        ->cancelParentActions()
                        ->action(fn (Component $livewire) => $this->post($record, $livewire));
                }

                if (app(SaleCurrencyQuestion::class)->latestRateOffer($record) !== null) {
                    $actions[] = Action::make('postAtLatestRate')
                        ->label(__('accounts::filament/resources/invoice/actions/confirm-action.currency-summary.use-latest'))
                        ->color('gray')
                        ->cancelParentActions()
                        ->action(fn (Component $livewire) => $this->post(
                            $record,
                            $livewire,
                            atLatestRate: true,
                            applyCredit: QuickPayment::unusedCreditQuestion($record) !== null,
                        ));
                }

                return $actions;
            })
            ->action(fn (Move $record, Component $livewire) => $this->post(
                $record,
                $livewire,
                applyCredit: QuickPayment::unusedCreditQuestion($record) !== null,
            ))
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
     * What the confirm dialog says, when it has anything to say.
     */
    protected function confirmDescription(Move $record): ?string
    {
        $parts = array_values(array_filter([
            QuickPayment::unusedCreditQuestion($record),
            app(SaleCurrencyQuestion::class)->postingSummary($record),
        ]));

        return $parts === [] ? null : implode("\n\n", $parts);
    }

    /**
     * Posts the document, after putting the latest rate on it when that was
     * chosen, then applies unused credit when that was the answer. One
     * transaction: a failed post or a failed apply undoes both, so the
     * credit is never taken without the invoice, and the invoice is never
     * left posted when the apply was refused by the books.
     */
    protected function post(Move $record, Component $livewire, bool $atLatestRate = false, bool $applyCredit = false): void
    {
        try {
            DB::transaction(function () use (&$record, $atLatestRate, $applyCredit): void {
                if ($atLatestRate) {
                    $record = app(SaleCurrencyQuestion::class)->applyLatestRate($record, (int) Auth::id());
                }

                $record->checked = $record->journal->auto_check_on_post;

                $record = AccountFacade::confirmMove($record);

                // Apply is the default answer (U97). "Don't apply" calls
                // this with the flag off. The credit lines are chosen by
                // the service, not by the request.
                if ($applyCredit) {
                    QuickPayment::applyUnusedCustomerCredit($record);
                }
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

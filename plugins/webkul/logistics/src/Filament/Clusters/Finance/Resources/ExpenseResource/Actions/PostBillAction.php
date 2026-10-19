<?php

namespace Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ExpenseState;
use Webkul\Logistics\Exceptions\CannotPostBill;
use Webkul\Logistics\Models\Expense;
use Webkul\Logistics\Services\ExpensePoster;

/**
 * Turns this expense, and the shipment's other approved expenses, into draft
 * vendor bills (D2, D3).
 *
 * Posts for the whole shipment rather than the one record, because the point of
 * D2 is one bill per vendor: billing expenses one at a time would produce a
 * separate bill per row for the same carrier, which is what the grouping exists
 * to avoid. When an expense has no shipment, only that expense is posted.
 *
 * The bills are drafts. Nothing reaches the ledger until finance posts them.
 */
class PostBillAction extends Action
{
    protected string $lang = 'logistics::expenses.actions.post-bill';

    public static function getDefaultName(): ?string
    {
        return 'postExpenseBill';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-document-plus')
            ->requiresConfirmation()
            ->modalHeading(__($this->lang.'.heading'))
            ->modalDescription(__($this->lang.'.description'))
            // Approved and not yet billed. The service checks both again, and
            // re-reads under the company scope: an action carries no policy
            // check of its own.
            ->visible(fn (Expense $record): bool => $record->state === ExpenseState::APPROVED
                && $record->bill_move_id === null
                && (Auth::user()?->can('postBill', $record) ?? false))
            ->action(function (Expense $record): void {
                $poster = app(ExpensePoster::class);

                try {
                    $bills = $record->shipment_id && $record->shipment
                        ? $poster->postForShipment($record->shipment)
                        : $poster->postExpenses(collect([$record]));
                } catch (CannotPostBill $e) {
                    // Expected, not exceptional: a missing payee, an employee
                    // with no contact, or a company with no purchase journal.
                    // Each message names the thing to go and fix.
                    Notification::make()
                        ->warning()
                        ->title(__($this->lang.'.cannot-post'))
                        ->body($e->getMessage())
                        ->persistent()
                        ->send();

                    return;
                }

                Notification::make()
                    ->success()
                    ->title(trans_choice($this->lang.'.notification', $bills->count(), [
                        'count' => $bills->count(),
                    ]))
                    ->body(__($this->lang.'.draft-reminder'))
                    ->send();
            });
    }
}

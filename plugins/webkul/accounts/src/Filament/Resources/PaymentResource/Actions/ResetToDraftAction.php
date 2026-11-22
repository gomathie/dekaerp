<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Payment;

class ResetToDraftAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'customers.payment.reset-to-draft';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/payment/actions/reset-to-draft.title'))
            ->color('gray')
            // A2b (user, 2026-11-13): resetting a posted payment's entry asks
            // why; MoveWorkflow refuses a blank reason for a posted move.
            ->schema(fn (Payment $record): array => $record->move?->state === MoveState::POSTED ? [
                Textarea::make('reason')
                    ->label(__('accounts::account-manager.change-reason.field'))
                    ->helperText(__('accounts::account-manager.change-reason.helper'))
                    ->required()
                    ->maxLength(500)
                    ->rows(3),
            ] : [])
            ->action(function (Payment $record, Component $livewire, array $data): void {
                // The entry first: if it refuses (no reason, locked period),
                // the payment must not be left in draft over a posted entry.
                // An entry already in draft is skipped: a reset that failed half
                // way (entry draft, payment still paid) must be finishable
                // here, not refused with "only posted entries can be reset".
                if ($record->move && $record->move->state !== MoveState::DRAFT) {
                    try {
                        // setRelation, not `$record->move =`: assigning a model
                        // to the attribute made save() write a non-existent
                        // "move" column after the entry was already reset,
                        // leaving the payment paid over a draft entry
                        // (Sentry DEKAERP-21, 2026-11-22).
                        $record->setRelation('move', AccountFacade::resetToDraftMove($record->move, $data['reason'] ?? null));
                    } catch (Throwable $e) {
                        Notification::make()->warning()->body($e->getMessage())->send();

                        $this->halt();
                    }
                }

                $record->state = PaymentStatus::DRAFT;
                $record->save();

                $livewire->refreshFormData(['state']);
            })
            ->hidden(fn (Payment $record) => $record->state == PaymentStatus::DRAFT);
    }
}

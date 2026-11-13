<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;

class ResetToDraftAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.reset-to-draft';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/reset-to-draft-action.title'))
            ->color('gray')
            ->icon('heroicon-o-arrow-path')
            // A2b (user, 2026-11-13): a confirmed document asks why, like Zoho
            // Books. MoveWorkflow refuses a blank reason too; this field is
            // only how the screen collects it.
            ->schema(fn (Move $record): array => $record->state === MoveState::POSTED ? [
                Textarea::make('reason')
                    ->label(__('accounts::account-manager.change-reason.field'))
                    ->helperText(__('accounts::account-manager.change-reason.helper'))
                    ->required()
                    ->maxLength(500)
                    ->rows(3),
            ] : [])
            ->action(function (Move $record, Component $livewire, array $data): void {
                if (! $this->validateMove($record)) {
                    return;
                }

                try {
                    $record = AccountFacade::resetToDraftMove($record, $data['reason'] ?? null);

                    $record->save();

                    $livewire->refreshFormData(['state', 'parent_state']);

                    $livewire->dispatch('refreshInvoiceSummary');
                } catch (Throwable $e) {
                    Notification::make()
                        ->warning()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->visible(function (Move $record) {
                return
                    $record->state == MoveState::CANCEL
                    || $record->state == MoveState::POSTED;
            });
    }

    private function validateMove(Move &$record): bool
    {
        if (! in_array($record->state, [MoveState::POSTED, MoveState::CANCEL])) {
            Notification::make()
                ->warning()
                ->title(__('accounts::filament/resources/invoice/actions/reset-to-draft-action.validation.notification.error.invalid-state.title'))
                ->body(__('accounts::filament/resources/invoice/actions/reset-to-draft-action.validation.notification.error.invalid-state.body'))
                ->send();

            return false;
        }

        return true;
    }
}

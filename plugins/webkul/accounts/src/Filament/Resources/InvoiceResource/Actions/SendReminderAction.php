<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Filament\Actions\Action;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\MoveWorkflow;

class SendReminderAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.send-reminder';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/send-reminder.title'))
            ->icon('heroicon-o-bell-alert')
            ->color('gray')
            ->requiresConfirmation()
            ->modalHeading(__('accounts::filament/resources/invoice/actions/send-reminder.heading'))
            ->modalDescription(__('accounts::filament/resources/invoice/actions/send-reminder.description'))
            // The button only shows an overdue customer invoice. The service
            // checks again, because a hidden button is not a guard.
            ->visible(fn (Move $record): bool => app(MoveWorkflow::class)->canSendReminder($record))
            ->action(fn (Move $record) => AccountFacade::sendInvoiceReminder($record));
    }
}

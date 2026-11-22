<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Models\Journal;
use Webkul\Account\Services\QuickPayment;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateInvoice extends CreateRecord
{
    use HandlesCrossCompanyException;

    protected ?bool $hasDatabaseTransactions = true;

    use HasRepeaterColumnManager;

    public function getSubNavigation(): array
    {
        if (filled($cluster = static::getCluster())) {
            return $this->generateNavigationItems($cluster::getClusteredComponents());
        }

        return [];
    }

    protected static string $resource = InvoiceResource::class;

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/resources/invoice/pages/create-invoice.notification.title'))
            ->body(__('accounts::filament/resources/invoice/pages/create-invoice.notification.body'));
    }

    public function mount(): void
    {
        parent::mount();

        $journal = Journal::where('type', JournalType::SALE)
            ->where('company_id', current_company_id())
            ->first();

        $this->data['move_type'] ??= MoveType::OUT_INVOICE->value;

        $this->data['journal_id'] = $journal?->id;

        $this->form->fill($this->data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::OUT_INVOICE;

        return $data;
    }

    protected function afterCreate(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());

        // "Paid now" (user, 2026-11-20): confirm and record the full payment.
        // Same transaction as the create, so a refused payment leaves no
        // half-done invoice behind.
        if (! empty($this->data['paid_now']) && ! empty($this->data['paid_journal_id'])) {
            try {
                app(QuickPayment::class)->confirmAndPay($this->getRecord()->refresh(), (int) $this->data['paid_journal_id']);
            } catch (ValidationException|QueryException $exception) {
                throw $exception;
            } catch (Throwable $exception) {
                // Posting refuses with a plain Exception ("Please add at least
                // one line to the invoice."). Uncaught, that was an error page
                // and a Sentry issue (DEKAERP-22). Shown the way the Confirm
                // button shows it, and the whole create is rolled back.
                Notification::make()
                    ->warning()
                    ->title(__('accounts::filament/resources/invoice/actions/confirm-action.notification.error.title'))
                    ->body($exception->getMessage())
                    ->persistent()
                    ->send();

                $this->halt(shouldRollbackDatabaseTransaction: true);
            }
        }
    }
}

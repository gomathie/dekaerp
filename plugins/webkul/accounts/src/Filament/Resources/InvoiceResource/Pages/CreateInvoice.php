<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Concerns\AsksSaleCurrency;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Models\Journal;
use Webkul\Account\Services\QuickPayment;
use Webkul\Account\Services\SaleCurrencyQuestion;
use Webkul\Account\Services\WalkInCustomer;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;

class CreateInvoice extends CreateRecord
{
    use AsksSaleCurrency;
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

    /**
     * Set only by "Create and confirm". "Save as draft" leaves it false so a
     * new invoice stays a draft, which is what Create used to do. Cleared
     * after the attempt: a refused confirm must not turn the next save into
     * a post.
     */
    public bool $confirmAfterCreate = false;

    protected bool $creatingWithConfirm = false;

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

        // "New direct sale" (user, 2026-11-22): the invoice form with the
        // walk-in customer and "Paid now" already set. Sales presets its own
        // boxes from the same flag (InvoiceContributions). One sale path:
        // the removed Direct Sale screen (2026-08-21) was a second one.
        if (request()->boolean('direct_sale')
            && ($this->data['move_type'] ?? null) === MoveType::OUT_INVOICE->value
            && current_company_id()) {
            $this->data['partner_id'] = app(WalkInCustomer::class)->forCompany((int) current_company_id())->getKey();
            // Only for someone who may record payments (user, 2026-11-26);
            // anyone else gets the walk-in invoice and records payment later.
            $this->data['paid_now'] = QuickPayment::mayRecordPayment();
        }

        $this->form->fill($this->data);

        // The currency question is chips on this form (plan 10 D2). The
        // blocking dialog stays available from "Currency and rate", and
        // quotations still open it themselves.
    }

    /**
     * A chip, or the rate typed under it. Locked properties, set here only:
     * the browser cannot write them. A currency the company may not use is
     * ignored.
     */
    public function applySaleCurrencyChoice(int $currencyId, ?float $displayRate): void
    {
        $kept = app(SaleCurrencyQuestion::class)->choice((int) current_company_id(), $currencyId, $displayRate);

        if ($kept === null) {
            return;
        }

        $this->data['currency_id'] = $kept['currency_id'];
        $this->saleCurrencyId = $kept['currency_id'];
        $this->saleCurrencyRate = $kept['rate'];
    }

    protected function saleCurrencyRateColumn(): string
    {
        return 'invoice_currency_rate';
    }

    /** The question is for sales: a customer invoice only, never a credit note. */
    protected function asksSaleCurrency(): bool
    {
        return ($this->data['move_type'] ?? null) === MoveType::OUT_INVOICE->value
            && app(SaleCurrencyQuestion::class)->isAsked(current_company_id());
    }

    protected function getHeaderActions(): array
    {
        return [$this->saleCurrencyHeaderAction()];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::OUT_INVOICE;

        return $data;
    }

    protected function handleRecordCreation(array $data): Model
    {
        if ($this->getParentRecord()) {
            return parent::handleRecordCreation($data);
        }

        $record = new ($this->getModel())($data);

        $this->applyConfirmedSaleRate($record);

        $record->save();

        return $record;
    }

    protected function afterCreate(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());

        $this->noteConfirmedSaleRate($this->getRecord(), 'accounts::filament/resources/invoice/pages/create-invoice.sale-currency.chatter');

        // "Paid now" (user, 2026-11-20): confirm and record the full payment.
        // Same transaction as the create, so a refused payment leaves no
        // half-done invoice behind.
        if (! empty($this->data['paid_now']) && ! empty($this->data['paid_journal_id'])) {
            try {
                // confirmAndPay already posts the invoice. Confirming again
                // here would post twice and refuse the second time.
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

            return;
        }

        if ($this->confirmAfterCreate && $this->getRecord()->move_type === MoveType::OUT_INVOICE) {
            try {
                AccountFacade::confirmMove($this->getRecord()->refresh());
            } catch (ValidationException|QueryException $exception) {
                throw $exception;
            } catch (Throwable $exception) {
                // Same refusal as Confirm (no lines, lock date). The draft is
                // rolled back with the create, so "Create and confirm" does
                // not leave a half-saved invoice.
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

    /**
     * Customer invoices only. Credit notes reuse this page and keep Create.
     * Their mount leaves move_type as a customer invoice until save
     * (??= does not replace the value set here), so the resource decides.
     */
    protected function offersCreateAndConfirm(): bool
    {
        if (str_contains(static::getResource(), 'CreditNote')) {
            return false;
        }

        return ($this->data['move_type'] ?? null) === MoveType::OUT_INVOICE->value;
    }

    public function createAndConfirm(): void
    {
        $this->confirmAfterCreate = true;
        $this->creatingWithConfirm = true;

        try {
            $this->create();
        } finally {
            $this->creatingWithConfirm = false;
            $this->confirmAfterCreate = false;
        }
    }

    public function create(bool $another = false): void
    {
        if (! $this->creatingWithConfirm) {
            $this->confirmAfterCreate = false;
        }

        parent::create($another);
    }

    protected function getFormActions(): array
    {
        if (! $this->offersCreateAndConfirm()) {
            return parent::getFormActions();
        }

        return [
            $this->getCreateAndConfirmFormAction(),
            $this->getSaveDraftFormAction(),
            ...($this->canCreateAnother() ? [$this->getCreateAnotherFormAction()] : []),
            $this->getCancelFormAction(),
        ];
    }

    protected function getCreateAndConfirmFormAction(): Action
    {
        return Action::make('create')
            ->label(__('accounts::filament/resources/invoice/pages/create-invoice.form.actions.create-and-confirm'))
            ->action('createAndConfirm')
            ->keyBindings(['mod+s']);
    }

    protected function getSaveDraftFormAction(): Action
    {
        return Action::make('saveDraft')
            ->label(__('accounts::filament/resources/invoice/pages/create-invoice.form.actions.save-draft'))
            ->action('create')
            ->color('gray');
    }
}

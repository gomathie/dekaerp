<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Throwable;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move;
use Webkul\Account\Services\QuickPayment;
use Webkul\Account\Services\SaleCurrencyQuestion;
use Webkul\Account\Services\WalkInCustomer;
use Webkul\Support\Filament\Concerns\HandlesCrossCompanyException;
use Webkul\Support\Filament\Concerns\HasRepeaterColumnManager;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

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

    /**
     * The currency question's answer (plan #10 MC-2). Locked: only the
     * dialog's own action sets them, never the browser.
     */
    #[Locked]
    public ?int $saleCurrencyId = null;

    /** A corrected rate to keep (stored convention), or null for the feed's. */
    #[Locked]
    public ?float $saleCurrencyRate = null;

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
            $this->data['paid_now'] = true;
        }

        $this->form->fill($this->data);

        // "Ask which currency each sale is in" (plan #10 MC-2): the question
        // opens with the page, before anything is typed.
        if (($this->data['move_type'] ?? null) === MoveType::OUT_INVOICE->value
            && app(SaleCurrencyQuestion::class)->isAsked(current_company_id())) {
            $this->defaultAction = 'askSaleCurrency';
        }
    }

    /**
     * The currency question (plan #10 MC-2, TRACE365, user 2026-11-24). The
     * base currency opens the form as before. Another currency shows the
     * day's rate, which the person accepts or corrects; there is no way on
     * without a rate, because a missing rate is never 1.
     */
    public function askSaleCurrencyAction(): Action
    {
        $question = app(SaleCurrencyQuestion::class);
        $company = Company::withoutGlobalScopes()->with('currency')->find(current_company_id());
        $baseId = (int) $company?->currency_id;
        $lang = 'accounts::filament/resources/invoice/pages/create-invoice.sale-currency';

        $feed = fn (?int $currencyId): ?array => $company && $currencyId && $currencyId !== $baseId && ($currency = Currency::find($currencyId))
            ? $question->feedRate($currency, $company)
            : null;

        $display = function (?int $currencyId) use ($question, $company, $feed): ?array {
            $currency = $currencyId ? Currency::find($currencyId) : null;

            if (! $company?->currency || ! $currency) {
                return null;
            }

            return $question->display($feed($currencyId)['rate'] ?? 1.0, $company->currency, $currency);
        };

        $fillRate = function (Set $set, ?int $currencyId) use ($feed, $display): void {
            $set('rate', $feed($currencyId) ? ($display($currencyId)['amount'] ?? null) : null);
        };

        return Action::make('askSaleCurrency')
            ->modalHeading(__($lang.'.heading'))
            ->modalDescription(__($lang.'.description'))
            ->modalIcon('heroicon-o-banknotes')
            ->modalWidth(Width::Large)
            ->modalSubmitActionLabel(__($lang.'.continue'))
            ->fillForm(function () use ($baseId, $feed, $display): array {
                $currencyId = (int) ($this->data['currency_id'] ?? $baseId);
                $shown = $display($currencyId);

                return [
                    'currency_id' => $currencyId,
                    'rate'        => $feed($currencyId) ? ($shown['amount'] ?? null) : null,
                ];
            })
            ->schema([
                ToggleButtons::make('currency_id')
                    ->hiddenLabel()
                    ->options(fn (): array => collect($question->options((int) current_company_id()))
                        ->mapWithKeys(fn (string $code, int $id): array => [$id => $id === $baseId ? __($lang.'.base', ['code' => $code]) : $code])
                        ->all())
                    ->inline()
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn (Set $set, $state) => $fillRate($set, (int) $state)),
                TextInput::make('rate')
                    ->label(__($lang.'.rate'))
                    ->numeric()
                    ->minValue(0.000001)
                    ->required(fn (Get $get): bool => (int) $get('currency_id') !== $baseId)
                    ->visible(fn (Get $get): bool => (int) $get('currency_id') !== $baseId)
                    ->prefix(fn (Get $get): string => '1 '.($display((int) $get('currency_id'))['from'] ?? '').' =')
                    ->suffix(fn (Get $get): string => $display((int) $get('currency_id'))['to'] ?? '')
                    ->helperText(function (Get $get) use ($feed, $lang): string {
                        $rate = $feed((int) $get('currency_id'));

                        if ($rate === null) {
                            return __($lang.'.no-rate');
                        }

                        $date = $rate['date']?->translatedFormat('j M Y') ?? '—';

                        return $rate['stale'] ? __($lang.'.stale', ['date' => $date]) : __($lang.'.feed', ['date' => $date]);
                    }),
            ])
            ->action(function (array $data) use ($question, $baseId, $feed, $display): void {
                $currencyId = (int) $data['currency_id'];

                // Only a currency the company may use (CR-6); anything else
                // keeps the form as it was.
                if (! array_key_exists($currencyId, $question->options((int) current_company_id()))) {
                    return;
                }

                $this->data['currency_id'] = $currencyId;
                $this->saleCurrencyId = $currencyId;
                $this->saleCurrencyRate = null;

                if ($currencyId === $baseId) {
                    return;
                }

                // The direction the dialog showed, worked out again here
                // rather than taken back from the browser.
                $stored = $question->fromDisplay((float) $data['rate'], $display($currencyId)['baseFirst'] ?? true);
                $feedRate = $feed($currencyId)['rate'] ?? null;

                // The feed's own rate is not pinned: it keeps following the
                // invoice date, as every invoice does. Only a corrected rate
                // (or one typed because there was none) is kept as manual.
                if ($feedRate === null || abs($stored - $feedRate) > 1e-9 * max(1.0, $feedRate)) {
                    $this->saleCurrencyRate = $stored;
                }
            });
    }

    /**
     * "Currency and rate" reopens the same question, so a rate can be
     * corrected after the dialog is closed (MC-2 rate bar). It is the one
     * action, not a copy: a second dialog would be a second set of rules.
     */
    protected function getHeaderActions(): array
    {
        return [
            $this->askSaleCurrencyAction()
                ->label(__('accounts::filament/resources/invoice/pages/create-invoice.sale-currency.change'))
                ->icon('heroicon-o-banknotes')
                ->color('gray')
                ->visible(fn (): bool => ($this->data['move_type'] ?? null) === MoveType::OUT_INVOICE->value
                    && app(SaleCurrencyQuestion::class)->isAsked(current_company_id())),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['move_type'] ??= MoveType::OUT_INVOICE;

        return $data;
    }

    /**
     * A confirmed rate goes on the invoice before its first save, so every
     * line is costed at it from the start. Applied only while the invoice
     * is still in the currency it was confirmed for: choosing a customer
     * with another default currency afterwards leaves it on the feed.
     */
    protected function handleRecordCreation(array $data): Model
    {
        if ($this->getParentRecord()) {
            return parent::handleRecordCreation($data);
        }

        $record = new ($this->getModel())($data);

        if ($this->saleCurrencyRate !== null && (int) $record->currency_id === (int) $this->saleCurrencyId) {
            $record->forceFill([
                'invoice_currency_rate'      => $this->saleCurrencyRate,
                'currency_rate_source'       => Move::RATE_MANUAL,
                'currency_rate_confirmed_by' => Auth::id(),
            ]);
        }

        $record->save();

        return $record;
    }

    protected function afterCreate(): void
    {
        AccountFacade::computeAccountMove($this->getRecord());

        // Who kept which rate, on the invoice's own history (MC-2).
        $record = $this->getRecord();

        if ($record->currency_rate_source === Move::RATE_MANUAL && $record->currency && $record->company?->currency) {
            $record->addMessage([
                'type' => 'note',
                'body' => e(__('accounts::filament/resources/invoice/pages/create-invoice.sale-currency.chatter', [
                    'rate' => app(SaleCurrencyQuestion::class)->label((float) $record->invoice_currency_rate, $record->company->currency, $record->currency),
                ])),
            ]);
        }

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

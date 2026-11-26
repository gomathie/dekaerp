<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Throwable;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\Account\Models\PaymentMethodLine;
use Webkul\Account\Models\PaymentRegister;
use Webkul\Account\Services\PaymentWaysProvisioner;
use Webkul\Account\Services\QuickPayment;
use Webkul\Accounting\Models\Journal;
use Webkul\Support\Models\Company;

class PayAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.pay';
    }

    protected function getAvailablePartnerBanks(PaymentRegister $paymentRegister, $journalId): Collection
    {
        $batch = data_get($paymentRegister->batches, '0');

        $journal = $journalId ? Journal::find($journalId) : null;

        if (! $batch || ! $journal) {
            return collect();
        }

        return $paymentRegister->getBatchAvailablePartnerBanks($batch, $journal);
    }

    protected function resolvePartnerBankId(PaymentRegister $paymentRegister, $journalId): ?int
    {
        $availablePartnerBanks = $this->getAvailablePartnerBanks($paymentRegister, $journalId);

        $partnerBankId = data_get($paymentRegister->batches, '0.payment_values.partner_bank_id');

        if ($partnerBankId && $availablePartnerBanks->pluck('id')->contains($partnerBankId)) {
            return $partnerBankId;
        }

        return $availablePartnerBanks->first()?->id;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/pay-action.title'))
            // "Record payment", Zoho style (user, 2026-11-26): the main next
            // step on a confirmed invoice or bill that is not fully paid.
            ->color('primary')
            ->schema(function (Schema $schema) {
                $paymentRegister = new PaymentRegister;

                try {
                    $paymentRegister->lines = $this->getRecord()->lines;
                    $paymentRegister->company = $this->getRecord()->company;
                    $paymentRegister->currency = $this->getRecord()->currency;
                    $paymentRegister->currency_id = $this->getRecord()->currency_id;
                    $paymentRegister->payment_type = $this->getRecord()->isInbound(true)
                        ? PaymentType::RECEIVE
                        : PaymentType::SEND;
                    $paymentRegister->computeBatches();
                    $paymentRegister->computeAvailableJournalIds();
                    $paymentRegister->journal_id = $paymentRegister->available_journal_ids[0] ?? null;
                    $paymentRegister->journal = Journal::find($paymentRegister->journal_id);

                    $paymentRegister->computePaymentMethodLineId();

                    $amountsToPay = $paymentRegister->getTotalAmountsToPay($paymentRegister->batches);
                    $paymentRegister->amount = $amountsToPay['amount_by_default'];
                    $paymentRegister->computeInstallmentsMode();
                } catch (Throwable $e) {
                    // Reported, and a plain sentence for the user: the raw
                    // message ("Call to a member function isZero() on null")
                    // reached a client and never reached Sentry (2026-11-19).
                    report($e);

                    Notification::make()
                        ->title(__('accounts::filament/resources/invoice/actions/pay-action.notifications.payment-failed.title'))
                        ->body(__('accounts::filament/resources/invoice/actions/pay-action.notifications.payment-failed.plain-body'))
                        ->danger()
                        ->send();
                }

                return $schema->components([
                    /*
                     * One question first (user, 2026-11-19, after comparing Zoho
                     * Books): how it was paid, how much, when. Everything else is
                     * already defaulted and waits under More options.
                     */
                    Group::make()
                        ->schema([
                            Select::make('journal_id')
                                ->relationship(
                                    'journal',
                                    'name',
                                    modifyQueryUsing: fn (Builder $query) => $query->whereIn('id', $paymentRegister->available_journal_ids)
                                )
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.paid-how'))
                                ->searchable()
                                ->preload()
                                ->required()
                                ->live()
                                ->default(fn () => $paymentRegister->available_journal_ids[0] ?? null)
                                // "Add your own" way to pay (user, 2026-11-19): a journal
                                // with its own account, for whoever may create journals.
                                ->createOptionForm([
                                    TextInput::make('name')
                                        ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.new-way'))
                                        ->required()
                                        ->maxLength(64),
                                ])
                                ->createOptionUsing(fn (array $data, $record): ?int => app(PaymentWaysProvisioner::class)
                                    ->addWay(Company::withoutGlobalScopes()->findOrFail($record->company_id), $data['name']))
                                ->createOptionAction(fn (Action $action) => $action
                                    ->visible(fn (): bool => (bool) Auth::user()?->can('create', Journal::class)))
                                ->afterStateUpdated(function (Set $set, Get $get) use ($paymentRegister) {
                                    $paymentRegister->journal_id = $get('journal_id');
                                    $paymentRegister->journal = Journal::find($get('journal_id'));
                                    $paymentRegister->computePaymentMethodLineId();

                                    $set('payment_method_line_id', $paymentRegister->payment_method_line_id);
                                    $set('partner_bank_id', $this->resolvePartnerBankId($paymentRegister, $get('journal_id')));
                                }),

                            Hidden::make('installments_mode')
                                ->default($paymentRegister->installments_mode),
                            TextInput::make('amount')
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.amount'))
                                ->prefix(fn ($record) => $record->currency->symbol ?? '')
                                ->default($paymentRegister->amount)
                                ->required()
                                ->live(onBlur: true)
                                ->afterStateUpdated(function (Set $set, $state) use ($paymentRegister) {
                                    $paymentRegister->amount = $state;
                                    $paymentRegister->computeInstallmentsMode();
                                    $set('installments_mode', $paymentRegister->installments_mode);
                                })
                                ->helperText(function (Get $get) use ($paymentRegister) {
                                    $paymentRegister->amount = $get('amount') ?? $paymentRegister->amount;
                                    $paymentRegister->installments_mode = $get('installments_mode') ?? $paymentRegister->installments_mode;

                                    $switchValues = $paymentRegister->computeInstallmentsSwitchValues();

                                    if (! $switchValues['installments_switch_html']) {
                                        return null;
                                    }

                                    return new HtmlString($switchValues['installments_switch_html']);
                                })
                                ->hintAction(
                                    Action::make('toggleInstallments')
                                        ->label(function (Get $get) use ($paymentRegister) {
                                            $installmentsMode = $get('installments_mode') ?? $paymentRegister->installments_mode;

                                            return $installmentsMode === 'full' ? 'installments' : 'full amount';
                                        })
                                        ->link()
                                        ->action(function (Set $set, Get $get) use ($paymentRegister) {
                                            $switchValues = $paymentRegister->computeInstallmentsSwitchValues();

                                            if ($switchValues['installments_switch_amount'] > 0) {
                                                $paymentRegister->amount = $switchValues['installments_switch_amount'];
                                                $paymentRegister->computeInstallmentsMode();

                                                $set('amount', $paymentRegister->amount);
                                                $set('installments_mode', $paymentRegister->installments_mode);
                                            }
                                        })
                                ),
                            DatePicker::make('payment_date')
                                ->native(false)
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.payment-date'))
                                ->default(now())
                                ->required(),
                        ])
                        ->columnSpanFull(),

                    Section::make(__('accounts::filament/resources/invoice/actions/pay-action.form.more-options'))
                        ->collapsed()
                        ->schema([
                            Select::make('payment_method_line_id')
                                ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.payment-method'))
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live()
                                ->default($paymentRegister->payment_method_line_id)
                                ->relationship(
                                    name: 'paymentMethodLine',
                                    titleAttribute: 'name',
                                    modifyQueryUsing: function (Builder $query, Get $get) {
                                        $journal = Journal::find($get('journal_id'));

                                        if (! $journal) {
                                            return $query->whereRaw('1 = 0');
                                        }

                                        $paymentMethodLineIds = $journal->getAvailablePaymentMethodLines(
                                            $this->getRecord()->isInbound(true)
                                                ? PaymentType::RECEIVE
                                                : PaymentType::SEND
                                        )->pluck('id');

                                        $query->whereIn('id', $paymentMethodLineIds);
                                    }
                                )
                                ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                                ->afterStateUpdated(function (Set $set, Get $get) use ($paymentRegister) {
                                    $paymentRegister->payment_method_line_id = $get('payment_method_line_id');
                                    $paymentRegister->paymentMethodLine = PaymentMethodLine::find($get('payment_method_line_id'));
                                    $paymentRegister->journal = Journal::find($get('journal_id'));
                                    $paymentRegister->computeShowRequirePartnerBank();
                                }),
                            Select::make('partner_bank_id')
                                ->relationship(
                                    'partnerBank',
                                    'account_number',
                                    modifyQueryUsing: function (Builder $query, Get $get) use ($paymentRegister) {
                                        $query
                                            ->withTrashed()
                                            ->whereIn('id', $this->getAvailablePartnerBanks($paymentRegister, $get('journal_id'))->pluck('id'));
                                    }
                                )
                                ->getOptionLabelFromRecordUsing(function ($record): string {
                                    return $record->account_number.' - '.$record->bank?->name.($record->trashed() ? ' (Deleted)' : '');
                                })
                                ->disableOptionWhen(function ($label) {
                                    return str_contains($label, ' (Deleted)');
                                })
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.recipient-bank-account'))
                                ->default(fn () => $this->resolvePartnerBankId($paymentRegister, $paymentRegister->journal_id))
                                ->searchable()
                                ->preload()
                                ->required(function (Get $get) use ($paymentRegister) {
                                    $journal = Journal::find($get('journal_id'));

                                    if (! $journal) {
                                        return false;
                                    }

                                    $paymentRegister->journal = $journal;
                                    $paymentRegister->payment_method_line_id = $get('payment_method_line_id');
                                    $paymentRegister->paymentMethodLine = PaymentMethodLine::find($get('payment_method_line_id'));

                                    if (! $paymentRegister->paymentMethodLine) {
                                        return false;
                                    }

                                    $paymentRegister->computeShowRequirePartnerBank();

                                    return $paymentRegister->require_partner_bank_account && $paymentRegister->show_partner_bank_account;
                                })
                                ->visible(function (Get $get) use ($paymentRegister) {
                                    $journal = Journal::find($get('journal_id'));

                                    if (! $journal) {
                                        return false;
                                    }

                                    $paymentRegister->journal = $journal;
                                    $paymentRegister->payment_method_line_id = $get('payment_method_line_id');
                                    $paymentRegister->paymentMethodLine = PaymentMethodLine::find($get('payment_method_line_id'));

                                    if (! $paymentRegister->paymentMethodLine) {
                                        return false;
                                    }

                                    $paymentRegister->computeShowRequirePartnerBank();

                                    return $paymentRegister->show_partner_bank_account;
                                })
                                ->disabled($this->getRecord()->isInbound(true))
                                ->dehydrated(),
                            Select::make('currency_id')
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.currency'))
                                ->relationship(
                                    name: 'currency',
                                    titleAttribute: 'name',
                                    // CR-6: the invoice's company decides which currencies may pay it.
                                    modifyQueryUsing: fn (Builder $query, $record) => $query->active()->permittedFor($record?->company_id ?: current_company_id()),
                                )
                                // The invoice's own currency, never the journal's: a GHS
                                // company's bank journal created in USD made every GHS
                                // invoice pay in USD, which then failed to match the
                                // invoice ("isZero() on null", TRACE365, 2026-11-19).
                                ->default(fn ($record) => $record->currency_id)
                                ->required()
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('communication')
                                ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.communication'))
                                ->default(function ($record) {
                                    return $record->name;
                                })
                                ->required(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                    ->columns(2);
            })
            ->action(function (Move $record, $data, Component $livewire): void {
                try {
                    $lineIds = $record->paymentTermLines
                        ->filter(fn ($line) => ! $line->reconciled)
                        ->pluck('id')
                        ->toArray();

                    $paymentRegister = PaymentRegister::create($data);

                    $paymentRegister->lines()->sync($lineIds);

                    $paymentRegister->refresh();

                    $paymentRegister->computeFromLines();

                    $paymentRegister->save();

                    AccountFacade::createPayments($paymentRegister);

                    $record->refresh();

                    if (method_exists($livewire, 'refreshFormData')) {
                        $livewire->refreshFormData(['state', 'payment_state', 'amount_residual']);
                    }

                    $livewire->dispatch('refreshInvoiceSummary');
                } catch (Throwable $e) {
                    Notification::make()
                        ->danger()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->hidden(function (Move $record) {
                // Who may record a payment: one rule, shared with "Paid now".
                // Hidden actions cannot be called, so this also refuses a
                // crafted request.
                return ! QuickPayment::mayRecordPayment()
                    || $record->state != MoveState::POSTED
                    || ! in_array($record->payment_state, [
                        PaymentState::NOT_PAID,
                        PaymentState::PARTIAL,
                        PaymentState::IN_PAYMENT,
                    ]);
            });
    }
}

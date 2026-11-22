<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Schemas;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Size;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\CommunicationStandard;
use Webkul\Account\Enums\CommunicationType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Resources\BankAccountResource;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\InvoiceResource\Support\InvoiceSchemaRegistry;
use Webkul\Account\Filament\Resources\JournalResource;
use Webkul\Account\Models\FiscalPosition;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Partner;
use Webkul\Account\Settings\CustomerInvoiceSettings;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Partner\Models\Partner as BasePartner;
use Webkul\Support\Filament\CompanyMembers;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class InvoiceForm
{
    /**
     * Credit notes reuse this form. Before the form moved out of the
     * resource, `static::class` named the resource; here it would always
     * name InvoiceForm, so ask the page which resource it serves.
     */
    protected static function isCreditNote($livewire): bool
    {
        return is_object($livewire)
            && method_exists($livewire, 'getResource')
            && str_contains($livewire::getResource(), 'CreditNote');
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = MoveState::options();

                        if ($record?->state !== MoveState::CANCEL) {
                            unset($options[MoveState::CANCEL->value]);
                        }

                        return $options;
                    })
                    ->default(MoveState::DRAFT->value)
                    ->columnSpan('full')
                    ->disabled()
                    ->live()
                    ->reactive(),

                Section::make(__('accounts::filament/resources/invoice.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Actions::make([
                            Action::make('payment_state')
                                ->icon(fn ($record) => $record->payment_state->getIcon())
                                ->color(fn ($record) => $record->payment_state->getColor())
                                ->visible(fn ($record) => in_array($record?->payment_state, [PaymentState::PAID, PaymentState::REVERSED]))
                                ->label(fn ($record) => $record->payment_state->getLabel())
                                ->size(Size::ExtraLarge->value),
                        ]),
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('partner_id')
                                            ->label(__('accounts::filament/resources/invoice.form.section.general.fields.customer'))
                                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name.($record->trashed() ? ' (Deleted)' : ''))
                                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                            ->relationship(
                                                'partner',
                                                'name',
                                                fn (Builder $query) => $query->orderBy('id')->withTrashed(),
                                            )
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            /*
                                             * "+" adds a customer without leaving the invoice
                                             * (TRACE365, 2026-11-19). Only the essentials are asked;
                                             * the full contact form stays under Contacts. The
                                             * contact is stamped with the current company by
                                             * BelongsToCompany, and needs the contact create
                                             * permission like the Contacts screen.
                                             */
                                            ->createOptionForm([
                                                TextInput::make('name')
                                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.name'))
                                                    ->required()
                                                    ->maxLength(255),
                                                TextInput::make('email')
                                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.email'))
                                                    ->email()
                                                    ->maxLength(255),
                                                TextInput::make('phone')
                                                    ->label(__('partners::filament/resources/partner.form.sections.general.fields.phone'))
                                                    ->tel()
                                                    ->maxLength(255),
                                            ])
                                            ->createOptionAction(fn (Action $action) => $action
                                                ->visible(fn (): bool => (bool) Auth::user()?->can('create', BasePartner::class)))
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $partner = $state ? Partner::find($state) : null;

                                                // The customer's own salesperson wins; a customer without
                                                // one keeps whoever is already chosen (2026-11-20).
                                                if ($partner?->user_id) {
                                                    $set('invoice_user_id', $partner->user_id);
                                                }

                                                $set('preferred_payment_method_line_id', $partner?->property_inbound_payment_method_line_id);

                                                $set('invoice_payment_term_id', $partner?->property_payment_term_id);
                                            })
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        /*
                                         * Salesperson (user, 2026-11-20): shown next to the
                                         * customer and filled with whoever creates the invoice,
                                         * so the Sales and Invoices salesperson charts count
                                         * invoices made without an order. It used to sit empty
                                         * under "Other information" and list every user on the
                                         * installation; CompanyMembers limits it to this
                                         * company's people.
                                         */
                                        Select::make('invoice_user_id')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.sales-person'))
                                            ->relationship('invoiceUser', 'name', fn (Builder $query): Builder => CompanyMembers::scope($query))
                                            ->default(fn (): ?int => Auth::id())
                                            ->searchable()
                                            ->preload(),
                                    ]),

                                Group::make()
                                    ->schema([
                                        DatePicker::make('invoice_date')
                                            ->label(__('accounts::filament/resources/invoice.form.section.general.fields.invoice-date'))
                                            ->default(now())
                                            ->native(false)
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        /*
                                         * "Paid now" (user, 2026-11-20): saving a new invoice
                                         * confirms it and records the full payment in one step
                                         * (QuickPayment). Not stored on the invoice; read by
                                         * CreateInvoice::afterCreate(). Create only, and
                                         * customer invoices only: credit notes reuse this form.
                                         */
                                        Toggle::make('paid_now')
                                            ->label(__('accounts::filament/resources/invoice/actions/pay-action.paid-now.label'))
                                            ->helperText(__('accounts::filament/resources/invoice/actions/pay-action.paid-now.helper'))
                                            ->live()
                                            ->dehydrated(false)
                                            ->visible(fn (string $operation, $livewire): bool => $operation === 'create' && ! static::isCreditNote($livewire)),
                                        Select::make('paid_journal_id')
                                            ->label(__('accounts::filament/resources/invoice/actions/pay-action.form.fields.paid-how'))
                                            ->options(fn (Get $get): array => Journal::query()
                                                ->where('company_id', $get('company_id') ?: current_company_id())
                                                ->whereIn('type', [JournalType::BANK, JournalType::CASH])
                                                ->orderBy('sort')
                                                ->pluck('name', 'id')
                                                ->all())
                                            ->default(fn (Get $get) => Journal::query()
                                                ->where('company_id', $get('company_id') ?: current_company_id())
                                                ->where('type', JournalType::CASH)
                                                ->value('id'))
                                            ->required(fn (Get $get): bool => (bool) $get('paid_now'))
                                            ->visible(fn (Get $get, string $operation, $livewire): bool => $operation === 'create' && (bool) $get('paid_now') && ! static::isCreditNote($livewire))
                                            ->dehydrated(false),
                                        // Fields other plugins add (Sales: create a linked
                                        // sales order). Accounts must not reference them.
                                        ...InvoiceSchemaRegistry::renderForm('general.after'),
                                        Group::make()
                                            ->schema([
                                                DatePicker::make('invoice_date_due')
                                                    ->required()
                                                    ->default(now())
                                                    ->native(false)
                                                    ->live()
                                                    ->hidden(fn (Get $get) => $get('invoice_payment_term_id') !== null)
                                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.due-date')),
                                                Select::make('invoice_payment_term_id')
                                                    ->relationship(
                                                        'invoicePaymentTerm',
                                                        'name',
                                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                                            ->withTrashed()
                                                            ->where(owned_by_company($get('company_id'))),
                                                    )
                                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                                    })
                                                    ->disableOptionWhen(function ($label) {
                                                        return str_contains($label, ' (Deleted)');
                                                    })
                                                    ->required(fn (Get $get) => $get('invoice_date_due') === null)
                                                    ->live()
                                                    ->searchable()
                                                    ->preload()
                                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL]))
                                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.payment-term')),
                                            ])
                                            ->columns(2),

                                        Group::make()
                                            ->schema([
                                                Select::make('journal_id')
                                                    ->relationship(
                                                        'journal',
                                                        'name',
                                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                                            ->where('type', JournalType::SALE)
                                                            ->where(owned_by_company($get('company_id'))),
                                                    )
                                                    ->searchable()
                                                    ->preload()
                                                    ->required()
                                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.journal'))
                                                    ->createOptionForm(function ($form) {
                                                        $schema = JournalResource::form($form);

                                                        // Find and disable the type field
                                                        $components = $schema->getComponents();
                                                        foreach ($components as $component) {
                                                            InvoiceResource::disableTypeField($component);
                                                        }

                                                        return $schema;
                                                    })
                                                    ->createOptionAction(
                                                        fn (Action $action, Get $get) => $action
                                                            ->fillForm(fn () => [
                                                                'type'                     => JournalType::SALE,
                                                                'invoice_reference_type'   => CommunicationType::INVOICE,
                                                                'invoice_reference_model'  => CommunicationStandard::AUREUS,
                                                                'company_id'               => $get('company_id') ?? current_company_id(),
                                                            ])
                                                    )
                                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),

                                                Select::make('currency_id')
                                                    ->label(__('accounts::filament/resources/invoice.form.section.general.fields.currency'))
                                                    ->relationship(
                                                        name: 'currency',
                                                        titleAttribute: 'name',
                                                        // CR-6: only the company's permitted currencies, when it has a list.
                                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->active()->permittedFor((int) ($get('company_id') ?: current_company_id())),
                                                    )
                                                    ->required()
                                                    ->searchable()
                                                    ->preload()
                                                    ->live()
                                                    ->reactive()
                                                    ->default(current_company()?->currency_id)
                                                    // Switching currency used to change only the label: lines kept
                                                    // the old currency's prices and the summary its old symbol
                                                    // (client report, 2026-11-19).
                                                    ->afterStateUpdated(fn (Set $set, Get $get, $state, $old, $livewire) => InvoiceResource::afterCurrencyUpdated($set, $get, $state, $old, $livewire))
                                                    ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                            ])
                                            ->columns(2),
                                    ]),
                            ])->columns(2),
                    ]),

                Tabs::make()
                    ->schema([
                        Tab::make(__('accounts::filament/resources/invoice.form.tabs.invoice-lines.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                InvoiceResource::getProductRepeater(),

                                Livewire::make(InvoiceResource::getSummaryComponent(), function (Get $get, $record, $livewire) {
                                    $totals = InvoiceResource::calculateMoveTotals($get, $livewire);

                                    $currency = Currency::find($get('currency_id'));

                                    return [
                                        'record'     => $record,
                                        'rounding'   => $totals['rounding'],
                                        'amountTax'  => $totals['totalTax'],
                                        'subtotal'   => $totals['subtotal'],
                                        'totalTax'   => $totals['totalTax'],
                                        'grandTotal' => $totals['grandTotal'] + $totals['rounding'],
                                        'currency'   => $currency,
                                    ];
                                })
                                    ->key('invoiceSummary')
                                    ->reactive()
                                    ->visible(fn (Get $get) => $get('currency_id') && ! empty($get('products'))),
                            ]),

                        Tab::make(__('accounts::filament/resources/invoice.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.title'))
                                    ->schema([
                                        TextInput::make('reference')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.customer-reference'))
                                            ->maxLength(255),
                                        Select::make('partner_bank_id')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.recipient-bank'))
                                            ->relationship(
                                                'partnerBank',
                                                'account_number',
                                                modifyQueryUsing: function (Builder $query, Get $get) {
                                                    $companyId = $get('company_id') ?? current_company_id();

                                                    $bankAccountIds = Journal::where('type', JournalType::BANK)
                                                        ->where('company_id', $companyId)
                                                        ->pluck('bank_account_id')
                                                        ->filter();

                                                    $query->whereIn('id', $bankAccountIds);
                                                }
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->account_number.' - '.$record->bank->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema, Get $get) => BankAccountResource::form($schema)->fill([
                                                'partner_id' => $get('partner_id'),
                                            ]))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        TextInput::make('payment_reference')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.payment-reference')),
                                        DatePicker::make('delivery_date')
                                            ->native(false)
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.invoice.fields.delivery-date'))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                    ])
                                    ->columns(2),

                                Fieldset::make(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.title'))
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.company'))
                                            ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->default(current_company_id())
                                            ->live()
                                            ->afterStateUpdated(function (Get $get, Set $set) {
                                                $company = Company::find($get('company_id'));

                                                if ($company?->currency_id) {
                                                    $set('currency_id', $company->currency_id);
                                                }

                                                $set('journal_id', Journal::query()
                                                    ->where('type', JournalType::SALE)
                                                    ->where('company_id', $company?->id)
                                                    ->value('id'));

                                                clear_foreign_company_values($set, $get, [
                                                    'fiscal_position_id' => FiscalPosition::class,
                                                ], $company?->id);

                                                $set('partner_bank_id', null);
                                            }),
                                        Select::make('invoice_incoterm_id')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.incoterm'))
                                            ->relationship('invoiceIncoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->default(fn (CustomerInvoiceSettings $settings) => $settings->incoterm_id),
                                        TextInput::make('incoterm_location')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.incoterm-location')),
                                        Select::make('preferred_payment_method_line_id')
                                            ->relationship(
                                                name: 'paymentMethodLine',
                                                titleAttribute: 'name',
                                                // This company's lines only, with what the label
                                                // reads loaded once (DEKAERP-23); see forCompany().
                                                modifyQueryUsing: fn ($query, Get $get) => $query
                                                    ->forCompany($get('company_id'))
                                                    ->whereHas('paymentMethod', fn ($q) => $q->where('payment_type', PaymentType::RECEIVE))
                                                    ->with(['journal', 'paymentMethod']),
                                            )
                                            ->getOptionLabelFromRecordUsing(fn ($record) => $record->display_name)
                                            ->preload()
                                            ->searchable()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.payment-method')),
                                        Select::make('fiscal_position_id')
                                            ->relationship(
                                                'fiscalPosition',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                            )
                                            ->preload()
                                            ->searchable()
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.fiscal-position'))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.fiscal-position-tooltip'))
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        Select::make('invoice_cash_rounding_id')
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.cash-rounding'))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.cash-rounding-tooltip'))
                                            ->relationship('invoiceCashRounding', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->reactive()
                                            ->live()
                                            ->nullable()
                                            ->visible(fn (CustomerInvoiceSettings $settings) => (bool) $settings->group_cash_rounding)
                                            ->disabled(fn ($record) => in_array($record?->state, [MoveState::POSTED, MoveState::CANCEL])),
                                        Toggle::make('checked')
                                            ->inline(false)
                                            ->label(__('accounts::filament/resources/invoice.form.tabs.other-information.fieldset.accounting.fields.checked')),
                                    ])
                                    ->columns(2),
                            ])
                            ->columns(2),

                        Tab::make(__('accounts::filament/resources/invoice.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('narration')
                                    ->hiddenLabel(),
                            ]),
                    ]),

                Section::make()
                    ->schema(InvoiceResource::getCustomFormFields())
                    ->columns(2),
            ])
            ->columns(1);
    }
}

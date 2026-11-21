<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders\Resources\CustomerResource;
use Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource;
use Webkul\Sale\Livewire\QuotationSummary;
use Webkul\Sale\Models\Partner;
use Webkul\Sale\Settings\PriceSettings;
use Webkul\Sale\Settings\QuotationAndOrderSettings;
use Webkul\Support\Filament\CompanyMembers;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class QuotationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(function ($record) {
                        $options = OrderState::options();

                        if ($record == null) {
                            unset($options[OrderState::CANCEL->value]);
                        } else {
                            if ($record->state !== OrderState::CANCEL) {
                                unset($options[OrderState::CANCEL->value]);
                            } else {
                                unset($options[OrderState::SALE->value]);
                            }
                        }

                        return $options;
                    })
                    ->default(OrderState::DRAFT->value)
                    ->disabled()
                    ->live()
                    ->reactive(),
                Section::make(__('sales::filament/clusters/orders/resources/quotation.form.section.general.title'))
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        Select::make('partner_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.customer'))
                                            ->relationship(
                                                'partner',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('id')->withTrashed()
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->createOptionForm(fn (Schema $schema) => CustomerResource::form($schema))
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, $state) {
                                                $partner = $state ? Partner::find($state) : null;

                                                $set('user_id', $partner?->user?->id);
                                                $set('payment_term_id', $partner?->propertyPaymentTerm?->id);
                                            })
                                            ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::SALE, OrderState::CANCEL]))
                                            ->columnSpan(1)
                                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name.($record->trashed() ? ' (Deleted)' : ''))
                                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)')),
                                    ]),
                                DatePicker::make('validity_date')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.expiration'))
                                    ->native(false)
                                    ->default(fn (QuotationAndOrderSettings $settings) => now()->addDays($settings->default_quotation_validity))
                                    ->required()
                                    ->hidden(fn ($record) => $record)
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::SALE, OrderState::CANCEL])),
                                DatePicker::make('date_order')
                                    ->label(function ($record) {
                                        return $record?->state == OrderState::SALE
                                            ? __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.order-date')
                                            : __('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.quotation-date');
                                    })
                                    ->default(now())
                                    ->native(false)
                                    ->required()
                                    ->disabled(fn ($record): bool => $record?->locked || in_array($record?->state, [OrderState::SALE, OrderState::CANCEL])),
                                Select::make('payment_term_id')
                                    ->label(__('sales::filament/clusters/orders/resources/quotation.form.section.general.fields.payment-term'))
                                    ->relationship(
                                        'paymentTerm',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(1),
                            ])->columns(2),
                    ]),
                Tabs::make()
                    ->schema([
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.order-line.title'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                QuotationResource::getProductRepeater(),
                                Livewire::make(QuotationSummary::class, function (Get $get, PriceSettings $settings, $livewire) {
                                    $totals = QuotationResource::calculateQuotationTotals($get, $livewire);

                                    return [
                                        'currency'         => Currency::find($get('currency_id')),
                                        'enableMargin'     => $settings->enable_margin,
                                        'subtotal'         => $totals['subtotal'],
                                        'totalTax'         => $totals['totalTax'],
                                        'grandTotal'       => $totals['grandTotal'],
                                        'margin'           => $totals['margin'],
                                        'marginPercentage' => $totals['marginPercentage'],
                                    ];
                                })
                                    ->key('quotationSummary')
                                    ->reactive()
                                    ->visible(fn (Get $get) => $get('currency_id') && ! empty($get('products'))),
                            ]),
                        Tab::make(__('Optional Products'))
                            ->hidden(fn ($record) => $record && ! in_array($record->state, [OrderState::DRAFT, OrderState::SENT]))
                            ->icon('heroicon-o-arrow-path-rounded-square')
                            ->schema(function (Set $set, Get $get) {
                                return [
                                    QuotationResource::getOptionalProductRepeater($get, $set),
                                ];
                            }),
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.title'))
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.title'))
                                    ->schema([
                                        Select::make('user_id')
                                            ->relationship('user', 'name', fn ($query) => CompanyMembers::scope($query))
                                            ->searchable()
                                            ->preload()
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.sales-person')),
                                        TextInput::make('client_order_ref')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.customer-reference')),
                                        Select::make('sales_order_tags')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.sales.fields.tags'))
                                            ->relationship('tags', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.title'))
                                    ->schema([
                                        Select::make('warehouse_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.fields.warehouse'))
                                            ->relationship(
                                                'warehouse',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(
                                                    'company_id',
                                                    $get('company_id') ?? current_company_id(),
                                                )->orderBy('id'),
                                            )
                                            ->default(fn (Get $get): ?int => QuotationResource::getDefaultWarehouseId(
                                                $get('company_id') ?? current_company_id()
                                            ))
                                            ->searchable()
                                            ->preload()
                                            ->disabled(fn ($record) => in_array($record?->state, [OrderState::SALE, OrderState::CANCEL]))
                                            ->live()
                                            ->visible(fn (): bool => QuotationResource::canUseInventoryWarehouses()),
                                        DatePicker::make('commitment_date')
                                            ->disabled(fn ($record) => in_array($record?->state, [OrderState::CANCEL]))
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.shipping.fields.commitment-date'))
                                            ->native(false),
                                    ]),
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.title'))
                                    ->schema([
                                        TextInput::make('origin')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.source-document'))
                                            ->maxLength(255),
                                        Select::make('campaign_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.campaign'))
                                            ->relationship('campaign', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('medium_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.medium'))
                                            ->relationship('medium', 'name')
                                            ->searchable()
                                            ->preload(),
                                        Select::make('utm_source_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.tracking.fields.source'))
                                            ->relationship('utmSource', 'name')
                                            ->searchable()
                                            ->preload(),
                                    ]),
                                Fieldset::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.title'))
                                    ->schema([
                                        Select::make('company_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.fields.company'))
                                            ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(function ($label) {
                                                return str_contains($label, ' (Deleted)');
                                            })
                                            ->searchable()
                                            ->preload()
                                            ->live()
                                            ->afterStateUpdated(function (Set $set, Get $get, ?int $state): void {
                                                $companyId = $state ?? current_company_id();

                                                $set('currency_id', Company::find($state)?->currency_id);
                                                $set('warehouse_id', QuotationResource::getDefaultWarehouseId($companyId));

                                                clear_foreign_company_values($set, $get, [
                                                    'payment_term_id' => PaymentTerm::class,
                                                ], $companyId);
                                            })
                                            ->reactive()
                                            ->default(current_company_id()),
                                        Select::make('currency_id')
                                            ->label(__('sales::filament/clusters/orders/resources/quotation.form.tabs.other-information.fieldset.additional-information.fields.currency'))
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
                                            // Same as invoices (v1.12.2): switching currency converts
                                            // every line at the company's rate, or warns when no rate
                                            // exists instead of silently keeping the old figures.
                                            ->afterStateUpdated(fn (Set $set, Get $get, $state, $old, $livewire) => QuotationResource::afterCurrencyUpdated($set, $get, $state, $old, $livewire)),
                                        ...QuotationResource::getCustomFormFields(),
                                    ]),
                            ]),
                        Tab::make(__('sales::filament/clusters/orders/resources/quotation.form.tabs.term-and-conditions.title'))
                            ->icon('heroicon-o-clipboard-document-list')
                            ->schema([
                                RichEditor::make('note')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}

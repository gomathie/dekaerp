<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Filament\Resources\IncotermResource;
use Webkul\Account\Models\PaymentTerm;
use Webkul\Account\Support\DocumentCurrency;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Inventory\Enums as InventoryEnums;
use Webkul\Inventory\Models\OperationType;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\OrderResource;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\VendorResource;
use Webkul\Purchase\Livewire\OrderSummary;
use Webkul\Support\Filament\CompanyMembers;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

class OrderForm
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

                        if ($record && $record->state !== OrderState::CANCELED) {
                            unset($options[OrderState::CANCELED->value]);
                        }

                        if ($record && $record->state !== OrderState::DONE) {
                            unset($options[OrderState::DONE->value]);
                        }

                        if (! $record || $record->state !== OrderState::TO_APPROVE) {
                            unset($options[OrderState::TO_APPROVE->value]);
                        }

                        return $options;
                    })
                    ->default(OrderState::DRAFT)
                    ->disabled(),
                Section::make(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                        modifyQueryUsing: fn (Builder $query) => $query->orderBy('id')->withTrashed()
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->createOptionForm(fn (Schema $schema) => VendorResource::form($schema))
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) => OrderResource::handleVendorChange($state, $set, $get))
                                    ->live()
                                    ->reactive()
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                TextInput::make('partner_reference')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor-reference'))
                                    ->maxLength(255)
                                    ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.vendor-reference-tooltip')),
                                Select::make('requisition_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.agreement'))
                                    ->relationship(
                                        name: 'requisition',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: function (Builder $query, Get $get, $operation, $state) {
                                            $query
                                                ->where('partner_id', $get('partner_id'))
                                                ->where('company_id', $get('company_id') ?? current_company_id())
                                                ->where(function ($query) use ($operation, $state) {
                                                    $query->where('state', RequisitionState::CONFIRMED);
                                                    if ($operation !== 'create' && $state) {
                                                        $query->orWhere('id', $state);
                                                    }
                                                })
                                                ->where(function ($query) {
                                                    $query->whereNull('ends_at')
                                                        ->orWhere('ends_at', '>=', now());
                                                })
                                                ->where(function ($query) {
                                                    $query->whereNull('starts_at')
                                                        ->orWhere('starts_at', '<=', now());
                                                });
                                        })
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->searchable()
                                    ->preload()
                                    ->visible(OrderResource::getOrderSettings()->enable_purchase_agreements)
                                    ->afterStateUpdated(fn ($state, Set $set, Get $get) => OrderResource::handleRequisitionChange($state, $set, $get))
                                    ->live(),
                                Select::make('currency_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.currency'))
                                    ->relationship(
                                        name: 'currency',
                                        titleAttribute: 'name',
                                        // CR-6: only the company's permitted currencies, when it has a list.
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query->active()->permittedFor((int) ($get('company_id') ?: current_company_id())),
                                    )
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    // The Vendor Bills journal's currency, else the company's
                                    // (DocumentCurrency, TRACE365 2026-11-23: purchases in USD).
                                    ->default(fn (): ?int => DocumentCurrency::for(null, JournalType::PURCHASE))
                                    ->live()
                                    ->afterStateUpdated(fn (Set $set, Get $get) => OrderResource::updateProductPricesForVendor($get('partner_id'), $set, $get))
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                            ]),

                        Group::make()
                            ->schema([
                                DateTimePicker::make('approved_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.confirmation-date'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->disabled()
                                    ->visible(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                DateTimePicker::make('ordered_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.order-deadline'))
                                    ->native(false)
                                    ->required()
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->default(now())
                                    ->hidden(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                DateTimePicker::make('planned_at')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.expected-arrival'))
                                    ->native(false)
                                    ->suffixIcon('heroicon-o-calendar')
                                    ->hint('Test')
                                    ->hint(fn ($record): string => $record && $record->mail_reminder_confirmed ? __('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.confirmed-by-vendor') : '')
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                Select::make('operation_type_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.sections.general.fields.deliver-to'))
                                    ->relationship(
                                        name: 'operationType',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                            ->whereIn('type', [
                                                InventoryEnums\OperationType::INCOMING,
                                                InventoryEnums\OperationType::DROPSHIP,
                                            ])
                                            ->where(function (Builder $query) use ($get) {
                                                $query->whereNull('warehouse_id')
                                                    ->orWhereHas('warehouse', fn (Builder $q) => $q->where('company_id', $get('company_id') ?? current_company_id()));
                                            }),
                                    )
                                    ->getOptionLabelFromRecordUsing(function (OperationType $record) {
                                        if (! $record->warehouse) {
                                            return $record->name;
                                        }

                                        return $record->warehouse->name.': '.$record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->visible(fn (): bool => OrderResource::canUseInventoryWarehouses())
                                    ->default(fn (Get $get) => OrderResource::getInventoryOperationTypeId($get('company_id') ?? current_company_id()))
                                    ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                            ]),
                    ])
                    ->columns(2),

                Tabs::make()
                    ->schema([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.products.title'))
                            ->schema([
                                OrderResource::getProductRepeater(),
                                Livewire::make(OrderSummary::class, function (Get $get, $livewire) {
                                    $totals = OrderResource::calculateOrderTotals($get, $livewire);

                                    return [
                                        'record'     => $livewire->record ?? null,
                                        'currency'   => Currency::find($get('currency_id')),
                                        'subtotal'   => $totals['subtotal'],
                                        'totalTax'   => $totals['totalTax'],
                                        'grandTotal' => $totals['grandTotal'],
                                    ];
                                })
                                    ->key('orderSummary')
                                    ->reactive()
                                    ->visible(fn (Get $get) => $get('currency_id') && ! empty($get('products'))),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.title'))
                            ->schema(OrderResource::mergeCustomFormFields([
                                Group::make()
                                    ->schema([
                                        Select::make('user_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.buyer'))
                                            ->relationship('user', 'name', fn ($query) => CompanyMembers::scope($query))
                                            ->searchable()
                                            ->preload()
                                            ->default(Auth::id())
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                        Select::make('company_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.company'))
                                            ->relationship(
                                                'company',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query) => $query->withTrashed(),
                                            )
                                            ->getOptionLabelFromRecordUsing(function ($record): string {
                                                return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                            })
                                            ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                            ->searchable()
                                            ->preload()
                                            ->required()
                                            ->live()
                                            ->default(current_company_id())
                                            ->afterStateUpdated(function (Set $set, Get $get, $state): void {
                                                $set('operation_type_id', OrderResource::getInventoryOperationTypeId($state ?? current_company_id()));

                                                clear_foreign_company_values($set, $get, [
                                                    'payment_term_id' => PaymentTerm::class,
                                                ], $state);
                                            })
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT])),
                                        TextInput::make('origin')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.source-document'))
                                            ->maxLength(255),
                                        Select::make('incoterm_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm'))
                                            ->relationship('incoterm', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->createOptionForm(fn (Schema $schema) => IncotermResource::form($schema))
                                            ->hintIcon('heroicon-o-question-mark-circle', tooltip: __('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm-tooltip'))
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                        TextInput::make('incoterm_location')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.incoterm-location'))
                                            ->maxLength(255)
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                    ]),

                                Group::make()
                                    ->schema([
                                        Select::make('payment_term_id')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.additional.fields.payment-term'))
                                            ->relationship(
                                                'paymentTerm',
                                                'name',
                                                modifyQueryUsing: fn (Builder $query, Get $get) => $query->where(owned_by_company($get('company_id'))),
                                            )
                                            ->searchable()
                                            ->preload()
                                            ->disabled(fn ($record): bool => $record && ! in_array($record?->state, [OrderState::DRAFT, OrderState::SENT, OrderState::PURCHASE])),
                                    ]),
                            ]))
                            ->columns(2),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/order.form.tabs.terms.title'))
                            ->schema([
                                RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}

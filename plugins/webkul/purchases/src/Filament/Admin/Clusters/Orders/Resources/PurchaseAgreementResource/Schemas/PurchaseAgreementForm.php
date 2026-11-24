<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Field\Filament\Forms\Components\ProgressStepper as FormProgressStepper;
use Webkul\Purchase\Enums\RequisitionState;
use Webkul\Purchase\Enums\RequisitionType;
use Webkul\Purchase\Filament\Admin\Clusters\Orders\Resources\PurchaseAgreementResource;
use Webkul\Support\Filament\CompanyMembers;

class PurchaseAgreementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FormProgressStepper::make('state')
                    ->hiddenLabel()
                    ->inline()
                    ->options(RequisitionState::options())
                    ->default(RequisitionState::DRAFT)
                    ->disabled(),
                Section::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.title'))
                    ->schema([
                        Group::make()
                            ->schema([
                                Select::make('partner_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.vendor'))
                                    ->relationship(
                                        'partner',
                                        'name',
                                        fn (Builder $query) => $query->withTrashed()
                                    )
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT),
                                Select::make('user_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.buyer'))
                                    ->relationship('user', 'name', fn ($query) => CompanyMembers::scope($query))
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT),
                                Select::make('type')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.agreement-type'))
                                    ->options(RequisitionType::class)
                                    ->required()
                                    ->default(RequisitionType::BLANKET_ORDER)
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT)
                                    ->live(),
                                Select::make('currency_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.currency'))
                                    ->relationship(
                                        name: 'currency',
                                        titleAttribute: 'name',
                                        // Only the company's currencies (CR-6; plan #10 MC-1,
                                        // 2026-11-24): this was the one transaction picker
                                        // still offering every active currency.
                                        modifyQueryUsing: fn (Builder $query) => $query->active()->permittedFor(current_company_id()),
                                    )
                                    ->required()
                                    ->searchable()
                                    ->default(current_company()?->currency_id)
                                    ->preload(),
                            ]),

                        Group::make()
                            ->schema([
                                Group::make()
                                    ->schema([
                                        DatePicker::make('starts_at')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.valid-from'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar')
                                            ->minDate(now()->toDateString())
                                            ->live()
                                            ->afterStateUpdated(fn (Set $set) => $set('ends_at', null))
                                            ->rules([
                                                'date',
                                                'after_or_equal:today',
                                            ]),
                                        DatePicker::make('ends_at')
                                            ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.valid-to'))
                                            ->native(false)
                                            ->suffixIcon('heroicon-o-calendar')
                                            ->native(false)
                                            ->minDate(fn (Get $get) => $get('starts_at') ?: now()->toDateString())
                                            ->live()
                                            ->rules([
                                                'date',
                                                'after_or_equal:date_from',
                                            ]),
                                    ])
                                    ->columns(2)
                                    ->hidden(function (Get $get): bool {
                                        return $get('type') != RequisitionType::BLANKET_ORDER;
                                    }),
                                TextInput::make('reference')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.reference'))
                                    ->maxLength(255)
                                    ->placeholder(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.reference-placeholder')),
                                Select::make('company_id')
                                    ->label(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.sections.general.fields.company'))
                                    ->relationship('company', 'name', modifyQueryUsing: fn (Builder $query) => $query->withTrashed())
                                    ->getOptionLabelFromRecordUsing(function ($record): string {
                                        return $record->name.($record->trashed() ? ' (Deleted)' : '');
                                    })
                                    ->disableOptionWhen(function ($label) {
                                        return str_contains($label, ' (Deleted)');
                                    })
                                    ->searchable()
                                    ->required()
                                    ->preload()
                                    ->default(current_company_id())
                                    ->live()
                                    ->afterStateHydrated(PurchaseAgreementResource::handleCompanyChange(...))
                                    ->afterStateUpdated(PurchaseAgreementResource::handleCompanyChange(...))
                                    ->disabled(fn ($record): bool => $record && $record?->state != RequisitionState::DRAFT),
                            ]),
                    ])
                    ->columns(2),

                Tabs::make()
                    ->schema([
                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.products.title'))
                            ->schema([
                                PurchaseAgreementResource::getProductsRepeater(),
                            ]),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.additional.title'))
                            ->visible(! empty($customFormFields = PurchaseAgreementResource::getCustomFormFields()))
                            ->schema($customFormFields),

                        Tab::make(__('purchases::filament/admin/clusters/orders/resources/purchase-agreement.form.tabs.terms.title'))
                            ->schema([
                                RichEditor::make('description')
                                    ->hiddenLabel(),
                            ]),
                    ]),
            ])
            ->columns(1);
    }
}

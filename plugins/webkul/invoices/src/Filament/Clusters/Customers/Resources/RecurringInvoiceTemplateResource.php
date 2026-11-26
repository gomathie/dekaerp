<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Invoice\Filament\Clusters\Customers;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\Pages\CreateRecurringInvoiceTemplate;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\Pages\EditRecurringInvoiceTemplate;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\Pages\ListRecurringInvoiceTemplates;
use Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\RelationManagers\PeriodsRelationManager;
use Webkul\Invoice\Models\RecurringInvoiceTemplate;
use Webkul\Invoice\Services\RecurringInvoiceService;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\CompanyMembers;
use Webkul\Support\Models\Currency;

class RecurringInvoiceTemplateResource extends Resource
{
    public const LANG = 'invoices::recurring';

    protected static ?string $model = RecurringInvoiceTemplate::class;

    protected static ?string $cluster = Customers::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return __(self::LANG.'.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __(self::LANG.'.navigation');
    }

    public static function getNavigationLabel(): string
    {
        return __(self::LANG.'.navigation');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__(self::LANG.'.sections.details'))->schema([
                TextInput::make('name')->label(__(self::LANG.'.fields.name'))->required()->maxLength(255),
                Select::make('partner_id')->label(__(self::LANG.'.fields.customer'))->relationship('partner', 'name', modifyQueryUsing: fn ($query) => $query->where(owned_by_company(current_company_id())))->searchable()->preload()->required(),
                Select::make('journal_id')->label(__(self::LANG.'.fields.journal'))->relationship('journal', 'name', modifyQueryUsing: fn ($query) => $query->where('company_id', current_company_id())->where('type', 'sale'))->required(),
                Select::make('currency_id')->label(__(self::LANG.'.fields.currency'))->options(fn () => Currency::query()->active()->permittedFor(current_company_id())->orderBy('name')->pluck('name', 'id')->all())->searchable()->required(),
                Select::make('invoice_user_id')->label(__(self::LANG.'.fields.responsible'))->options(fn () => CompanyMembers::scope(User::query())->pluck('name', 'id')->all())->default(fn () => auth()->id())->searchable(),
                Select::make('invoice_payment_term_id')->label(__(self::LANG.'.fields.payment-term'))->relationship('paymentTerm', 'name', modifyQueryUsing: fn ($query) => $query->whereNull('company_id')->orWhere('company_id', current_company_id()))->searchable(),
                DatePicker::make('next_invoice_date')->label(__(self::LANG.'.fields.next-date'))->required()->native(false),
                Select::make('frequency')->label(__(self::LANG.'.fields.frequency'))->options([
                    'weekly'    => __(self::LANG.'.frequencies.weekly'),
                    'monthly'   => __(self::LANG.'.frequencies.monthly'),
                    'quarterly' => __(self::LANG.'.frequencies.quarterly'),
                    'yearly'    => __(self::LANG.'.frequencies.yearly'),
                ])->required(),
                DatePicker::make('ends_on')->label(__(self::LANG.'.fields.ends-on'))->native(false),
                Toggle::make('auto_email')->label(__(self::LANG.'.fields.auto-email'))->default(false),
                Textarea::make('narration')->label(__(self::LANG.'.fields.notes'))->rows(3)->maxLength(10000),
            ])->columns(2),
            Section::make(__(self::LANG.'.sections.lines'))->schema([
                Repeater::make('lines')->relationship()->schema([
                    Select::make('product_id')->label(__(self::LANG.'.fields.product'))->relationship('product', 'name', modifyQueryUsing: fn ($query) => $query->where(owned_by_company(current_company_id())))->searchable()->preload()->live(),
                    TextInput::make('name')->label(__(self::LANG.'.fields.description'))->maxLength(255),
                    TextInput::make('quantity')->label(__(self::LANG.'.fields.quantity'))->numeric()->minValue(0.000001)->default(1)->required(),
                    TextInput::make('price_unit')->label(__(self::LANG.'.fields.price'))->numeric()->minValue(0)->required(),
                    TextInput::make('discount')->label(__(self::LANG.'.fields.discount'))->numeric()->minValue(0)->maxValue(100)->default(0),
                    Select::make('taxes')->label(__(self::LANG.'.fields.taxes'))->relationship('taxes', 'name', modifyQueryUsing: fn ($query) => $query->where('type_tax_use', TypeTaxUse::SALE)->where(function ($taxes): void {
                        $taxes->whereNull('company_id')->orWhere('company_id', current_company_id());
                    }))->multiple()->searchable()->preload(),
                ])->columns(3)->minItems(1)->addActionLabel(__(self::LANG.'.actions.add-line')),
            ]),
        ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['partner', 'invoiceUser']);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label(__(self::LANG.'.fields.name'))->searchable()->sortable(),
            TextColumn::make('partner.name')->label(__(self::LANG.'.fields.customer'))->searchable()->wrap(),
            TextColumn::make('frequency')->label(__(self::LANG.'.fields.frequency'))->badge(),
            TextColumn::make('next_invoice_date')->label(__(self::LANG.'.fields.next-date'))->date()->sortable(),
            TextColumn::make('state')->label(__(self::LANG.'.fields.status'))->badge(),
            TextColumn::make('last_failure_reason')->label(__(self::LANG.'.fields.last-issue'))->wrap()->placeholder('—'),
        ])->recordActions([
            Action::make('generateNow')
                ->label(__(self::LANG.'.actions.generate-now'))
                ->authorize(fn (RecurringInvoiceTemplate $record): bool => (bool) auth()->user()?->hasPermissionInCompany(RecurringInvoiceService::PERMISSION, (int) $record->company_id))
                ->requiresConfirmation()
                ->visible(fn (RecurringInvoiceTemplate $record): bool => (bool) auth()->user()?->hasPermissionInCompany(RecurringInvoiceService::PERMISSION, (int) $record->company_id))
                ->action(function (RecurringInvoiceTemplate $record): void {
                    app(RecurringInvoiceService::class)->generateNow($record, auth()->user());
                }),
            Action::make('pause')
                ->label(__(self::LANG.'.actions.pause'))
                ->authorize(fn (RecurringInvoiceTemplate $record): bool => (bool) auth()->user()?->hasPermissionInCompany('update_invoice_recurring::invoice::template', (int) $record->company_id))
                ->visible(fn (RecurringInvoiceTemplate $record): bool => $record->state === 'active')
                ->action(fn (RecurringInvoiceTemplate $record) => app(RecurringInvoiceService::class)->setState($record, 'paused', auth()->user())),
            Action::make('resume')
                ->label(__(self::LANG.'.actions.resume'))
                ->authorize(fn (RecurringInvoiceTemplate $record): bool => (bool) auth()->user()?->hasPermissionInCompany('update_invoice_recurring::invoice::template', (int) $record->company_id))
                ->visible(fn (RecurringInvoiceTemplate $record): bool => $record->state === 'paused')
                ->action(fn (RecurringInvoiceTemplate $record) => app(RecurringInvoiceService::class)->setState($record, 'active', auth()->user())),
            Action::make('stop')
                ->label(__(self::LANG.'.actions.stop'))
                ->color('danger')
                ->authorize(fn (RecurringInvoiceTemplate $record): bool => (bool) auth()->user()?->hasPermissionInCompany('update_invoice_recurring::invoice::template', (int) $record->company_id))
                ->requiresConfirmation()
                ->visible(fn (RecurringInvoiceTemplate $record): bool => in_array($record->state, ['active', 'paused'], true))
                ->action(fn (RecurringInvoiceTemplate $record) => app(RecurringInvoiceService::class)->setState($record, 'stopped', auth()->user())),
            Action::make('history')
                ->label(__(self::LANG.'.actions.history'))
                ->url(fn (RecurringInvoiceTemplate $record): string => static::getUrl('edit', ['record' => $record])),
        ])->headerActions([CreateAction::make()->label(__(self::LANG.'.actions.create'))])->defaultSort('next_invoice_date');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListRecurringInvoiceTemplates::route('/'),
            'create' => CreateRecurringInvoiceTemplate::route('/create'),
            'edit'   => EditRecurringInvoiceTemplate::route('/{record}/edit'),
        ];
    }

    public static function getRelations(): array
    {
        return [PeriodsRelationManager::class];
    }

    public static function canAccess(): bool
    {
        // A canAccess() written here replaces the resource's own, which asks
        // the policy (viewAny). The module check alone opened the list to
        // every user of an entitled company (AGENTS, canAccess trap).
        return PluginEntitlement::enabledForCurrent('invoices') && parent::canAccess();
    }
}

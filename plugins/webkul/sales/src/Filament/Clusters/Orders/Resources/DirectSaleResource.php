<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Journal;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Orders;
use Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource\Pages\CreateDirectSale;
use Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource\Pages\ListDirectSales;
use Webkul\Sale\Filament\Clusters\Orders\Resources\DirectSaleResource\Pages\PrintReceipt;
use Webkul\Sale\Models\Order;
use Webkul\Security\Traits\HasResourcePermissionQuery;

class DirectSaleResource extends Resource
{
    use HasResourcePermissionQuery;

    public const SALE_SOURCE = 'direct';

    protected static ?string $model = Order::class;

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $cluster = Orders::class;

    public static function getModelLabel(): string
    {
        return __('Direct Sale');
    }

    public static function getNavigationLabel(): string
    {
        return __('Direct Sale');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('sale_source', self::SALE_SOURCE);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Order header values required by the reused line repeater.
                Hidden::make('state')->default(OrderState::DRAFT->value),
                Hidden::make('sale_source')->default(self::SALE_SOURCE),
                Hidden::make('date_order')->default(fn () => now()),
                Hidden::make('validity_date')->default(fn () => now()),
                Hidden::make('company_id')->default(fn () => Auth::user()->default_company_id),
                Hidden::make('currency_id')->default(fn () => Auth::user()->defaultCompany?->currency_id),

                Section::make(__('Customer'))
                    ->icon('heroicon-o-user')
                    ->schema([
                        Select::make('partner_id')
                            ->label(__('Customer'))
                            ->relationship(
                                'partner',
                                'name',
                                modifyQueryUsing: fn (Builder $query) => $query->orderBy('id')->withTrashed(),
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder(__('Walk-In Customer (default)'))
                            ->helperText(__('Leave empty to assign the default Walk-In Customer.'))
                            ->getOptionLabelFromRecordUsing(fn ($record): string => $record->name.($record->trashed() ? ' (Deleted)' : ''))
                            ->columnSpanFull(),
                    ]),

                Tabs::make()
                    ->columnSpanFull()
                    ->schema([
                        Tab::make(__('Products'))
                            ->icon('heroicon-o-list-bullet')
                            ->schema([
                                QuotationResource::getProductRepeater(),
                            ]),
                    ]),

                Section::make(__('Payment'))
                    ->icon('heroicon-o-credit-card')
                    ->columns(2)
                    ->schema([
                        Select::make('payment_journal_id')
                            ->label(__('Payment Method'))
                            ->options(fn () => Journal::query()
                                ->whereIn('type', [JournalType::BANK->value, JournalType::CASH->value])
                                ->where('company_id', Auth::user()->default_company_id)
                                ->pluck('name', 'id'))
                            ->placeholder(__('Auto (first available)'))
                            ->helperText(__('Cash / bank journal used to record the payment.'))
                            ->dehydrated(true),
                        TextInput::make('paid_amount')
                            ->label(__('Paid Amount'))
                            ->numeric()
                            ->minValue(0)
                            ->helperText(__('Leave empty to pay the full amount. A lower value records a partial payment.'))
                            ->dehydrated(true),
                        Textarea::make('note')
                            ->label(__('Notes'))
                            ->columnSpanFull()
                            ->rows(2),
                    ]),
            ])
            ->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with(['partner', 'invoices']))
            ->columns([
                TextColumn::make('name')
                    ->label(__('Reference'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('Customer'))
                    ->searchable(),
                TextColumn::make('date_order')
                    ->label(__('Date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('amount_total')
                    ->label(__('Total'))
                    ->money(fn ($record) => $record->currency?->code)
                    ->sortable(),
                TextColumn::make('invoices.payment_state')
                    ->label(__('Payment'))
                    ->badge()
                    ->placeholder('—'),
                TextColumn::make('state')
                    ->label(__('Status'))
                    ->badge(),
            ])
            ->defaultSort('id', 'desc')
            ->recordActions([
                Action::make('receipt')
                    ->label(__('Receipt'))
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (Order $record): string => PrintReceipt::getUrl(['record' => $record]))
                    ->openUrlInNewTab(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'   => ListDirectSales::route('/'),
            'create'  => CreateDirectSale::route('/create'),
            'receipt' => PrintReceipt::route('/{record}/receipt'),
        ];
    }
}

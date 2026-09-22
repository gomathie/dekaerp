<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Enums\TypeTaxUse;
use Webkul\Logistics\Models\Shipment;
use Webkul\Product\Models\Product;

class ChargesRelationManager extends RelationManager
{
    protected static string $relationship = 'charges';

    protected static string $lang = 'logistics::invoicing.charges';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(static::$lang.'.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // Required, not merely offered. A charge with no product produces a
            // move line with no account, which accounting treats as a
            // non-product line: the invoice then computes untaxed, silently and
            // without an error. That is a way to under-bill a customer and
            // never notice.
            Select::make('product_id')
                ->label(__(static::$lang.'.fields.product'))
                ->options(fn (): array => static::serviceProducts($this->getOwnerRecord()))
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(function (Set $set, $state): void {
                    $product = $state ? Product::query()->find($state) : null;

                    if (! $product) {
                        return;
                    }

                    $set('description', $product->name);
                    $set('price_unit', $product->price);
                    $set('uom_id', $product->uom_id);
                }),

            TextInput::make('description')
                ->label(__(static::$lang.'.fields.description'))
                ->required()
                ->maxLength(255),

            TextInput::make('quantity')
                ->label(__(static::$lang.'.fields.quantity'))
                ->numeric()
                ->minValue(0)
                ->default(1)
                ->required(),

            TextInput::make('price_unit')
                ->label(__(static::$lang.'.fields.price-unit'))
                ->numeric()
                ->minValue(0)
                ->required(),

            TextInput::make('discount')
                ->label(__(static::$lang.'.fields.discount'))
                ->numeric()
                ->minValue(0)
                ->maxValue(100)
                ->default(0),

            // Only taxes that can actually compute. A tax without repartition
            // lines contributes nothing and, again, yields an untaxed invoice
            // with no error.
            Select::make('taxes')
                ->label(__(static::$lang.'.fields.taxes'))
                ->relationship('taxes', 'name', fn ($query) => $query
                    ->where('company_id', $this->getOwnerRecord()->company_id)
                    ->where('type_tax_use', TypeTaxUse::SALE)
                    ->whereHas('invoiceRepartitionLines'))
                ->multiple()
                ->preload(),

            Toggle::make('is_billable')
                ->label(__(static::$lang.'.fields.is-billable'))
                ->default(true)
                ->helperText(__(static::$lang.'.fields.is-billable-helper')),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label(__(static::$lang.'.fields.description'))
                    ->searchable(),

                TextColumn::make('quantity')
                    ->label(__(static::$lang.'.fields.quantity'))
                    ->numeric(),

                TextColumn::make('price_unit')
                    ->label(__(static::$lang.'.fields.price-unit'))
                    ->money(fn (Model $record): ?string => $record->currency?->code)
                    ->visibleFrom('sm'),

                IconColumn::make('is_billable')
                    ->label(__(static::$lang.'.fields.is-billable'))
                    ->boolean()
                    ->visibleFrom('md'),

                // Shows at a glance what a new invoice would pick up.
                IconColumn::make('move_line_id')
                    ->label(__(static::$lang.'.fields.invoiced'))
                    ->boolean()
                    ->visibleFrom('md'),
            ])
            ->headerActions([
                CreateAction::make()
                    ->visible(fn (): bool => Auth::user()?->can('update', $this->getOwnerRecord()) ?? false),
            ])
            ->recordActions([
                // An invoiced charge is part of a posted accounting document.
                // Editing or deleting it here would put the invoice and the
                // charge out of step, so both stop at that point.
                EditAction::make()
                    ->visible(fn (Model $record): bool => $record->move_line_id === null
                        && (Auth::user()?->can('update', $this->getOwnerRecord()) ?? false)),

                DeleteAction::make()
                    ->visible(fn (Model $record): bool => $record->move_line_id === null
                        && (Auth::user()?->can('update', $this->getOwnerRecord()) ?? false)),
            ]);
    }

    /**
     * The company's own service products (LOG-*, plus anything it added).
     *
     * @return array<int, string>
     */
    protected static function serviceProducts(Shipment $shipment): array
    {
        return Product::query()
            ->where('company_id', $shipment->company_id)
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}

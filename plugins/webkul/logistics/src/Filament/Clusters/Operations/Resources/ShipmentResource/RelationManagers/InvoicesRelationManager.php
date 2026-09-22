<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Resources\InvoiceResource;

/**
 * The invoices raised from this shipment's charges.
 *
 * Read-only on purpose. Invoices belong to Accounting: their state, payment and
 * numbering are managed there, and the row links out to the existing invoice
 * screen rather than reproducing any of it here.
 */
class InvoicesRelationManager extends RelationManager
{
    protected static string $relationship = 'invoices';

    protected static string $lang = 'logistics::invoicing.invoices';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(static::$lang.'.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__(static::$lang.'.columns.number'))
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('state')
                    ->label(__(static::$lang.'.columns.state'))
                    ->badge(),

                TextColumn::make('payment_state')
                    ->label(__(static::$lang.'.columns.payment-state'))
                    ->badge()
                    ->placeholder('—')
                    ->visibleFrom('sm'),

                TextColumn::make('invoice_date')
                    ->label(__(static::$lang.'.columns.date'))
                    ->date()
                    ->placeholder('—')
                    ->visibleFrom('md'),

                TextColumn::make('amount_total')
                    ->label(__(static::$lang.'.columns.total'))
                    ->money(fn (Model $record): ?string => $record->currency?->code)
                    ->visibleFrom('sm'),
            ])
            ->recordActions([
                Action::make('openInvoice')
                    ->label(__(static::$lang.'.actions.open'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Model $record): string => InvoiceResource::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab()
                    // Accounting's own permission decides this, not ours.
                    ->visible(fn (Model $record): bool => Auth::user()?->can('view', $record) ?? false),
            ]);
    }
}

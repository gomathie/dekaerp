<?php

namespace Webkul\Invoice\Filament\Clusters\Customers\Resources\RecurringInvoiceTemplateResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PeriodsRelationManager extends RelationManager
{
    protected static string $relationship = 'periods';

    public function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('period_start')->label(__('invoices::recurring.fields.period-start'))->date()->sortable(),
            TextColumn::make('period_end')->label(__('invoices::recurring.fields.period-end'))->date(),
            TextColumn::make('status')->label(__('invoices::recurring.fields.status'))->badge(),
            TextColumn::make('invoice.name')->label(__('invoices::recurring.fields.invoice'))->placeholder('—'),
            TextColumn::make('failure_reason')->label(__('invoices::recurring.fields.last-issue'))->wrap()->placeholder('—'),
        ]);
    }
}

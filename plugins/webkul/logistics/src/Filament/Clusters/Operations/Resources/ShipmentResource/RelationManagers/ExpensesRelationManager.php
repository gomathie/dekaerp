<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\ExpenseState;

/**
 * What this shipment cost (D2).
 *
 * Read-only here on purpose. An expense belongs to the Finance cluster, where it
 * has its approval flow, its receipt rules and its own permissions; duplicating
 * the form on the shipment page would give two ways to create one and two places
 * for the rules to drift. This answers "what has this job cost me", and links out
 * for anything more.
 *
 * Behind view_financials, like the charges and the unbilled figures: an
 * operations user who may see the shipment has no business seeing its margin.
 */
class ExpensesRelationManager extends RelationManager
{
    protected static string $relationship = 'expenses';

    protected static string $lang = 'logistics::expenses.relation-manager';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return Auth::user()?->can('viewFinancials', $ownerRecord) ?? false;
    }

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __(static::$lang.'.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label(__(static::$lang.'.fields.date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__(static::$lang.'.fields.category'))
                    ->searchable(),

                TextColumn::make('payee.name')
                    ->label(__(static::$lang.'.fields.payee'))
                    ->description(fn (Model $record): ?string => $record->paid_by?->getLabel())
                    ->searchable()
                    ->visibleFrom('sm'),

                TextColumn::make('amount')
                    ->label(__(static::$lang.'.fields.amount'))
                    ->money(fn (Model $record): ?string => $record->currency?->name)
                    ->sortable()
                    // Approved and billed costs only: a draft expense is a
                    // claim, not yet money the company has agreed to.
                    ->summarize(
                        \Filament\Tables\Columns\Summarizers\Sum::make()
                            ->label(__(static::$lang.'.fields.approved-total'))
                            ->query(fn ($query) => $query->whereIn('state', [
                                ExpenseState::APPROVED->value,
                                ExpenseState::BILLED->value,
                            ])),
                    ),

                TextColumn::make('state')
                    ->label(__(static::$lang.'.fields.state'))
                    ->badge()
                    ->visibleFrom('md'),

                // Which costs have reached a vendor bill, so it is clear at a
                // glance what a posting run would still pick up.
                TextColumn::make('billMove.name')
                    ->label(__(static::$lang.'.fields.bill'))
                    ->placeholder(__(static::$lang.'.fields.not-billed'))
                    ->visibleFrom('lg'),
            ])
            ->defaultSort('date', 'desc');
    }
}

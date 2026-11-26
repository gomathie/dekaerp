<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Carbon;
use Webkul\Account\Services\ExpenseRecorder;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Actions\DeleteExpenseAction;
use Webkul\Invoice\Filament\Exports\ExpenseExporter;
use Webkul\Invoice\Models\Expense;
use Webkul\Support\Support\CompanyCurrencyEquivalent;

/**
 * The approved Expenses list (plan #10 Z1). Each value carries its column
 * label (HasFilamentDefaults), which is what the phone cards show.
 */
class ExpensesTable
{
    protected const LANG = ExpenseResource::LANG.'.table';

    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('date')
                    ->label(__(self::LANG.'.columns.date'))
                    ->date()
                    // The number under the date, as on the other money lists (R-9).
                    ->description(fn (Expense $record): string => $record->number)
                    ->searchable(['number'])
                    ->sortable(),
                TextColumn::make('expenseAccount.name')
                    ->label(__(self::LANG.'.columns.expense-account'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('journal.name')
                    ->label(__(self::LANG.'.columns.paid-from'))
                    ->badge()
                    ->color('info'),
                TextColumn::make('partner.name')
                    ->label(__(self::LANG.'.columns.vendor'))
                    ->placeholder('—')
                    ->searchable()
                    ->visibleFrom('md'),
                TextColumn::make('reference')
                    ->label(__(self::LANG.'.columns.reference'))
                    ->placeholder('—')
                    ->searchable()
                    ->visibleFrom('lg'),
                TextColumn::make('amount_total')
                    ->label(__(self::LANG.'.columns.amount'))
                    ->money(fn (Expense $record): ?string => $record->currency?->name)
                    // A foreign-currency expense shows its company value under it.
                    ->description(fn (Expense $record): ?string => CompanyCurrencyEquivalent::short((float) $record->amount_total_company, $record->currency_id, $record->company))
                    ->alignEnd()
                    ->sortable()
                    // The total is always the company-currency value: expenses
                    // in several currencies cannot be added up as they are.
                    ->summarize(
                        Summarizer::make()
                            ->label(__(self::LANG.'.total'))
                            ->using(fn (QueryBuilder $query): float => (float) $query->sum('amount_total_company'))
                            ->formatStateUsing(fn ($state): string => money((float) $state, current_company()?->currency?->name)),
                    ),
                IconColumn::make('receipt_path')
                    ->label(__(self::LANG.'.columns.receipt'))
                    ->icon(fn (?string $state): ?string => filled($state) ? 'heroicon-o-paper-clip' : null)
                    ->tooltip(fn (Expense $record): ?string => $record->receipt_name),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                Filter::make('period')
                    ->label(__(self::LANG.'.filters.period'))
                    ->schema([
                        DatePicker::make('from')
                            ->label(__(self::LANG.'.filters.from'))
                            ->native(false)
                            ->default(now()->startOfMonth()),
                        DatePicker::make('until')
                            ->label(__(self::LANG.'.filters.until'))
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date) => $query->whereDate('date', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, $date) => $query->whereDate('date', '<=', $date)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = __(self::LANG.'.filters.from').' '.Carbon::parse($data['from'])->toFormattedDateString();
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = __(self::LANG.'.filters.until').' '.Carbon::parse($data['until'])->toFormattedDateString();
                        }

                        return $indicators;
                    }),
                SelectFilter::make('journal_id')
                    ->label(__(self::LANG.'.filters.paid-from'))
                    ->options(fn (): array => app(ExpenseRecorder::class)->journalsQuery((int) current_company_id())->pluck('name', 'id')->all()),
                SelectFilter::make('expense_account_id')
                    ->label(__(self::LANG.'.filters.expense-account'))
                    ->options(fn (): array => app(ExpenseRecorder::class)->accountsQuery((int) current_company_id())->orderBy('code')->pluck('name', 'id')->all())
                    ->searchable(),
                SelectFilter::make('partner_id')
                    ->label(__(self::LANG.'.filters.vendor'))
                    ->relationship('partner', 'name')
                    ->searchable(),
                Filter::make('with_receipt')
                    ->label(__(self::LANG.'.filters.with-receipt'))
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('receipt_path')),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteExpenseAction::make(),
                ]),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->label(__(self::LANG.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(ExpenseExporter::class),
            ]);
    }
}

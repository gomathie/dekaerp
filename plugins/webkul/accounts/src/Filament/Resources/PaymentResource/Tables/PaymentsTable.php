<?php

namespace Webkul\Account\Filament\Resources\PaymentResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Account\Filament\Exports\PaymentExporter;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Models\Payment;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columns(PaymentResource::mergeCustomTableColumns([
                // Shown under the number now (plan #9 R-9, as the invoice list);
                // still available as its own column.
                TextColumn::make('date')
                    ->label(__('accounts::filament/resources/payment.table.columns.date'))
                    ->visibleFrom('sm')
                    ->placeholder('-')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                // Two-line cells (R-9): the payment date under the number, the
                // contact's company under the contact; also what a phone shows.
                TextColumn::make('name')
                    ->label(__('accounts::filament/resources/payment.table.columns.name'))
                    ->weight('semibold')
                    ->description(fn ($record): ?string => $record->date?->translatedFormat('d M Y'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('journal.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.journal'))
                    ->visibleFrom('md')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('paymentMethod.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.payment-method'))
                    // "Mobile Money" rather than "Manual Payment" (Payment::methodLabel()).
                    ->state(fn ($record): string => $record->methodLabel())
                    ->visibleFrom('lg')
                    ->placeholder('-')
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.partner'))
                    ->description(fn ($record): ?string => $record->partner?->parent?->name)
                    ->placeholder('-')
                    ->sortable(),
                // Which invoice or bill a payment settled (TRACE365, 2026-11-23): they
                // read the list to work out their taxes and could not see it.
                TextColumn::make('invoices.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.invoices'))
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('memo')
                    ->label(__('accounts::filament/resources/payment.form.sections.fields.memo'))
                    ->visibleFrom('md')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                // Company currency, so the filtered total adds like with like.
                TextColumn::make('amount_company_currency_signed')
                    ->label(__('accounts::filament/resources/payment.table.columns.amount-currency'))
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn (Payment $record) => $record->company?->currency_code, true)
                    ->summarize(Sum::make()
                        ->label(__('accounts::filament/resources/payment.table.summaries.total'))
                        ->money(fn (): ?string => current_company()?->currency?->name)),
                // The payment's own currency: a USD payment showed its dollar amount
                // with the company's cedi sign.
                TextColumn::make('amount')
                    ->label(__('accounts::filament/resources/payment.table.columns.amount'))
                    ->visibleFrom('lg')
                    ->placeholder('-')
                    ->sortable()
                    ->money(fn (Payment $record) => $record->currency?->name ?? $record->company?->currency_code, true),
                // Not yet matched to an invoice or bill (TRACE365, 2026-11-24),
                // from Payment::scopeWithUnusedAmount(), in the payment's currency.
                TextColumn::make('unused_amount')
                    ->label(__('accounts::filament/resources/payment.table.columns.unused'))
                    ->state(fn (Payment $record): ?float => $record->unusedAmount())
                    ->visibleFrom('md')
                    ->placeholder('-')
                    ->money(fn (Payment $record) => $record->currency?->name ?? $record->company?->currency_code, true),
                TextColumn::make('state')
                    ->label(__('accounts::filament/resources/payment.table.columns.state'))
                    ->placeholder('-')
                    ->sortable()
                    ->badge(),
                TextColumn::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.columns.company'))
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]))
            // Newest first as the table default, not as orderByDesc in getEloquentQuery():
            // that ran before any column sort, so clicking a header did nothing (upstream #1671).
            ->defaultSort('id', 'desc')
            // partner.parent: the company line under each contact, without one
            // query per row.
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->withUnusedAmount()->with('partner.parent'))
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/payment.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('company.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.company'))
                    ->collapsible(),
                Tables\Grouping\Group::make('journal.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.journal'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethodLine.id')
                    ->label(__('accounts::filament/resources/payment.table.groups.payment-method-line'))
                    ->getTitleFromRecordUsing(fn ($record) => $record->paymentMethodLine?->display_name)
                    ->collapsible(),
                Tables\Grouping\Group::make('partner.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.partner'))
                    ->collapsible(),
                Tables\Grouping\Group::make('paymentMethod.name')
                    ->label(__('accounts::filament/resources/payment.table.groups.payment-method'))
                    ->collapsible(),
                Tables\Grouping\Group::make('partnerBank.account_holder_name')
                    ->label(__('accounts::filament/resources/payment.table.groups.partner-bank-account'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/payment.table.groups.created-at'))
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/payment.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters(PaymentResource::mergeCustomTableFilters([
                // Month and year in two clicks (TRACE365, 2026-11-23): payments received
                // per month are what they compute their taxes from. On the payment date,
                // as a date range so the index on `date` is used.
                Filter::make('period')
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        Select::make('year')
                            ->label(__('accounts::filament/resources/payment.table.filters.year'))
                            ->options(fn (): array => PaymentsTable::yearOptions())
                            ->placeholder(__('accounts::filament/resources/payment.table.filters.any-year')),
                        Select::make('month')
                            ->label(__('accounts::filament/resources/payment.table.filters.month'))
                            ->options(fn (): array => PaymentsTable::monthOptions())
                            ->placeholder(__('accounts::filament/resources/payment.table.filters.whole-year')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        [$start, $end] = PaymentsTable::periodRange($data['year'] ?? null, $data['month'] ?? null);

                        return $start === null
                            ? $query
                            : $query->whereBetween($query->qualifyColumn('date'), [$start->toDateString(), $end->toDateString()]);
                    })
                    ->indicateUsing(function (array $data): ?string {
                        [$start] = PaymentsTable::periodRange($data['year'] ?? null, $data['month'] ?? null);

                        if ($start === null) {
                            return null;
                        }

                        return blank($data['month'] ?? null)
                            ? $start->format('Y')
                            : $start->translatedFormat('F Y');
                    }),
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        RelationshipConstraint::make('journal')
                            ->label(__('accounts::filament/resources/payment.table.filters.journal'))
                            ->icon('heroicon-o-book-open')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.journal'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('accounts::filament/resources/payment.table.filters.company'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.company'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partnerBank')
                            ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('account_number')
                                    ->label(__('accounts::filament/resources/payment.table.filters.customer-bank-account'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('paymentMethodLine')
                            ->label(__('accounts::filament/resources/payment.table.filters.payment-method-line'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.payment-method-line'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('paymentMethod')
                            ->label(__('accounts::filament/resources/payment.table.filters.payment-method'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.payment-method'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('currency')
                            ->label(__('accounts::filament/resources/payment.table.filters.currency'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.currency'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('partner')
                            ->label(__('accounts::filament/resources/payment.table.filters.partner'))
                            ->icon('heroicon-o-user')
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->label(__('accounts::filament/resources/payment.table.filters.partner'))
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('created_at')
                            ->label(__('accounts::filament/resources/payment.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounts::filament/resources/payment.table.filters.updated-at')),
                    ]),
            ]))
            ->recordActions([
                ActivityTableAction::make(),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('accounts::filament/resources/payment.table.actions.delete.notification.title'))
                            ->body(__('accounts::filament/resources/payment.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/payment.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/payment.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
                ExportAction::make()
                    ->label(__('accounts::filament/resources/payment.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(PaymentExporter::class),
            ]);
    }

    /**
     * The first and last day of the chosen year, or of one month in it. A month
     * with no year means the current year; neither means no period.
     *
     * @return array{0: ?Carbon, 1: ?Carbon}
     */
    public static function periodRange(mixed $year, mixed $month): array
    {
        if (blank($year) && blank($month)) {
            return [null, null];
        }

        $year = blank($year) ? now()->year : (int) $year;

        if (blank($month)) {
            $start = Carbon::create($year, 1, 1);

            return [$start, $start->copy()->endOfYear()];
        }

        $start = Carbon::create($year, max(1, min(12, (int) $month)), 1);

        return [$start, $start->copy()->endOfMonth()];
    }

    /**
     * This year and the nine before it, newest first.
     *
     * @return array<int, string>
     */
    public static function yearOptions(): array
    {
        $years = range(now()->year, now()->year - 9);

        return array_combine($years, array_map('strval', $years));
    }

    /**
     * @return array<int, string>
     */
    public static function monthOptions(): array
    {
        $months = [];

        foreach (range(1, 12) as $month) {
            $months[$month] = Carbon::create(2000, $month, 1)->translatedFormat('F');
        }

        return $months;
    }
}

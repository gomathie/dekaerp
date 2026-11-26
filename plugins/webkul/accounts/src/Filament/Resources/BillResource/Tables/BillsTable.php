<?php

namespace Webkul\Account\Filament\Resources\BillResource\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\QueryBuilder;
use Filament\Tables\Filters\QueryBuilder\Constraints\BooleanConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\DateConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\NumberConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint;
use Filament\Tables\Filters\QueryBuilder\Constraints\RelationshipConstraint\Operators\IsRelatedToOperator;
use Filament\Tables\Filters\QueryBuilder\Constraints\TextConstraint;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder as BaseQueryBuilder;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Filament\Exports\BillExporter;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;
use Webkul\Support\Support\CompanyCurrencyEquivalent;

class BillsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->reorderableColumns()
            ->columns(BillResource::mergeCustomTableColumns([
                // Number with its bill date under it, as on the invoices list
                // (plan 10, item 3): one column fewer, so the money columns fit
                // a 1440 px screen without scrolling sideways.
                TextColumn::make('name')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.number'))
                    ->weight('semibold')
                    ->description(fn ($record): ?string => $record->invoice_date?->translatedFormat('d M Y'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('state')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.state'))
                    ->visibleFrom('sm')
                    ->sortable()
                    ->badge()
                    ->toggleable(isToggledHiddenByDefault: false),
                // A bill's partner is the vendor; the heading said "Customer"
                // (plan 10, item 3).
                TextColumn::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/bill.table.columns.vendor'))
                    // The company a contact works for, under their name (plan #9 R-9).
                    ->description(fn ($record): ?string => $record->partner?->parent?->name)
                    ->placeholder('-')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice_date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.bill-date'))
                    ->visibleFrom('md')
                    ->sortable()
                    // Shown under the number now; still available as its own column.
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_date_due')
                    ->state(function ($record) {
                        if ($record->payment_state == PaymentState::PAID) {
                            return null;
                        }

                        if (! $record->invoice_date_due) {
                            return '-';
                        }

                        if ($record->invoice_date_due->isToday()) {
                            return 'Today';
                        }

                        return $record->invoice_date_due->diffForHumans();
                    })
                    ->color(function ($record) {
                        if ($record->payment_state == PaymentState::PAID) {
                            return null;
                        }

                        if (! $record->invoice_date_due) {
                            return null;
                        }

                        if ($record->invoice_date_due->isToday()) {
                            return 'warning';
                        }

                        if ($record->invoice_date_due->isPast()) {
                            return 'danger';
                        }

                        return null;
                    })
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.due-date'))
                    ->visibleFrom('sm')
                    ->sortable(),
                // Amounts shown positive (plan 10, item 3): a bill's signed
                // columns are negative, so the list read "-$2,750.00" for money
                // owed. Sorting and searching still use the stored column.
                // Tax excluded and Tax start hidden so the money columns fit a
                // 1440 px screen; both stay in the column picker.
                TextColumn::make('amount_untaxed_in_currency_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.tax-excluded'))
                    ->visibleFrom('lg')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->state(fn ($record): float => abs((float) $record->amount_untaxed_in_currency_signed))
                    ->money(fn ($record) => $record->currency?->name)
                    ->summarize(static::positiveSum('amount_untaxed_in_currency_signed', 'accounts::filament/resources/bill.table.total'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_tax_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.tax'))
                    ->visibleFrom('lg')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->state(fn ($record): float => abs((float) $record->amount_tax_signed))
                    ->money(fn ($record) => $record->currency?->name)
                    ->summarize(static::positiveSum('amount_tax_signed', 'accounts::filament/resources/bill.table.total'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('amount_total_in_currency_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.total'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->state(fn ($record): float => abs((float) $record->amount_total_in_currency_signed))
                    ->summarize(static::positiveSum('amount_total_in_currency_signed', 'accounts::filament/resources/bill.table.total'))
                    ->money(fn ($record) => $record->currency?->name)
                    // A foreign-currency bill shows its company-currency value
                    // under it (TRACE365, 2026-11-23).
                    ->description(fn ($record): ?string => CompanyCurrencyEquivalent::short(abs((float) $record->amount_total_signed), $record->currency_id, $record->company))
                    ->toggleable(isToggledHiddenByDefault: false),
                // Paid in green, as on the invoices list (plan 10, item 3), in
                // the bill's own currency.
                TextColumn::make('amount_paid')
                    ->label(__('accounts::filament/resources/bill.table.columns.amount-paid'))
                    ->visibleFrom('lg')
                    ->state(fn ($record): float => max(0, abs((float) $record->amount_total) - abs((float) $record->amount_residual)))
                    ->formatStateUsing(fn ($state, $record): string => (float) $state > 0 ? money((float) $state, $record->currency?->name) : '-')
                    ->color(fn ($state): ?string => (float) $state > 0 ? 'success' : null)
                    ->toggleable(isToggledHiddenByDefault: false),
                // The bill's own-currency balance, as on the invoices list: the
                // stored signed column is in company currency.
                TextColumn::make('amount_residual_signed')
                    ->label(__('accounts::filament/resources/bill.table.columns.amount-due'))
                    ->visibleFrom('md')
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->state(fn ($record): float => abs((float) $record->amount_residual))
                    ->summarize(static::positiveSum('amount_residual_signed', 'accounts::filament/resources/bill.table.summarizers.total'))
                    ->money(fn ($record) => $record->currency?->name)
                    ->color(fn ($state): ?string => (float) $state > 0 ? 'danger' : null)
                    ->toggleable(isToggledHiddenByDefault: false),
                TextColumn::make('payment_state')
                    ->label(__('Payment State'))
                    ->placeholder('-')
                    ->color(fn (PaymentState $state) => $state->getColor())
                    ->icon(fn (PaymentState $state) => $state->getIcon())
                    ->formatStateUsing(fn (PaymentState $state) => $state->getLabel())
                    ->badge()
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                IconColumn::make('checked')
                    ->boolean()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.checked'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('date')
                    ->date()
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.accounting-date'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoice_origin')
                    ->placeholder('-')
                    ->label(__('accounts::filament/resources/bill.table.columns.source-document'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('reference')
                    ->label(__('accounts::filament/resources/bill.table.columns.reference'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('invoiceUser.name')
                    ->label(__('accounts::filament/resources/bill.table.columns.sales-person'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('currency.name')
                    ->label(__('accounts::filament/resources/bill.table.columns.bill-currency'))
                    ->searchable()
                    ->placeholder('-')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ]))
            // Newest first as the table default, not as orderByDesc in getEloquentQuery():
            // that ran before any column sort, so clicking a header did nothing (upstream #1671).
            ->defaultSort('id', 'desc')
            ->groups([
                Tables\Grouping\Group::make('name')
                    ->label(__('accounts::filament/resources/bill.table.groups.name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_partner_display_name')
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-partner-display-name'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date')
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('checked')
                    ->label(__('accounts::filament/resources/bill.table.groups.checked'))
                    ->collapsible(),
                Tables\Grouping\Group::make('date')
                    ->date()
                    ->label(__('accounts::filament/resources/bill.table.groups.date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_date_due')
                    ->date()
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-due-date'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoice_origin')
                    ->label(__('accounts::filament/resources/bill.table.groups.bill-origin'))
                    ->collapsible(),
                Tables\Grouping\Group::make('invoiceUser.name')
                    ->date()
                    ->label(__('accounts::filament/resources/bill.table.groups.sales-person'))
                    ->collapsible(),
                Tables\Grouping\Group::make('currency.name')
                    ->label(__('accounts::filament/resources/bill.table.groups.currency'))
                    ->collapsible(),
                Tables\Grouping\Group::make('created_at')
                    ->label(__('accounts::filament/resources/bill.table.groups.created-at'))
                    ->date()
                    ->collapsible(),
                Tables\Grouping\Group::make('updated_at')
                    ->label(__('accounts::filament/resources/bill.table.groups.updated-at'))
                    ->date()
                    ->collapsible(),
            ])
            ->filtersFormColumns(2)
            ->filters(BillResource::mergeCustomTableFilters([
                QueryBuilder::make()
                    ->constraintPickerColumns(2)
                    ->constraints([
                        TextConstraint::make('name')
                            ->label(__('accounts::filament/resources/bill.table.filters.number')),
                        TextConstraint::make('invoice_origin')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-origin')),
                        TextConstraint::make('reference')
                            ->label(__('accounts::filament/resources/bill.table.filters.reference')),
                        TextConstraint::make('invoice_partner_display_name')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-partner-display-name')),
                        TextConstraint::make('payment_reference')
                            ->label(__('accounts::filament/resources/bill.table.filters.payment-reference')),
                        TextConstraint::make('narration')
                            ->label(__('accounts::filament/resources/bill.table.filters.narration')),
                        RelationshipConstraint::make('partner')
                            ->label(__('accounts::filament/resources/bill.table.filters.partner'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('journal')
                            ->label(__('accounts::filament/resources/bill.table.filters.journal'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('fiscalPosition')
                            ->label(__('accounts::filament/resources/bill.table.filters.fiscal-position'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('currency')
                            ->label(__('accounts::filament/resources/bill.table.filters.currency'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        RelationshipConstraint::make('company')
                            ->label(__('accounts::filament/resources/bill.table.filters.company'))
                            ->multiple()
                            ->selectable(
                                IsRelatedToOperator::make()
                                    ->titleAttribute('name')
                                    ->searchable()
                                    ->multiple()
                                    ->preload(),
                            ),
                        DateConstraint::make('date')
                            ->label(__('accounts::filament/resources/bill.table.filters.date')),
                        DateConstraint::make('invoice_date')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-date')),
                        DateConstraint::make('invoice_date_due')
                            ->label(__('accounts::filament/resources/bill.table.filters.bill-due-date')),
                        DateConstraint::make('delivery_date')
                            ->label(__('accounts::filament/resources/bill.table.filters.delivery-date')),
                        NumberConstraint::make('amount_untaxed')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-untaxed')),
                        NumberConstraint::make('amount_tax')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-tax')),
                        NumberConstraint::make('amount_total')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-total')),
                        NumberConstraint::make('amount_residual')
                            ->label(__('accounts::filament/resources/bill.table.filters.amount-residual')),
                        BooleanConstraint::make('checked')
                            ->label(__('accounts::filament/resources/bill.table.filters.checked')),
                        BooleanConstraint::make('posted_before')
                            ->label(__('accounts::filament/resources/bill.table.filters.posted-before')),
                        BooleanConstraint::make('is_move_sent')
                            ->label(__('accounts::filament/resources/bill.table.filters.is-move-sent')),
                        DateConstraint::make('created_at')
                            ->label(__('accounts::filament/resources/bill.table.filters.created-at')),
                        DateConstraint::make('updated_at')
                            ->label(__('accounts::filament/resources/bill.table.filters.updated-at')),
                    ]),
            ]))
            ->recordActions([
                ActivityTableAction::make(),
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                        ->hidden(fn (Model $record): bool => $record->state == MoveState::POSTED)
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/bill.table.actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/bill.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/resources/bill.table.bulk-actions.delete.notification.title'))
                                ->body(__('accounts::filament/resources/bill.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
                ExportAction::make()
                    ->label(__('accounts::filament/resources/bill.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(BillExporter::class),
            ])
            ->checkIfRecordIsSelectableUsing(
                fn (Model $record): bool => BillResource::can('delete', $record) && $record->state !== MoveState::POSTED,
            )
            ->modifyQueryUsing(function (Builder $query) {
                $query->with('currency', 'company.currency', 'partner.parent');
            });
    }

    /**
     * The column's total, positive like its rows (plan 10, item 3). A plain
     * summarizer with its own query, so the batched sum of the signed column
     * cannot bring the minus sign back.
     */
    protected static function positiveSum(string $column, string $labelKey): Summarizer
    {
        return Summarizer::make()
            ->label(__($labelKey))
            ->using(fn (BaseQueryBuilder $query): float => abs((float) $query->sum($column)))
            ->numeric();
    }
}

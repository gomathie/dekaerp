<?php

namespace Webkul\Accounting\Filament\Clusters\Accounting\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\BankStatementLine;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Payment;
use Webkul\Accounting\Exceptions\CannotReconcile;
use Webkul\Accounting\Filament\Clusters\Accounting;
use Webkul\Accounting\Services\BankReconciliation as Reconciliation;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;
use Webkul\Support\Filament\ImportUpload;
use Webkul\Support\Services\CompanyContext;

/**
 * Bank reconciliation (accounting plan A3). Import a CSV statement, then
 * match each line to the payment it confirms or book it to an account.
 * All the rules are in Services\BankReconciliation; this page only collects
 * input and shows the result.
 */
class BankReconciliation extends Page implements HasTable
{
    use ChecksPagePermission, HasPageShield, InteractsWithTable;

    protected const LANG = 'accounting::filament/clusters/accounting/pages/bank-reconciliation';

    protected string $view = 'accounting::filament.clusters.accounting.pages.bank-reconciliation';

    protected static ?string $cluster = Accounting::class;

    protected static ?string $slug = 'bank-reconciliation';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static ?int $navigationSort = 3;

    protected static function getPagePermission(): ?string
    {
        return Reconciliation::PERMISSION;
    }

    /**
     * Module switch and the page's own permission; a canAccess() here
     * replaces HasPageShield's, so the permission is checked explicitly.
     */
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('accounting')
            && static::hasPagePermission();
    }

    public static function getNavigationLabel(): string
    {
        return __(self::LANG.'.navigation.title');
    }

    public function getTitle(): string
    {
        return __(self::LANG.'.navigation.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label(__(self::LANG.'.import.label'))
                ->icon('heroicon-o-arrow-up-tray')
                ->modalDescription(__(self::LANG.'.import.description'))
                ->schema([
                    Select::make('journal_id')
                        ->label(__(self::LANG.'.import.journal'))
                        ->options(fn (): array => $this->journalOptions())
                        ->required(),
                    FileUpload::make('file')
                        ->label(__(self::LANG.'.import.file'))
                        ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', 'application/csv'])
                        // The browser hint above is trivially bypassed; the
                        // `mimes` rule sniffs the content server-side.
                        ->rules(['file', 'mimes:csv,txt', 'max:2048'])
                        ->maxSize(2048)
                        ->required(),
                    TextInput::make('name')
                        ->label(__(self::LANG.'.import.name'))
                        ->maxLength(255),
                    Grid::make(2)->schema([
                        Select::make('date_format')
                            ->label(__(self::LANG.'.import.date-format'))
                            ->options(array_combine(Reconciliation::DATE_FORMATS, ['2026-11-30', '30/11/2026', '11/30/2026', '30.11.2026', '30-11-2026']))
                            ->default('Y-m-d')
                            ->required(),
                        Toggle::make('has_header')
                            ->label(__(self::LANG.'.import.has-header'))
                            ->default(true)
                            ->inline(false),
                    ]),
                    Grid::make(4)->schema([
                        $this->columnInput('date', true, 1),
                        $this->columnInput('label', true, 2),
                        $this->columnInput('amount', false, 3)
                            ->helperText(__(self::LANG.'.import.amount-help')),
                        $this->columnInput('money_in'),
                        $this->columnInput('money_out'),
                        $this->columnInput('partner'),
                        $this->columnInput('reference'),
                    ]),
                ])
                ->action(function (array $data, Reconciliation $service): void {
                    $resolved = ImportUpload::resolve($data['file'] ?? null, 'bank-statement-');

                    if ($resolved === null) {
                        Notification::make()->danger()->title(__('accounting::bank-reconciliation.errors.empty-file'))->send();

                        return;
                    }

                    [$path, , $isTemporaryCopy] = $resolved;

                    try {
                        $journal = Journal::query()->findOrFail($data['journal_id']);

                        $map = ['date_format' => $data['date_format'], 'has_header' => (bool) $data['has_header']];

                        foreach (['date', 'label', 'amount', 'money_in', 'money_out', 'partner', 'reference'] as $field) {
                            $map[$field] = filled($data['col_'.$field] ?? null) ? ((int) $data['col_'.$field]) - 1 : null;
                        }

                        if ($map['amount'] === null && $map['money_in'] === null && $map['money_out'] === null) {
                            throw CannotReconcile::badRow(1, __('accounting::bank-reconciliation.fields.amount'));
                        }

                        $statement = $service->import($journal, $path, array_filter($map, fn ($value) => $value !== null));

                        Notification::make()
                            ->success()
                            ->title(__(self::LANG.'.import.done', [
                                'count' => BankStatementLine::query()->where('statement_id', $statement->id)->count(),
                            ]))
                            ->send();
                    } catch (CannotReconcile $exception) {
                        Notification::make()->danger()->title($exception->getMessage())->send();
                    } finally {
                        // The statement is in the database now; a copy of the
                        // file holds bank data and is not kept.
                        if ($isTemporaryCopy) {
                            @unlink($path);
                        }
                    }
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => BankStatementLine::query()
                ->with(['journal', 'currency', 'payment'])
                ->whereIn('company_id', app(CompanyContext::class)->activeIds()))
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label(__(self::LANG.'.columns.date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('payment_reference')
                    ->label(__(self::LANG.'.columns.label'))
                    ->searchable()
                    ->wrap(),
                TextColumn::make('partner_name')
                    ->label(__(self::LANG.'.columns.partner'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('journal.name')
                    ->label(__(self::LANG.'.columns.journal'))
                    ->toggleable(),
                TextColumn::make('amount')
                    ->label(__(self::LANG.'.columns.amount'))
                    ->money(fn (BankStatementLine $record): ?string => $record->currency?->name)
                    ->color(fn (BankStatementLine $record): string => (float) $record->amount < 0 ? 'danger' : 'success')
                    ->alignEnd()
                    ->sortable(),
                TextColumn::make('is_reconciled')
                    ->label(__(self::LANG.'.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn (bool $state): string => __(self::LANG.'.status.'.($state ? 'reconciled' : 'to-reconcile')))
                    ->color(fn (bool $state): string => $state ? 'success' : 'warning'),
                TextColumn::make('payment.name')
                    ->label(__(self::LANG.'.columns.payment'))
                    ->placeholder('—')
                    ->toggleable(),
            ])
            ->filters([
                TernaryFilter::make('is_reconciled')
                    ->label(__(self::LANG.'.columns.status'))
                    ->trueLabel(__(self::LANG.'.status.reconciled'))
                    ->falseLabel(__(self::LANG.'.status.to-reconcile'))
                    ->default(false),
                SelectFilter::make('journal_id')
                    ->label(__(self::LANG.'.columns.journal'))
                    ->options(fn (): array => $this->journalOptions()),
            ])
            ->recordActions([
                Action::make('match')
                    ->label(__(self::LANG.'.match.label'))
                    ->icon('heroicon-o-link')
                    ->visible(fn (BankStatementLine $record): bool => ! $record->is_reconciled)
                    ->modalDescription(__(self::LANG.'.match.description'))
                    ->schema(fn (BankStatementLine $record): array => [
                        Select::make('payment_id')
                            ->label(__(self::LANG.'.match.payment'))
                            ->options(fn (): array => $this->paymentOptions($record))
                            ->helperText(fn (): ?string => $this->paymentOptions($record) === [] ? __(self::LANG.'.match.none') : null)
                            ->required(),
                    ])
                    ->action(function (BankStatementLine $record, array $data, Reconciliation $service): void {
                        $this->attempt(fn () => $service->matchPayment($record, Payment::query()->findOrFail($data['payment_id'])), self::LANG.'.match.done');
                    }),
                Action::make('book')
                    ->label(__(self::LANG.'.book.label'))
                    ->icon('heroicon-o-pencil-square')
                    ->color('gray')
                    ->visible(fn (BankStatementLine $record): bool => ! $record->is_reconciled)
                    ->modalDescription(__(self::LANG.'.book.description'))
                    ->schema(fn (BankStatementLine $record): array => [
                        Select::make('account_id')
                            ->label(__(self::LANG.'.book.account'))
                            ->options(fn (): array => $this->accountOptions($record))
                            ->searchable()
                            ->required(),
                        TextInput::make('label')
                            ->label(__(self::LANG.'.book.entry-label'))
                            ->default($record->payment_reference)
                            ->maxLength(255),
                    ])
                    ->action(function (BankStatementLine $record, array $data, Reconciliation $service): void {
                        $this->attempt(fn () => $service->bookToAccount($record, Account::query()->findOrFail($data['account_id']), $data['label'] ?? null), self::LANG.'.book.done');
                    }),
            ])
            ->emptyStateHeading(__(self::LANG.'.empty.heading'))
            ->emptyStateDescription(__(self::LANG.'.empty.description'))
            ->emptyStateIcon('heroicon-o-building-library');
    }

    protected function columnInput(string $field, bool $required = false, ?int $default = null): TextInput
    {
        return TextInput::make('col_'.$field)
            ->label(__(self::LANG.'.import.columns.'.$field))
            ->numeric()
            ->integer()
            ->minValue(1)
            ->maxValue(100)
            ->default($default)
            ->required($required);
    }

    protected function attempt(callable $callback, string $successKey): void
    {
        try {
            $callback();

            Notification::make()->success()->title(__($successKey))->send();
        } catch (CannotReconcile $exception) {
            Notification::make()->danger()->title($exception->getMessage())->send();
        }
    }

    /**
     * @return array<int, string>
     */
    protected function journalOptions(): array
    {
        return Journal::query()
            ->whereIn('type', [JournalType::BANK, JournalType::CASH])
            ->whereIn('company_id', app(CompanyContext::class)->activeIds())
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * @return array<int, string>
     */
    protected function paymentOptions(BankStatementLine $line): array
    {
        try {
            return app(Reconciliation::class)->suggestions($line)
                ->mapWithKeys(fn (Payment $payment): array => [
                    $payment->id => collect([$payment->name, $payment->partner?->name, $payment->date?->toDateString()])->filter()->implode(' · '),
                ])
                ->all();
        } catch (CannotReconcile) {
            return [];
        }
    }

    /**
     * @return array<int, string>
     */
    protected function accountOptions(BankStatementLine $line): array
    {
        return Account::query()
            ->where('deprecated', false)
            ->whereNotIn('account_type', [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE])
            ->where(fn (Builder $query) => $query
                ->whereHas('companies', fn (Builder $companies) => $companies->where('companies.id', $line->company_id))
                ->orWhereDoesntHave('companies'))
            ->orderBy('code')
            ->get(['id', 'code', 'name'])
            ->mapWithKeys(fn (Account $account): array => [$account->id => trim($account->code.' '.$account->name)])
            ->all();
    }
}

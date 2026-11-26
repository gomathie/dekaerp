<?php

namespace Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\FusedGroup;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Expense;
use Webkul\Account\Models\Tax;
use Webkul\Account\Services\ExpenseRecorder;
use Webkul\Account\Support\DocumentCurrency;
use Webkul\Invoice\Filament\Clusters\Vendors\Resources\ExpenseResource;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Support\CompanyStorage;

/**
 * The approved Expenses form (plan #10 Z1). Every option list is the
 * recorder's own query for the expense's company, so the form offers
 * exactly what the service accepts; the service checks again on save.
 */
class ExpenseForm
{
    protected const LANG = ExpenseResource::LANG.'.form';

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 2])
                            ->schema([
                                DatePicker::make('date')
                                    ->label(__(self::LANG.'.fields.date'))
                                    ->native(false)
                                    ->default(now())
                                    ->required(),
                                FusedGroup::make([
                                    TextInput::make('amount')
                                        ->hiddenLabel()
                                        ->numeric()
                                        ->minValue(0.01)
                                        ->maxValue(ExpenseRecorder::MAX_AMOUNT)
                                        ->required()
                                        ->live(onBlur: true),
                                    // The currency is the suffix of the amount (U103), not a
                                    // second field beside it that collapses on a phone.
                                    Select::make('currency_id')
                                        ->hiddenLabel()
                                        ->options(fn (?Model $record): array => static::currencyOptions($record))
                                        ->default(fn (): ?int => static::company(null)?->currency_id)
                                        ->selectablePlaceholder(false)
                                        ->live()
                                        ->required(),
                                ])
                                    ->label(__(self::LANG.'.fields.amount'))
                                    ->helperText(__(self::LANG.'.hints.currency')),
                                Select::make('expense_account_id')
                                    ->label(__(self::LANG.'.fields.expense-account'))
                                    ->options(fn (?Model $record): array => static::recorder()
                                        ->accountsQuery(static::companyId($record))
                                        ->orderBy('code')
                                        ->get()
                                        ->mapWithKeys(fn (Account $account): array => [$account->id => trim($account->code.' '.$account->name)])
                                        ->all())
                                    ->default(fn (): ?int => static::recorder()->lastUsedAccountId(static::companyId(null)))
                                    ->searchable()
                                    ->required()
                                    ->helperText(__(self::LANG.'.hints.expense-account'))
                                    ->columnSpanFull(),
                                ToggleButtons::make('journal_id')
                                    ->label(__(self::LANG.'.fields.paid-from'))
                                    ->options(fn (?Model $record): array => static::journalOptions($record))
                                    ->default(fn (): ?int => static::defaultJournalId(null))
                                    ->inline()
                                    ->required()
                                    ->visible(fn (?Model $record): bool => count(static::journalOptions($record)) <= 5)
                                    ->dehydrated(fn (?Model $record): bool => count(static::journalOptions($record)) <= 5)
                                    ->helperText(__(self::LANG.'.hints.paid-from'))
                                    ->columnSpanFull(),
                                // More than five accounts wraps a button row on a phone (U102).
                                Select::make('journal_id')
                                    ->label(__(self::LANG.'.fields.paid-from'))
                                    ->options(fn (?Model $record): array => static::journalOptions($record))
                                    ->default(fn (): ?int => static::defaultJournalId(null))
                                    ->searchable()
                                    ->required()
                                    ->visible(fn (?Model $record): bool => count(static::journalOptions($record)) > 5)
                                    ->dehydrated(fn (?Model $record): bool => count(static::journalOptions($record)) > 5)
                                    ->helperText(__(self::LANG.'.hints.paid-from'))
                                    ->columnSpanFull(),
                                Select::make('partner_id')
                                    ->label(__(self::LANG.'.fields.vendor'))
                                    ->searchable()
                                    ->getSearchResultsUsing(fn (string $search, ?Model $record): array => static::recorder()
                                        ->partnersQuery(static::companyId($record))
                                        ->whereRaw('LOWER(name) LIKE ?', ['%'.addcslashes(mb_strtolower($search), '%_\\').'%'])
                                        ->orderBy('name')
                                        ->limit(50)
                                        ->pluck('name', 'id')
                                        ->all())
                                    ->getOptionLabelUsing(fn ($value, ?Model $record): ?string => static::recorder()
                                        ->partnersQuery(static::companyId($record))
                                        ->whereKey($value)
                                        ->value('name'))
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->label(__(self::LANG.'.fields.vendor-name'))
                                            ->required()
                                            ->maxLength(255),
                                    ])
                                    ->createOptionUsing(fn (array $data, ?Model $record): int => static::recorder()
                                        ->createVendor((string) $data['name'], static::companyId($record))
                                        ->getKey())
                                    ->live()
                                    ->afterStateUpdated(function (Set $set, $state, ?Model $record): void {
                                        // The vendor's own currency, when it has one, as on bills.
                                        if ($currencyId = DocumentCurrency::ofContact($state ? (int) $state : null, static::companyId($record))) {
                                            $set('currency_id', $currencyId);
                                        }
                                    })
                                    ->helperText(__(self::LANG.'.hints.vendor')),
                                Select::make('tax_id')
                                    ->label(__(self::LANG.'.fields.tax'))
                                    ->options(fn (?Model $record): array => static::recorder()
                                        ->taxesQuery(static::companyId($record))
                                        ->orderBy('name')
                                        ->get()
                                        ->mapWithKeys(fn (Tax $tax): array => [$tax->id => $tax->price_include
                                            ? __(self::LANG.'.tax-included', ['tax' => $tax->name])
                                            : $tax->name])
                                        ->all())
                                    ->placeholder(__(self::LANG.'.hints.no-tax'))
                                    ->live()
                                    // The switch starts at the chosen tax's own setting.
                                    ->afterStateUpdated(function (Set $set, $state, ?Model $record): void {
                                        $tax = filled($state) ? static::recorder()->taxesQuery(static::companyId($record))->whereKey($state)->first() : null;

                                        $set('amount_includes_tax', $tax ? (bool) $tax->price_include : null);
                                    })
                                    ->helperText(__(self::LANG.'.hints.tax')),
                                Toggle::make('amount_includes_tax')
                                    ->label(__(self::LANG.'.fields.amount-includes-tax'))
                                    ->helperText(__(self::LANG.'.hints.amount-includes-tax'))
                                    ->visible(fn (Get $get): bool => filled($get('tax_id')))
                                    // An expense saved before the switch existed shows the
                                    // tax's own setting, which is what it was posted with.
                                    ->afterStateHydrated(function (Toggle $component, $state, ?Model $record): void {
                                        if ($state === null && $record?->tax_id) {
                                            $component->state(static::recorder()->includesTax(null, Tax::withoutGlobalScopes()->find($record->tax_id)));
                                        }
                                    })
                                    ->live(),
                                TextInput::make('reference')
                                    ->label(__(self::LANG.'.fields.reference'))
                                    ->maxLength(255),
                                Textarea::make('note')
                                    ->label(__(self::LANG.'.fields.note'))
                                    ->rows(1)
                                    ->maxLength(2000),
                                FileUpload::make('receipt_path')
                                    ->label(__(self::LANG.'.fields.receipt'))
                                    ->disk('public')
                                    ->directory(ExpenseRecorder::RECEIPT_DIRECTORY)
                                    ->preserveFilenames(false)
                                    ->preventFilePathTampering()
                                    ->fetchFileInformation(false)
                                    ->storeFileNamesIn('receipt_name')
                                    // Stored by the service, which reads the type from the content.
                                    ->saveUploadedFileUsing(function (TemporaryUploadedFile $file, FileUpload $component, ?Model $record): string {
                                        try {
                                            return static::recorder()->storeReceipt($file, static::companyId($record));
                                        } catch (ValidationException $e) {
                                            // Shown on this field, not as a stray error.
                                            throw ValidationException::withMessages([$component->getStatePath() => $e->errors()['receipt_path'] ?? [$e->getMessage()]]);
                                        }
                                    })
                                    ->getUploadedFileUsing(fn (string $file, string|array|null $storedFileNames, ?Model $record): array => [
                                        'name' => (is_array($storedFileNames) ? ($storedFileNames[$file] ?? null) : $storedFileNames) ?? basename($file),
                                        'size' => 0,
                                        'type' => null,
                                        'url'  => CompanyStorage::url(static::companyId($record), $file),
                                    ])
                                    ->acceptedFileTypes(array_keys(ExpenseRecorder::RECEIPT_TYPES))
                                    ->maxSize(ExpenseRecorder::RECEIPT_MAX_KB)
                                    ->helperText(__(self::LANG.'.hints.receipt'))
                                    ->columnSpanFull(),
                                Text::make(fn (Get $get, ?Model $record): string => static::postingSummary($get, $record))
                                    ->columnSpanFull(),
                                TextInput::make('reason')
                                    ->label(__(self::LANG.'.fields.reason'))
                                    ->helperText(__(self::LANG.'.hints.reason'))
                                    // Only when the books change: a note or receipt
                                    // change needs none (user, 2026-11-26). Same rule
                                    // as the service, which checks again.
                                    ->required(fn (Get $get, ?Model $record): bool => $record instanceof Expense
                                        && static::recorder()->changesTheBooks($record, static::bookFields($get)))
                                    ->maxLength(255)
                                    ->visibleOn('edit')
                                    ->columnSpanFull(),
                            ]),
                    ]),
            ]);
    }

    /**
     * "Pays X from Y: expense N, tax T" under the form, from the recorder's
     * own split, so what the person reads is what will be posted.
     */
    protected static function postingSummary(Get $get, ?Model $record): string
    {
        $amount = $get('amount');
        $currency = Currency::query()->find($get('currency_id'));

        if (! is_numeric($amount) || (float) $amount <= 0 || ! $currency) {
            return __(self::LANG.'.summary.empty');
        }

        $companyId = static::companyId($record);
        $tax = filled($get('tax_id')) ? static::recorder()->taxesQuery($companyId)->whereKey($get('tax_id'))->first() : null;
        $split = static::recorder()->split((float) $amount, $tax, $currency, $tax ? static::recorder()->includesTax($get('amount_includes_tax'), $tax) : null);

        return __(self::LANG.'.summary.posts', [
            'total' => money($split['total'], $currency->name),
            'net'   => money($split['net'], $currency->name),
            'tax'   => money($split['tax'], $currency->name),
        ]);
    }

    /**
     * The fields ExpenseRecorder::changesTheBooks() compares, as the form
     * holds them now.
     *
     * @return array<string, mixed>
     */
    protected static function bookFields(Get $get): array
    {
        return collect(['date', 'amount', 'currency_id', 'expense_account_id', 'journal_id', 'tax_id', 'partner_id', 'reference', 'amount_includes_tax'])
            ->mapWithKeys(fn (string $field): array => [$field => $get($field)])
            ->all();
    }

    /**
     * The expense's own company on edit, the session's on create. Never a
     * form field: an expense is filed under the company being worked in.
     */
    /**
     * @return array<int, string>
     */
    protected static function currencyOptions(?Model $record): array
    {
        return static::recorder()
            ->currenciesQuery(static::companyId($record))
            ->orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    /**
     * Last used journal first, so a long searchable list opens on the usual account.
     *
     * @return array<int, string>
     */
    protected static function journalOptions(?Model $record): array
    {
        $companyId = static::companyId($record);
        $options = static::recorder()->journalsQuery($companyId)->pluck('name', 'id')->all();
        $last = static::recorder()->lastUsedJournalId($companyId);

        if ($last !== null && array_key_exists($last, $options)) {
            $label = $options[$last];
            unset($options[$last]);
            $options = [$last => $label] + $options;
        }

        return $options;
    }

    protected static function defaultJournalId(?Model $record): ?int
    {
        $options = static::journalOptions($record);

        return $options === [] ? null : (int) array_key_first($options);
    }

    public static function companyId(?Model $record): int
    {
        return (int) ($record?->company_id ?: current_company_id());
    }

    protected static function company(?Model $record): ?Company
    {
        return Company::query()->find(static::companyId($record));
    }

    protected static function recorder(): ExpenseRecorder
    {
        return app(ExpenseRecorder::class);
    }
}

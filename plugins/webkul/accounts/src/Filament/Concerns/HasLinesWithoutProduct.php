<?php

namespace Webkul\Account\Filament\Concerns;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\Product;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;
use Webkul\Support\Models\Currency;

/**
 * Invoice and bill lines without a product (plan #9 R-5; user, 2026-11-25,
 * after approving the mockup; Zoho Books accepts a free-text line).
 *
 * The line is a description, an account, a quantity, a unit price and
 * taxes. It is posted as an ordinary product line with no product, so
 * totals, tax lines, the journal entry and the printed document include it,
 * and it moves no stock: nothing in inventory reads an invoice line.
 *
 * One place for the parts both line repeaters (InvoiceResource, and
 * BillResource, which does not extend it) share.
 */
trait HasLinesWithoutProduct
{
    /**
     * Marks the item as a line without a product. Never saved: a saved line
     * with no product is one, so an existing line is recognised from its
     * record. It must come first in the item schema, because the taxes
     * field recomputes the line while the item is hydrated, and a line not
     * yet recognised there had its price and totals reset to 0.
     */
    public static function lineWithoutProductFlag(): Hidden
    {
        return Hidden::make('without_product')
            ->default(false)
            ->dehydrated(false)
            ->afterStateHydrated(function (Hidden $component, $record): void {
                if ($record instanceof MoveLine && $record->exists) {
                    $component->state(blank($record->product_id));
                }
            });
    }

    public static function isLineWithoutProduct(Get $get): bool
    {
        return (bool) $get('without_product');
    }

    /**
     * Marks the "use what I typed" choice in the item box (plan 10, piece 7).
     * Never saved: the choice turns the line into a line without a product
     * at once (startTypedLine()), so product_id never holds it on save.
     */
    public const TYPED_LINE_PREFIX = 'typed:';

    /**
     * One box per line, as in Zoho Books (user decision, 2026-11-25, checked
     * in Zoho live): it searches products and services of the document's
     * company, each with its price, and always offers the typed text itself
     * as a free line priced by hand. It replaces R-5's separate "Add a line
     * without a product" button. The company filter is the same one the
     * relationship uses: without it the box listed every client's products.
     *
     * @return array<int|string, string>
     */
    public static function itemSearchResults(string $search, mixed $companyId, mixed $currencyId, string $priceColumn): array
    {
        $search = trim($search);
        $currency = $currencyId ? Currency::query()->whereKey($currencyId)->value('name') : null;

        $options = Product::query()
            ->whereNull('is_configurable')
            ->where(owned_by_company($companyId ?: null))
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $query) use ($search): void {
                $like = '%'.mb_strtolower($search).'%';

                $query->whereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(reference) LIKE ?', [$like]);
            }))
            ->orderBy('name')
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (Product $product): array => [
                $product->getKey() => $product->name.' · '.money((float) $product->{$priceColumn}, $currency),
            ])
            ->all();

        if ($search !== '') {
            $options[static::TYPED_LINE_PREFIX.mb_substr($search, 0, 255)] = __('accounts::filament/resources/line-without-product.use-typed', ['text' => mb_substr($search, 0, 255)]);
        }

        return $options;
    }

    public static function isTypedLine(mixed $state): bool
    {
        return is_string($state) && str_starts_with($state, static::TYPED_LINE_PREFIX);
    }

    /**
     * "Use what I typed": the line becomes a line without a product (R-5),
     * described by the text, on the journal's default account and with no
     * tax until one is chosen. The price is the person's to enter.
     */
    public static function startTypedLine(Set $set, Get $get, string $state): void
    {
        $set('product_id', null);
        $set('without_product', true);
        $set('name', mb_substr(substr($state, strlen(static::TYPED_LINE_PREFIX)), 0, 255));
        $set('taxes', []);
        $set('account_id', static::defaultLineAccountId($get('../../journal_id')));
    }

    /**
     * The Account column, shown only while the document has a line without
     * a product, so an ordinary invoice keeps its layout.
     *
     * @return array<TableColumn>
     */
    public static function lineWithoutProductColumns(Repeater $component): array
    {
        $hasLine = collect($component->getRawState() ?? [])
            ->contains(fn ($item): bool => is_array($item) && ! empty($item['without_product']));

        // The description now sits under the item box, in the item's own cell
        // (plan 10, piece 7), so only the account needs a column.
        return [
            TableColumn::make('account_id')
                ->label(__('accounts::filament/resources/line-without-product.columns.account'))
                ->width(220)
                ->resizable()
                ->visible($hasLine),
        ];
    }

    /**
     * The line's own text, under the item box (plan 10, piece 7; user,
     * 2026-11-25: a description under each line, printed on the invoice).
     * Required on a line without a product, where it is the line. On a
     * product line it is optional: shown only when it says more than the
     * product's name, and sent blank as null so MoveLine::computeName() puts
     * the product's text back; text typed here is kept, and the printed
     * document shows it under the item (MoveDocument). Never sent once the
     * document is posted or cancelled.
     */
    public static function lineDescriptionField(): TextInput
    {
        return TextInput::make('name')
            ->label(__('accounts::filament/resources/line-without-product.columns.description'))
            ->hiddenLabel()
            ->placeholder(fn (Get $get): string => static::isLineWithoutProduct($get)
                ? __('accounts::filament/resources/line-without-product.columns.description')
                : __('accounts::filament/resources/line-without-product.description-placeholder'))
            ->maxLength(255)
            ->formatStateUsing(fn (?string $state, $record): ?string => static::ownLineText($state, $record))
            ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? $state : null)
            ->required(fn (Get $get): bool => static::isLineWithoutProduct($get))
            ->disabled(fn ($record): bool => static::isLineClosed($record))
            ->dehydrated(fn ($record): bool => ! static::isLineClosed($record));
    }

    /** The saved text, unless it is only the product's own name (with its sales description). */
    protected static function ownLineText(?string $state, $record): ?string
    {
        $productName = $record instanceof MoveLine ? $record->product?->display_name : null;

        if (blank($state) || blank($productName)) {
            return $state;
        }

        return $state === $productName || str_starts_with($state, $productName."\n") ? null : $state;
    }

    /**
     * The account the line is posted to. A product line's account comes
     * from its product on every save (MoveLine::computeAccountId()), so the
     * field only shows it there and never sends it; a value sent for a
     * product line would be silently replaced.
     */
    public static function lineAccountField(): Select
    {
        return Select::make('account_id')
            ->label(__('accounts::filament/resources/line-without-product.columns.account'))
            ->relationship(
                'account',
                'name',
                modifyQueryUsing: fn (Builder $query, Get $get): Builder => static::lineAccountQuery($query, $get('../../company_id')),
            )
            ->getOptionLabelFromRecordUsing(fn ($record): string => trim($record->code.' '.$record->name))
            ->searchable(['code', 'name'])
            ->preload()
            ->wrapOptionLabels(false)
            ->required(fn (Get $get): bool => static::isLineWithoutProduct($get))
            ->disabled(fn (Get $get, $record): bool => ! static::isLineWithoutProduct($get) || static::isLineClosed($record))
            ->dehydrated(fn (Get $get, $record): bool => static::isLineWithoutProduct($get) && ! static::isLineClosed($record));
    }

    /**
     * Accounts a line without a product may be posted to. The document
     * company's own chart only: without the filter the list offered every
     * client's accounts. Receivable and payable accounts are left out: the
     * document's balance line lives there, and a sales line posted to one
     * would be read as money owed rather than earned.
     */
    public static function lineAccountQuery(Builder $query, mixed $companyId): Builder
    {
        return $query
            ->where('deprecated', false)
            ->whereNotIn('account_type', [AccountType::ASSET_RECEIVABLE, AccountType::LIABILITY_PAYABLE])
            ->where(owned_by_company($companyId ?: null));
    }

    /**
     * Read through Journal's company scope: the journal field lists only the
     * document company's journals, and a journal the actor may not see gives
     * no default rather than another company's account.
     */
    public static function defaultLineAccountId(mixed $journalId): ?int
    {
        if (blank($journalId)) {
            return null;
        }

        $accountId = Journal::query()->whereKey($journalId)->value('default_account_id');

        return $accountId ? (int) $accountId : null;
    }

    /**
     * Data for a line without a product: posted as a product line, stated
     * explicitly. MoveLine's saving hook computes the account before the
     * display type, and a line without one is costed through the default
     * branch, which replaces the chosen account with the journal's; and
     * MoveCalculator::recomputeLine() zeroes the amounts of any line that is
     * not a product line (AGENTS.md, MoveLine display_type).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function prepareLineWithoutProduct(array $data): array
    {
        if (blank($data['product_id'] ?? null)) {
            $data['product_id'] = null;
            $data['display_type'] = DisplayType::PRODUCT;
        }

        return $data;
    }

    protected static function isLineClosed($record): bool
    {
        return in_array($record?->parent_state, [MoveState::POSTED, MoveState::CANCEL]);
    }
}

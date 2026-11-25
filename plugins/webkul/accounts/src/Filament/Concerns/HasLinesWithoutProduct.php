<?php

namespace Webkul\Account\Filament\Concerns;

use Filament\Actions\Action;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\MoveLine;
use Webkul\Support\Filament\Forms\Components\Repeater;
use Webkul\Support\Filament\Forms\Components\Repeater\TableColumn;

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
     * Description and Account columns, shown only while the document has a
     * line without a product, so an ordinary invoice keeps its layout.
     *
     * @return array<TableColumn>
     */
    public static function lineWithoutProductColumns(Repeater $component): array
    {
        $hasLine = collect($component->getRawState() ?? [])
            ->contains(fn ($item): bool => is_array($item) && ! empty($item['without_product']));

        return [
            TableColumn::make('name')
                ->label(__('accounts::filament/resources/line-without-product.columns.description'))
                ->width(250)
                ->resizable()
                ->visible($hasLine),
            TableColumn::make('account_id')
                ->label(__('accounts::filament/resources/line-without-product.columns.account'))
                ->width(220)
                ->resizable()
                ->visible($hasLine),
        ];
    }

    /**
     * The line's own text. A product line keeps the label it takes from its
     * product (MoveLine::computeName()), so only a line without a product
     * sends this, and never once the document is posted or cancelled.
     */
    public static function lineDescriptionField(): TextInput
    {
        return TextInput::make('name')
            ->label(__('accounts::filament/resources/line-without-product.columns.description'))
            ->maxLength(255)
            ->required(fn (Get $get): bool => static::isLineWithoutProduct($get))
            ->disabled(fn (Get $get, $record): bool => ! static::isLineWithoutProduct($get) || static::isLineClosed($record))
            ->dehydrated(fn (Get $get, $record): bool => static::isLineWithoutProduct($get) && ! static::isLineClosed($record));
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
     * "Add a line without a product", next to the repeater's own add button.
     * The account starts as the document journal's default: income for a
     * sales journal, expense for a purchase journal. Taxes start empty (user,
     * 2026-11-25: "no tax until chosen").
     */
    public static function addLineWithoutProductAction(): Action
    {
        return Action::make('addLineWithoutProduct')
            ->label(__('accounts::filament/resources/line-without-product.add'))
            ->icon('heroicon-m-plus')
            ->visible(fn (Repeater $component): bool => $component->isAddable())
            ->action(function (Repeater $component, Get $get): void {
                $uuid = $component->generateUuid() ?? (string) Str::uuid();

                $items = $component->getRawState() ?? [];
                $items[$uuid] = [];
                $component->rawState($items);

                $component->getChildSchema($uuid)->fill();

                $items = $component->getRawState();
                $items[$uuid]['without_product'] = true;
                $items[$uuid]['product_id'] = null;
                $items[$uuid]['taxes'] = [];
                $items[$uuid]['account_id'] = static::defaultLineAccountId($get('journal_id'));
                $component->rawState($items);

                $component->collapsed(false, shouldMakeComponentCollapsible: false);

                $component->callAfterStateUpdated();
            });
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

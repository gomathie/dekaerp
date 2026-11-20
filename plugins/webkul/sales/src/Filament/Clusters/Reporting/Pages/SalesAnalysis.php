<?php

namespace Webkul\Sale\Filament\Clusters\Reporting\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\PluginManager\Support\PluginEntitlement;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Filament\Clusters\Reporting;
use Webkul\Sale\Filament\Exports\SalesAnalysisExporter;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Support\DirectInvoices;
use Webkul\Support\Filament\CompanyMembers;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;

/**
 * Sales analysis (reports plan R2, 2026-11-13): confirmed orders with their
 * untaxed and total amounts, groupable by customer, salesperson or month,
 * with totals per group, filters and export. One page instead of one per
 * view ("no overkill", user, 2026-11-12). It counts ordered amounts
 * (confirmed orders), not invoiced or delivered ones.
 *
 * Rows are company-scoped by the model's CompanyScope; nothing here removes
 * it. Export runs the filtered query (getTableQueryForExport()).
 */
class SalesAnalysis extends Page implements HasTable
{
    use ChecksPagePermission, HasPageShield, InteractsWithTable;

    protected string $view = 'sales::filament.clusters.reporting.pages.analysis';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static function getPagePermission(): ?string
    {
        return 'page_sale_sales_analysis';
    }

    /**
     * Module switch and the page permission. Written out because a
     * canAccess() here replaces HasPageShield's (see ChecksPagePermission).
     */
    public static function canAccess(): bool
    {
        return PluginEntitlement::enabledForCurrent('sales') && static::hasPagePermission();
    }

    public static function getNavigationLabel(): string
    {
        return __('sales::filament/clusters/reporting.analysis.title');
    }

    public function getTitle(): string
    {
        return __('sales::filament/clusters/reporting.analysis.title');
    }

    public function getSubheading(): ?string
    {
        return __('sales::filament/clusters/reporting.analysis.subheading');
    }

    public function table(Table $table): Table
    {
        $lang = 'sales::filament/clusters/reporting.analysis';

        return $table
            ->query(static::rowsQuery())
            ->columns([
                TextColumn::make('name')->label(__($lang.'.columns.reference'))->searchable(),
                TextColumn::make('date_order')->label(__($lang.'.columns.date'))->date()->sortable(),
                TextColumn::make('customer')->label(__($lang.'.columns.customer'))->searchable(),
                TextColumn::make('salesperson')->label(__($lang.'.columns.salesperson'))->placeholder('—'),
                // Already in company currency (rowsQuery), so totals never
                // add cedis to dollars.
                TextColumn::make('amount_untaxed')
                    ->label(__($lang.'.columns.untaxed'))
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum('amount_untaxed'))),
                TextColumn::make('amount_total')
                    ->label(__($lang.'.columns.total'))
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum('amount_total'))),
            ])
            ->groups([
                Group::make('customer')->label(__($lang.'.groups.customer')),
                Group::make('salesperson')->label(__($lang.'.groups.salesperson')),
                Group::make('date_order')->label(__($lang.'.groups.month'))
                    ->getKeyFromRecordUsing(fn (Order $record): string => $record->date_order?->format('Y-m') ?? '')
                    ->getTitleFromRecordUsing(fn (Order $record): string => $record->date_order?->translatedFormat('F Y') ?? '—')
                    ->orderQueryUsing(fn (Builder $query, string $direction): Builder => $query->orderBy('date_order', $direction)),
            ])
            ->filters([
                Filter::make('period')
                    ->schema([
                        DatePicker::make('from')->label(__($lang.'.filters.from')),
                        DatePicker::make('until')->label(__($lang.'.filters.until')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('date_order', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('date_order', '<=', $date))),
                SelectFilter::make('partner_id')
                    ->label(__($lang.'.columns.customer'))
                    ->relationship('partner', 'name')
                    ->searchable()
                    ->preload(false),
                SelectFilter::make('user_id')
                    ->label(__($lang.'.columns.salesperson'))
                    ->options(fn (): array => CompanyMembers::options()),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__($lang.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(SalesAnalysisExporter::class),
            ])
            ->defaultSort('date_order', 'desc')
            ->paginated([25, 50, 100]);
    }

    /**
     * One row per sale: confirmed orders, plus invoices and credit notes made
     * without an order (DirectInvoices; user, 2026-11-20: "all products sold
     * and paid by invoices created directly should reflect in sales").
     * Amounts in company currency; a credit note counts negative. Invoice
     * rows take a negative id so the two sources never share a key, and an
     * invoice with no salesperson shows whoever created it.
     *
     * Each part reads through its own company and ownership scopes. The
     * outer query is named `sales_orders` so Order's relations and
     * Filament's qualified columns resolve; it drops the model's scopes,
     * which the parts have already applied.
     */
    public static function rowsQuery(): Builder
    {
        $orders = Order::query()
            ->where('sales_orders.state', OrderState::SALE->value)
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'sales_orders.partner_id')
            ->leftJoin('users', 'users.id', '=', 'sales_orders.user_id')
            ->toBase()
            ->selectRaw('sales_orders.id as id, sales_orders.name as name, sales_orders.date_order as date_order, '
                .'sales_orders.partner_id as partner_id, sales_orders.user_id as user_id, '
                .'partners_partners.name as customer, users.name as salesperson, '
                .'CASE WHEN sales_orders.currency_rate > 0 THEN sales_orders.amount_untaxed / sales_orders.currency_rate ELSE sales_orders.amount_untaxed END as amount_untaxed, '
                .'CASE WHEN sales_orders.currency_rate > 0 THEN sales_orders.amount_total / sales_orders.currency_rate ELSE sales_orders.amount_total END as amount_total');

        $invoices = DirectInvoices::invoices()
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'accounts_account_moves.partner_id')
            ->leftJoin('users', 'users.id', '=', DB::raw('COALESCE(accounts_account_moves.invoice_user_id, accounts_account_moves.creator_id)'))
            ->toBase()
            ->selectRaw('-accounts_account_moves.id as id, accounts_account_moves.name as name, '
                .'CAST(accounts_account_moves.invoice_date AS timestamp) as date_order, '
                .'accounts_account_moves.partner_id as partner_id, users.id as user_id, '
                .'partners_partners.name as customer, users.name as salesperson, '
                .'accounts_account_moves.amount_untaxed_signed as amount_untaxed, '
                .'accounts_account_moves.amount_total_signed as amount_total');

        return Order::query()
            ->withoutGlobalScopes()
            ->fromSub($orders->unionAll($invoices), 'sales_orders')
            ->select('sales_orders.*');
    }
}

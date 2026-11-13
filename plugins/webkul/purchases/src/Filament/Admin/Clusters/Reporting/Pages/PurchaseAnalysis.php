<?php

namespace Webkul\Purchase\Filament\Admin\Clusters\Reporting\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Webkul\Purchase\Enums\OrderState;
use Webkul\Purchase\Filament\Admin\Clusters\Reporting;
use Webkul\Purchase\Filament\Admin\Exports\PurchaseAnalysisExporter;
use Webkul\Purchase\Models\PurchaseOrder;
use Webkul\Purchase\Support\PurchasesAccess;
use Webkul\Support\Filament\CompanyMembers;
use Webkul\Support\Filament\Concerns\ChecksPagePermission;

/**
 * Purchase analysis (reports plan R2, 2026-11-13): confirmed purchase orders
 * groupable by supplier, buyer or month, with totals per group, filters and
 * export. Counts ordered amounts (confirmed and done orders), not billed or
 * received ones. Company currency: `total_cc_amount` is already converted;
 * the untaxed amount is divided by the order's rate.
 *
 * Rows are company-scoped by the model's CompanyScope; nothing removes it.
 */
class PurchaseAnalysis extends Page implements HasTable
{
    use ChecksPagePermission, HasPageShield, InteractsWithTable;

    protected string $view = 'purchases::filament.admin.clusters.reporting.pages.analysis';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected static function getPagePermission(): ?string
    {
        return 'page_purchase_purchase_analysis';
    }

    /** Module switch and the page permission (see ChecksPagePermission). */
    public static function canAccess(): bool
    {
        return PurchasesAccess::enabledForCurrent() && static::hasPagePermission();
    }

    public static function getNavigationLabel(): string
    {
        return __('purchases::filament/admin/clusters/reporting.analysis.title');
    }

    public function getTitle(): string
    {
        return __('purchases::filament/admin/clusters/reporting.analysis.title');
    }

    public function getSubheading(): ?string
    {
        return __('purchases::filament/admin/clusters/reporting.analysis.subheading');
    }

    public function table(Table $table): Table
    {
        $lang = 'purchases::filament/admin/clusters/reporting.analysis';

        return $table
            ->query(PurchaseOrder::query()
                ->whereIn('state', [OrderState::PURCHASE->value, OrderState::DONE->value])
                ->with(['partner', 'user']))
            ->columns([
                TextColumn::make('name')->label(__($lang.'.columns.reference'))->searchable(),
                TextColumn::make('ordered_at')->label(__($lang.'.columns.date'))->date()->sortable(),
                TextColumn::make('partner.name')->label(__($lang.'.columns.supplier'))->searchable(),
                TextColumn::make('user.name')->label(__($lang.'.columns.buyer'))->placeholder('—'),
                TextColumn::make('untaxed_amount')
                    ->label(__($lang.'.columns.untaxed'))
                    ->state(fn (PurchaseOrder $record): float => (float) $record->currency_rate > 0
                        ? round((float) $record->untaxed_amount / (float) $record->currency_rate, 2)
                        : (float) $record->untaxed_amount)
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum(DB::raw('CASE WHEN currency_rate > 0 THEN untaxed_amount / currency_rate ELSE untaxed_amount END')))),
                TextColumn::make('total_cc_amount')
                    ->label(__($lang.'.columns.total'))
                    ->numeric(2)
                    ->sortable()
                    ->summarize(Summarizer::make()->label(__($lang.'.sum'))->numeric(2)
                        ->using(fn (QueryBuilder $query): float => (float) $query->sum('total_cc_amount'))),
            ])
            ->groups([
                Group::make('partner.name')->label(__($lang.'.groups.supplier')),
                Group::make('user.name')->label(__($lang.'.groups.buyer')),
                Group::make('ordered_at')->label(__($lang.'.groups.month'))
                    ->getKeyFromRecordUsing(fn (PurchaseOrder $record): string => $record->ordered_at?->format('Y-m') ?? '')
                    ->getTitleFromRecordUsing(fn (PurchaseOrder $record): string => $record->ordered_at?->translatedFormat('F Y') ?? '—')
                    ->orderQueryUsing(fn (Builder $query, string $direction): Builder => $query->orderBy('ordered_at', $direction)),
            ])
            ->filters([
                Filter::make('period')
                    ->schema([
                        DatePicker::make('from')->label(__($lang.'.filters.from')),
                        DatePicker::make('until')->label(__($lang.'.filters.until')),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('ordered_at', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('ordered_at', '<=', $date))),
                SelectFilter::make('partner_id')
                    ->label(__($lang.'.columns.supplier'))
                    ->relationship('partner', 'name')
                    ->searchable(),
                SelectFilter::make('user_id')
                    ->label(__($lang.'.columns.buyer'))
                    ->options(fn (): array => CompanyMembers::options()),
            ])
            ->headerActions([
                ExportAction::make()
                    ->label(__($lang.'.export'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(PurchaseAnalysisExporter::class),
            ])
            ->defaultSort('ordered_at', 'desc')
            ->paginated([25, 50, 100]);
    }
}

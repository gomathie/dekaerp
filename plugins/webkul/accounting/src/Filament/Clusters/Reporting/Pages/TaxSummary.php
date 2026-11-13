<?php

namespace Webkul\Accounting\Filament\Clusters\Reporting\Pages;

use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Maatwebsite\Excel\Facades\Excel;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;
use Webkul\Account\Enums\MoveState;
use Webkul\Accounting\Filament\Clusters\Reporting;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns\NormalizeDateFilter;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns\ShowsCurrencyNotice;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\TaxSummaryExport;
use Webkul\Accounting\Support\CompanyRateMap;

/**
 * Tax Summary (accounting plan A7, user-approved 2026-11-12/13: "like Zoho
 * Books, taxes grouped and shown on reports").
 *
 * Per tax, for the period: the taxable base and the tax, on sales (collected)
 * and on purchases (paid), and the net to pay or reclaim. Read from the tax
 * lines stored on posted documents, so a later rate change never rewrites a
 * past period, and each child of a group tax (Ghana VAT, NHIL, GETFund) is
 * its own row. Credit notes and refunds carry the opposite sign and reduce
 * their period. Like the other report pages it builds raw ledger joins, so
 * it filters on the active companies itself (CompanyScope does not apply).
 */
class TaxSummary extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms, NormalizeDateFilter;
    use ShowsCurrencyNotice;

    protected string $view = 'accounting::filament.clusters.reporting.pages.tax-summary';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?int $navigationSort = 8;

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_tax_summary';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/reporting.pages.tax-summary.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/reporting.pages.tax-summary.navigation.title');
    }

    public function getTitle(): string
    {
        return __('accounting::filament/clusters/reporting.pages.tax-summary.navigation.title');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label(__('accounting::filament/clusters/reporting.pages.tax-summary.actions.export-excel'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $data = $this->taxSummaryData;

                    return Excel::download(
                        new TaxSummaryExport($data),
                        'tax-summary-'.$data['date_from']->format('Y-m-d').'-'.$data['date_to']->format('Y-m-d').'.xlsx'
                    );
                }),
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Section::make()
                ->schema([
                    DateRangePicker::make('date_range')
                        ->label(__('accounting::filament/clusters/reporting.pages.tax-summary.filters.date-range'))
                        ->suffixIcon('heroicon-o-calendar')
                        ->defaultThisMonth()
                        ->ranges([
                            'This Month'   => [now()->startOfMonth(), now()->endOfMonth()],
                            'Last Month'   => [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()],
                            'This Quarter' => [now()->startOfQuarter(), now()->endOfQuarter()],
                            'Last Quarter' => [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()],
                            'This Year'    => [now()->startOfYear(), now()->endOfYear()],
                            'Last Year'    => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
                        ])
                        ->alwaysShowCalendar()
                        ->live()
                        ->afterStateUpdated(fn () => null),
                ])
                ->columnSpanFull(),
        ];
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    /**
     * @return array{sales: list<array<string, mixed>>, purchases: list<array<string, mixed>>, totals: array<string, float>, date_from: Carbon, date_to: Carbon}
     */
    #[Computed]
    public function taxSummaryData(): array
    {
        $dateRange = $this->parseDateRange();
        $dateFrom = $dateRange ? Carbon::parse($dateRange[0])->startOfDay() : now()->startOfMonth();
        $dateTo = $dateRange ? Carbon::parse($dateRange[1])->endOfDay() : now()->endOfMonth();

        $rateMap = CompanyRateMap::make(date: $dateTo->toDateString());

        $balance = $rateMap->weight('accounts_account_move_lines.balance');
        $base = $rateMap->weight('accounts_account_move_lines.tax_base_amount');

        // Sales tax is a credit (negative balance), purchase tax a debit, so
        // the direction makes collected and paid tax read positive. A refund
        // posts the other way: its base takes the sign of its tax.
        $rows = DB::table('accounts_account_move_lines')
            ->join('accounts_account_moves', 'accounts_account_moves.id', '=', 'accounts_account_move_lines.move_id')
            ->join('accounts_taxes', 'accounts_taxes.id', '=', 'accounts_account_move_lines.tax_line_id')
            ->where('accounts_account_moves.state', MoveState::POSTED)
            ->whereIn('accounts_account_moves.company_id', $rateMap->companyIds())
            ->whereBetween('accounts_account_moves.date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->groupBy('accounts_taxes.id', 'accounts_taxes.name', 'accounts_taxes.type_tax_use', 'accounts_taxes.amount', 'accounts_taxes.amount_type')
            ->orderBy('accounts_taxes.name')
            ->select([
                'accounts_taxes.id',
                'accounts_taxes.name',
                'accounts_taxes.type_tax_use',
                'accounts_taxes.amount as rate',
                'accounts_taxes.amount_type',
                DB::raw("COALESCE(SUM(CASE WHEN accounts_taxes.type_tax_use = 'purchase' THEN {$balance} ELSE -({$balance}) END), 0) as tax"),
                DB::raw("COALESCE(SUM(CASE WHEN (CASE WHEN accounts_taxes.type_tax_use = 'purchase' THEN {$balance} ELSE -({$balance}) END) >= 0 THEN ABS({$base}) ELSE -ABS({$base}) END), 0) as base"),
            ])
            ->get();

        $toRow = fn (object $row): array => [
            'id'     => (int) $row->id,
            'name'   => $row->name,
            'rate'   => $row->amount_type === 'percent' ? rtrim(rtrim(number_format((float) $row->rate, 4, '.', ''), '0'), '.').'%' : null,
            'base'   => round((float) $row->base, 2),
            'tax'    => round((float) $row->tax, 2),
        ];

        $sales = $rows->where('type_tax_use', '!=', 'purchase')->map($toRow)->values()->all();
        $purchases = $rows->where('type_tax_use', 'purchase')->map($toRow)->values()->all();

        $collected = array_sum(array_column($sales, 'tax'));
        $paid = array_sum(array_column($purchases, 'tax'));

        return [
            'sales'     => $sales,
            'purchases' => $purchases,
            'totals'    => [
                'sales_base'     => array_sum(array_column($sales, 'base')),
                'collected'      => $collected,
                'purchases_base' => array_sum(array_column($purchases, 'base')),
                'paid'           => $paid,
                'net'            => round($collected - $paid, 2),
            ],
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
        ];
    }
}

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
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\MoveState;
use Webkul\Accounting\Filament\Clusters\Reporting;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns\NormalizeDateFilter;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Concerns\ShowsCurrencyNotice;
use Webkul\Accounting\Filament\Clusters\Reporting\Pages\Exports\CashFlowExport;
use Webkul\Accounting\Support\CompanyRateMap;

/**
 * Cash flow statement (accounting plan A6), direct method.
 *
 * Cash is every account of type "Bank and cash". Each posted entry that
 * moves cash in the period is classified by its main counterpart (the
 * non-cash line with the largest amount): fixed and non-current assets are
 * investing; equity and non-current liabilities are financing; everything
 * else (customers, suppliers, income, expenses, taxes, money in transit)
 * is operating. A transfer between two cash accounts nets to zero and is
 * left out. Opening + net change = closing, by construction, and the
 * closing figure equals the cash accounts' balance at the end date.
 *
 * Raw ledger joins, so it filters on the active companies itself, like the
 * other report pages.
 */
class CashFlow extends Page implements HasForms
{
    use HasPageShield, InteractsWithForms, NormalizeDateFilter;
    use ShowsCurrencyNotice;

    public const SECTIONS = ['operating', 'investing', 'financing'];

    protected string $view = 'accounting::filament.clusters.reporting.pages.cash-flow';

    protected static ?string $cluster = Reporting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 2;

    public ?array $data = [];

    protected static function getPagePermission(): ?string
    {
        return 'page_accounting_cash_flow';
    }

    public static function getNavigationGroup(): ?string
    {
        return __('accounting::filament/clusters/reporting.pages.cash-flow.navigation.group');
    }

    public static function getNavigationLabel(): string
    {
        return __('accounting::filament/clusters/reporting.pages.cash-flow.navigation.title');
    }

    public function getTitle(): string
    {
        return __('accounting::filament/clusters/reporting.pages.cash-flow.navigation.title');
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('excel')
                ->label(__('accounting::filament/clusters/reporting.pages.cash-flow.actions.export-excel'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $data = $this->cashFlowData;

                    return Excel::download(
                        new CashFlowExport($data),
                        'cash-flow-'.$data['date_from']->format('Y-m-d').'-'.$data['date_to']->format('Y-m-d').'.xlsx'
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
                        ->label(__('accounting::filament/clusters/reporting.pages.cash-flow.filters.date-range'))
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
     * Which section an entry belongs to, from its main counterpart's type.
     */
    public static function sectionFor(?string $accountType): string
    {
        return match ($accountType) {
            AccountType::ASSET_FIXED->value, AccountType::ASSET_NON_CURRENT->value => 'investing',
            AccountType::EQUITY->value, AccountType::EQUITY_UNAFFECTED->value, AccountType::LIABILITY_NON_CURRENT->value => 'financing',
            default => 'operating',
        };
    }

    /**
     * @return array{opening: float, closing: float, net: float, sections: array<string, array{rows: list<array{account: string, in: float, out: float}>, in: float, out: float, net: float}>, date_from: Carbon, date_to: Carbon}
     */
    #[Computed]
    public function cashFlowData(): array
    {
        $dateRange = $this->parseDateRange();
        $dateFrom = $dateRange ? Carbon::parse($dateRange[0])->startOfDay() : now()->startOfMonth();
        $dateTo = $dateRange ? Carbon::parse($dateRange[1])->endOfDay() : now()->endOfMonth();

        $rateMap = CompanyRateMap::make(date: $dateTo->toDateString());
        $balance = $rateMap->weight('accounts_account_move_lines.balance');

        $base = fn () => DB::table('accounts_account_move_lines')
            ->join('accounts_account_moves', 'accounts_account_moves.id', '=', 'accounts_account_move_lines.move_id')
            ->join('accounts_accounts', 'accounts_accounts.id', '=', 'accounts_account_move_lines.account_id')
            ->where('accounts_account_moves.state', MoveState::POSTED)
            ->whereIn('accounts_account_moves.company_id', $rateMap->companyIds());

        $cash = AccountType::ASSET_CASH->value;

        $opening = (float) $base()
            ->where('accounts_accounts.account_type', $cash)
            ->where('accounts_account_moves.date', '<', $dateFrom->toDateString())
            ->value(DB::raw("COALESCE(SUM({$balance}), 0)"));

        $inPeriod = fn () => $base()->whereBetween('accounts_account_moves.date', [$dateFrom->toDateString(), $dateTo->toDateString()]);

        // Net cash moved by each entry.
        $cashByMove = $inPeriod()
            ->where('accounts_accounts.account_type', $cash)
            ->groupBy('accounts_account_move_lines.move_id')
            ->select('accounts_account_move_lines.move_id', DB::raw("SUM({$balance}) as amount"))
            ->pluck('amount', 'move_id')
            ->map(fn ($amount): float => round((float) $amount, 2))
            ->filter(fn (float $amount): bool => abs($amount) >= 0.005);

        // Main counterpart of each of those entries.
        $counterparts = [];

        foreach ($cashByMove->keys()->chunk(1000) as $moveIds) {
            $inPeriod()
                ->whereIn('accounts_account_move_lines.move_id', $moveIds->all())
                ->where('accounts_accounts.account_type', '!=', $cash)
                ->select([
                    'accounts_account_move_lines.move_id',
                    'accounts_accounts.id as account_id',
                    'accounts_accounts.code',
                    'accounts_accounts.name',
                    'accounts_accounts.account_type',
                    DB::raw("ABS({$balance}) as weight"),
                ])
                ->get()
                ->each(function (object $line) use (&$counterparts): void {
                    $current = $counterparts[$line->move_id] ?? null;

                    if ($current === null || (float) $line->weight > (float) $current->weight) {
                        $counterparts[$line->move_id] = $line;
                    }
                });
        }

        $sections = array_fill_keys(self::SECTIONS, []);

        foreach ($cashByMove as $moveId => $amount) {
            $line = $counterparts[$moveId] ?? null;
            $section = self::sectionFor($line?->account_type);
            $key = $line ? (string) $line->account_id : 'other';

            $sections[$section][$key] ??= [
                'account' => $line ? trim($line->code.' '.$line->name) : __('accounting::filament/clusters/reporting.pages.cash-flow.other'),
                'in'      => 0.0,
                'out'     => 0.0,
            ];

            $sections[$section][$key][$amount >= 0 ? 'in' : 'out'] += abs($amount);
        }

        $result = [];
        $net = 0.0;

        foreach ($sections as $name => $rows) {
            $rows = collect($rows)->map(fn (array $row): array => [
                'account' => $row['account'],
                'in'      => round($row['in'], 2),
                'out'     => round($row['out'], 2),
            ])->sortBy('account')->values()->all();

            $in = round(array_sum(array_column($rows, 'in')), 2);
            $out = round(array_sum(array_column($rows, 'out')), 2);

            $result[$name] = ['rows' => $rows, 'in' => $in, 'out' => $out, 'net' => round($in - $out, 2)];
            $net += $in - $out;
        }

        return [
            'opening'   => round($opening, 2),
            'net'       => round($net, 2),
            'closing'   => round($opening + $net, 2),
            'sections'  => $result,
            'date_from' => $dateFrom,
            'date_to'   => $dateTo,
        ];
    }
}

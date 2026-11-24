<?php

namespace Webkul\Accounting\Support;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Services\SaleCurrencyQuestion;
use Webkul\Account\Settings\DefaultAccountSettings;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * The Multi-currency report's figures (plan #10 MC-3, TRACE365, user
 * 2026-11-24), one place for the page and its export.
 *
 * - Documents are posted invoices and credit notes (sales) or bills and
 *   refunds (purchases) dated in the period; a credit note or refund
 *   reduces its currency's totals.
 * - Base-currency figures at the rates booked come from the documents'
 *   `_signed` columns. "Today's rate" appears only in the columns that say
 *   so. A currency with no real rate today shows "—" there and stays out of
 *   that total: a missing rate is never 1 (DB-7).
 * - One company: the current one. Its base currency is the column every
 *   figure converts into, and another company's base would be a different
 *   column. Read with raw joins filtered on that company, like the other
 *   report pages.
 */
class MultiCurrencyReport
{
    public const SALES = 'sales';

    public const PURCHASES = 'purchases';

    /** The most documents listed under the totals; the totals count them all. */
    public const DOCUMENT_LIMIT = 200;

    public function __construct(protected SaleCurrencyQuestion $rates) {}

    /**
     * @return array{
     *     base: ?string,
     *     currencies: list<array<string, mixed>>,
     *     totals: array{count: int, invoiced_base: float, open_base: float, open_base_today: float, difference: float},
     *     exchange: array{gains: float, losses: float, net: float},
     *     documents: list<array<string, mixed>>,
     *     date_from: Carbon,
     *     date_to: Carbon,
     * }
     */
    public function build(Carbon $dateFrom, Carbon $dateTo, string $side = self::SALES, ?int $currencyId = null, ?int $companyId = null): array
    {
        $company = Company::withoutGlobalScopes()->with('currency')->find($companyId ?? current_company_id());
        $base = $company?->currency;

        $types = $side === self::PURCHASES ? [MoveType::IN_INVOICE, MoveType::IN_REFUND] : [MoveType::OUT_INVOICE, MoveType::OUT_REFUND];
        $refund = $side === self::PURCHASES ? MoveType::IN_REFUND : MoveType::OUT_REFUND;

        $documents = fn () => DB::table('accounts_account_moves')
            ->where('accounts_account_moves.company_id', $company?->getKey() ?? 0)
            ->where('accounts_account_moves.state', MoveState::POSTED->value)
            ->whereIn('accounts_account_moves.move_type', array_map(fn (MoveType $type): string => $type->value, $types))
            ->whereBetween('accounts_account_moves.invoice_date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->when($currencyId, fn ($query) => $query->where('accounts_account_moves.currency_id', $currencyId));

        // A credit note or refund counts against its currency.
        $sign = "(CASE WHEN accounts_account_moves.move_type = '{$refund->value}' THEN -1 ELSE 1 END)";

        $rows = $documents()
            ->groupBy('accounts_account_moves.currency_id')
            ->select([
                'accounts_account_moves.currency_id',
                DB::raw('COUNT(*) as document_count'),
                DB::raw("SUM({$sign} * accounts_account_moves.amount_total) as invoiced"),
                DB::raw("SUM({$sign} * ABS(accounts_account_moves.amount_total_signed)) as invoiced_base"),
                DB::raw("SUM({$sign} * accounts_account_moves.amount_residual) as open_amount"),
                DB::raw("SUM({$sign} * ABS(accounts_account_moves.amount_residual_signed)) as open_base"),
            ])
            ->get();

        $currencies = Currency::query()->whereIn('id', $rows->pluck('currency_id')->filter()->all())->get()->keyBy('id');

        $lines = [];
        $totals = ['count' => 0, 'invoiced_base' => 0.0, 'open_base' => 0.0, 'open_base_today' => 0.0, 'difference' => 0.0];

        foreach ($rows as $row) {
            $currency = $currencies->get($row->currency_id);

            if (! $currency || ! $base) {
                continue;
            }

            $invoiced = round((float) $row->invoiced, 2);
            $invoicedBase = round((float) $row->invoiced_base, 2);
            $open = round((float) $row->open_amount, 2);
            $openBase = round((float) $row->open_base, 2);

            // Today's value of what is still open, at the feed's rate.
            $todayRate = $currency->getKey() === $base->getKey() ? 1.0 : ($this->rates->feedRate($currency, $company)['rate'] ?? null);
            $openToday = $todayRate ? round($open / $todayRate, 2) : null;

            $averageRate = $invoicedBase != 0.0 ? abs($invoiced / $invoicedBase) : null;

            $lines[] = [
                'id'              => $currency->getKey(),
                'code'            => $currency->name,
                'is_base'         => $currency->getKey() === $base->getKey(),
                'count'           => (int) $row->document_count,
                'invoiced'        => $invoiced,
                'invoiced_base'   => $invoicedBase,
                'received'        => round($invoiced - $open, 2),
                'received_base'   => round($invoicedBase - $openBase, 2),
                'open'            => $open,
                'open_base'       => $openBase,
                'open_base_today' => $openToday,
                'difference'      => $openToday === null ? null : round($openToday - $openBase, 2),
                'average_rate'    => $averageRate && $currency->getKey() !== $base->getKey() ? $this->rateLabel($averageRate, $base, $currency) : null,
            ];

            $totals['count'] += (int) $row->document_count;
            $totals['invoiced_base'] += $invoicedBase;
            $totals['open_base'] += $openBase;

            if ($openToday !== null) {
                $totals['open_base_today'] += $openToday;
                $totals['difference'] += $openToday - $openBase;
            }
        }

        // Base currency first, then by code.
        usort($lines, fn (array $a, array $b): int => [$b['is_base'], $a['code']] <=> [$a['is_base'], $b['code']]);

        return [
            'base'       => $base?->name,
            'currencies' => $lines,
            'totals'     => array_map(fn ($value) => is_float($value) ? round($value, 2) : $value, $totals),
            'exchange'   => $this->exchangeDifferences($company, $dateFrom, $dateTo),
            'documents'  => $this->documents($documents(), $sign, $base),
            'date_from'  => $dateFrom,
            'date_to'    => $dateTo,
        ];
    }

    /**
     * "1 USD = 11.80 GHS" for a stored rate (units of the document's
     * currency per one of the company's), the way the invoice dialog shows it.
     */
    public function rateLabel(float $rate, Currency $base, Currency $currency): string
    {
        $shown = $this->rates->display($rate, $base, $currency);

        return '1 '.$shown['from'].' = '.rtrim(rtrim(number_format($shown['amount'], 4, '.', ''), '0'), '.').' '.$shown['to'];
    }

    /**
     * Gains and losses the exchange-difference entries booked in the period,
     * so the report agrees with the ledger.
     *
     * @return array{gains: float, losses: float, net: float}
     */
    protected function exchangeDifferences(?Company $company, Carbon $dateFrom, Carbon $dateTo): array
    {
        $settings = new DefaultAccountSettings;
        $income = $settings->income_currency_exchange_account_id;
        $expense = $settings->expense_currency_exchange_account_id;

        if (! $company || (! $income && ! $expense)) {
            return ['gains' => 0.0, 'losses' => 0.0, 'net' => 0.0];
        }

        $sums = DB::table('accounts_account_move_lines')
            ->join('accounts_account_moves', 'accounts_account_moves.id', '=', 'accounts_account_move_lines.move_id')
            ->where('accounts_account_moves.company_id', $company->getKey())
            ->where('accounts_account_moves.state', MoveState::POSTED->value)
            ->whereBetween('accounts_account_moves.date', [$dateFrom->toDateString(), $dateTo->toDateString()])
            ->whereIn('accounts_account_move_lines.account_id', array_filter([$income, $expense]))
            ->groupBy('accounts_account_move_lines.account_id')
            ->get(['accounts_account_move_lines.account_id', DB::raw('SUM(accounts_account_move_lines.balance) as total')])
            ->mapWithKeys(fn (object $row): array => [(int) $row->account_id => (float) $row->total]);

        // Income is a credit (negative balance), so a gain reads positive.
        $gains = round(-($sums[(int) $income] ?? 0.0), 2);
        $losses = round($sums[(int) $expense] ?? 0.0, 2);

        return ['gains' => $gains, 'losses' => $losses, 'net' => round($gains - $losses, 2)];
    }

    /**
     * @return list<array<string, mixed>>
     */
    protected function documents(QueryBuilder $query, string $sign, ?Currency $base): array
    {
        $rows = $query
            ->leftJoin('partners_partners', 'partners_partners.id', '=', 'accounts_account_moves.partner_id')
            ->orderByDesc('accounts_account_moves.invoice_date')
            ->orderByDesc('accounts_account_moves.id')
            ->limit(self::DOCUMENT_LIMIT)
            ->get([
                'accounts_account_moves.id',
                'accounts_account_moves.name',
                'accounts_account_moves.move_type',
                'accounts_account_moves.invoice_date',
                'accounts_account_moves.currency_id',
                'accounts_account_moves.invoice_currency_rate',
                'accounts_account_moves.payment_state',
                'partners_partners.name as partner',
                DB::raw("{$sign} * accounts_account_moves.amount_total as amount"),
                DB::raw("{$sign} * ABS(accounts_account_moves.amount_total_signed) as base_amount"),
            ]);

        $currencies = Currency::query()->whereIn('id', $rows->pluck('currency_id')->filter()->unique()->all())->get()->keyBy('id');

        return $rows->map(function (object $row) use ($currencies, $base): array {
            $currency = $currencies->get($row->currency_id);
            $rate = (float) $row->invoice_currency_rate;

            return [
                'id'         => (int) $row->id,
                'name'       => $row->name,
                'move_type'  => $row->move_type,
                'date'       => $row->invoice_date ? Carbon::parse($row->invoice_date) : null,
                'partner'    => $row->partner,
                'code'       => $currency?->name,
                'amount'     => round((float) $row->amount, 2),
                'base'       => round((float) $row->base_amount, 2),
                'rate'       => $currency && $base && $currency->getKey() !== $base->getKey() && $rate > 0 ? $this->rateLabel($rate, $base, $currency) : null,
                'state'      => $row->payment_state,
            ];
        })->all();
    }
}

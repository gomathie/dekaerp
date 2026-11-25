<?php

namespace Webkul\Account\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use InvalidArgumentException;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\CompanySetting;
use Webkul\Account\Models\Move;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * "Ask which currency each sale is in" (plan #10 MC-2, TRACE365, user
 * 2026-11-24). Every rule of the question lives here; the invoice page only
 * shows what this returns.
 *
 * A stored rate (accounts_account_moves.invoice_currency_rate) is units of
 * the invoice's currency per one unit of the company's: 11.8 for a GHS
 * invoice in a USD company. People read rates the other way round when that
 * number is below 1, so the dialog always shows "1 X = n Y" with n of at
 * least 1, and display() / fromDisplay() convert between the two.
 */
class SaleCurrencyQuestion
{
    /** A feed rate older than this many days is shown with a warning (D-MC-3). */
    public const STALE_AFTER_DAYS = 3;

    /**
     * Asked only when the company switched it on and can actually choose:
     * with a single permitted currency there is nothing to ask.
     */
    public function isAsked(?int $companyId): bool
    {
        if (! $companyId || ! CompanySetting::forCompany($companyId)->ask_sale_currency) {
            return false;
        }

        return count($this->options($companyId)) > 1;
    }

    /**
     * The company's currencies, base first.
     *
     * @return array<int, string> id => code
     */
    public function options(int $companyId): array
    {
        $baseId = (int) Company::withoutGlobalScopes()->whereKey($companyId)->value('currency_id');

        return Currency::query()
            ->active()
            ->permittedFor($companyId)
            ->get(['id', 'name'])
            ->sortBy(fn (Currency $currency): string => ($currency->id === $baseId ? '0' : '1').$currency->name)
            ->mapWithKeys(fn (Currency $currency): array => [$currency->id => $currency->name])
            ->all();
    }

    /**
     * The rate the daily feed gives for an invoice in $currency today, with
     * the date of the rate it rests on. Null when there is no real rate: a
     * missing rate is never 1 (Currency::canConvertTo()).
     *
     * @return array{rate: float, date: ?Carbon, stale: bool}|null
     */
    public function feedRate(Currency $currency, Company $company): ?array
    {
        $base = $company->currency;

        if (! $base || ! $base->canConvertTo($currency, $company)) {
            return null;
        }

        $rate = (float) $currency->getConversionRate(fromCurrency: $base, toCurrency: $currency, company: $company);

        if ($rate <= 0) {
            return null;
        }

        $latest = $currency->rates()
            ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $company->getKey()))
            ->whereDate('name', '<=', now()->toDateString())
            ->max('name');

        $date = $latest ? Carbon::parse($latest) : null;

        return [
            'rate'  => $rate,
            'date'  => $date,
            'stale' => $date !== null && $date->lt(now()->startOfDay()->subDays(self::STALE_AFTER_DAYS)),
        ];
    }

    /**
     * How to show a stored rate: "1 USD = 11.80 GHS" rather than
     * "1 USD = 0.0847 GHS".
     *
     * @return array{from: string, to: string, amount: float, baseFirst: bool}
     */
    public function display(float $rate, Currency $base, Currency $currency): array
    {
        $baseFirst = $rate >= 1;

        return [
            'from'      => $baseFirst ? $base->name : $currency->name,
            'to'        => $baseFirst ? $currency->name : $base->name,
            'amount'    => round($baseFirst ? $rate : 1 / $rate, 6),
            'baseFirst' => $baseFirst,
        ];
    }

    /**
     * "1 USD = 12.5 GHS", as the dialog showed it. One wording for the
     * invoice form, its history note and anything else that names the rate.
     */
    public function label(float $rate, Currency $base, Currency $currency): string
    {
        $shown = $this->display($rate, $base, $currency);

        return '1 '.$shown['from'].' = '.rtrim(rtrim(number_format($shown['amount'], 6, '.', ''), '0'), '.').' '.$shown['to'];
    }

    /**
     * The line under an invoice's currency (MC-2 rate bar): the rate the
     * person confirmed, before the invoice exists ($pending) or kept on it
     * since. Null when the invoice simply follows the daily feed.
     *
     * $pending is the dialog's answer, [currency id, stored rate]. When the
     * currency was changed afterwards the answer no longer applies; saying
     * so matters, because the create page then drops it.
     *
     * $record is an invoice (rate in invoice_currency_rate) or a quotation or
     * sales order (rate in currency_rate, MC-2b): $rateColumn names it.
     *
     * @param  array{0: ?int, 1: ?float}|null  $pending
     */
    public function keptRateText(?int $currencyId, ?Company $company, ?Model $record = null, ?array $pending = null, string $rateColumn = 'invoice_currency_rate'): ?string
    {
        $lang = 'accounts::filament/resources/invoice/pages/create-invoice.sale-currency';
        $base = $company?->currency;
        $currency = $currencyId ? Currency::find($currencyId) : null;

        if (! $base || ! $currency) {
            return null;
        }

        if ($record?->exists && $record->currency_rate_source === Move::RATE_MANUAL && (int) $record->currency_id === $currencyId) {
            $rate = $this->label((float) $record->{$rateColumn}, $base, $currency);
            $name = $record->currency_rate_confirmed_by
                ? User::query()->whereKey($record->currency_rate_confirmed_by)->value('name')
                : null;

            return $name ? __($lang.'.kept-by', ['rate' => $rate, 'name' => $name]) : __($lang.'.kept', ['rate' => $rate]);
        }

        [$pendingCurrencyId, $pendingRate] = ($pending ?? []) + [null, null];

        if ($pendingRate === null) {
            return null;
        }

        return (int) $pendingCurrencyId === $currencyId
            ? __($lang.'.kept', ['rate' => $this->label($pendingRate, $base, $currency)])
            : __($lang.'.dropped');
    }

    /**
     * The summary shown before an invoice or bill in another currency is
     * posted (MC-2 step 5): "Total USD 1,200.00 = GH₵ 14,160.00 at
     * 1 USD = 11.8 GHS. Confirm and post it?". After posting the rate never
     * changes, so this is the last moment to notice a wrong one.
     *
     * The company value comes from the document's own stored figures, never
     * from today's rate: it is what the books will carry. Null for a
     * document in the company's own currency (and for journal entries),
     * which confirm without a dialog, as before.
     */
    public function postingSummary(Move $move): ?string
    {
        $lang = 'accounts::filament/resources/invoice/actions/confirm-action.currency-summary';
        $base = $move->company?->currency;
        $currency = $move->currency;

        if (! $move->isInvoice(true) || ! $base || ! $currency || (int) $currency->getKey() === (int) $base->getKey()) {
            return null;
        }

        $rate = (float) $move->invoice_currency_rate;
        $total = abs((float) $move->amount_total);
        $companyTotal = abs((float) $move->amount_total_signed);

        // A draft whose company total was never computed still has a total
        // and a stored rate; derive the value from those rather than show 0.
        if ($companyTotal <= 0 && $total > 0 && $rate > 0) {
            $companyTotal = $total / $rate;
        }

        $sentences = [__($lang.'.total', [
            'amount' => money($total, $currency->name),
            'value'  => money($companyTotal, $base->name),
            // label() divides by the rate; a zero rate must not break posting.
            'rate'   => $rate > 0 ? $this->label($rate, $base, $currency) : '—',
        ])];

        // The stored rate belongs to the invoice date; on a back- or
        // forward-dated document people otherwise assume it is today's.
        if ($move->invoice_date && ! $move->invoice_date->isToday()) {
            $sentences[] = __($lang.'.rate-date', ['date' => $move->invoice_date->translatedFormat('j M Y')]);
        }

        // MC-2 step 5 "offers today's rate": what the latest rate would book,
        // so the choice between the two buttons is made with both figures.
        if ($offer = $this->latestRateOffer($move)) {
            $sentences[] = __($lang.'.latest', [
                'rate'  => $offer['label'],
                'date'  => $offer['date']?->translatedFormat('j M Y') ?? '—',
                'value' => money($total / $offer['rate'], $base->name),
            ]);
        }

        $sentences[] = __($lang.'.question');

        return implode(' ', $sentences);
    }

    /**
     * The latest feed rate, offered at posting when the document is dated
     * another day and carries a different rate (MC-2 step 5). Null when
     * there is nothing to offer: a document dated today, one already at the
     * latest rate, or no feed rate at all (a missing rate is never 1).
     *
     * @return array{rate: float, date: ?Carbon, stale: bool, label: string}|null
     */
    public function latestRateOffer(Move $move): ?array
    {
        $company = $move->company;
        $base = $company?->currency;
        $currency = $move->currency;

        if (! $move->isInvoice(true) || ! $base || ! $currency || (int) $currency->getKey() === (int) $base->getKey()) {
            return null;
        }

        if (! $move->invoice_date || $move->invoice_date->isToday()) {
            return null;
        }

        $feed = $this->feedRate($currency, $company);

        if ($feed === null || abs($feed['rate'] - (float) $move->invoice_currency_rate) <= 1e-9 * max(1.0, $feed['rate'])) {
            return null;
        }

        return $feed + ['label' => $this->label($feed['rate'], $base, $currency)];
    }

    /**
     * Puts the latest rate on a draft before it is posted, when the person
     * chose "Post at the latest rate". It is kept as a confirmed rate
     * (manual, under their name): a feed rate would be re-read for the
     * invoice date on the next save and silently undo the choice. The lines
     * are re-costed at it and the history says who chose which rate.
     *
     * Refuses a document that is not a draft, or has nothing to offer: the
     * rate of a posted entry never changes.
     */
    public function applyLatestRate(Move $move, int $userId): Move
    {
        $offer = $this->latestRateOffer($move);

        if ($move->state !== MoveState::DRAFT || $offer === null) {
            throw new InvalidArgumentException('There is no other rate to post this document at.');
        }

        $move->forceFill([
            'invoice_currency_rate'      => $offer['rate'],
            'currency_rate_source'       => Move::RATE_MANUAL,
            'currency_rate_confirmed_by' => $userId,
        ])->save();

        $move = AccountFacade::computeAccountMove($move);

        $move->addMessage([
            'type' => 'note',
            'body' => e(__('accounts::filament/resources/invoice/actions/confirm-action.currency-summary.latest-note', [
                'rate' => $offer['label'],
                'date' => $offer['date']?->translatedFormat('j M Y') ?? '—',
            ])),
        ]);

        return $move;
    }

    /**
     * The stored rate for what the person typed in display() order.
     */
    public function fromDisplay(float $amount, bool $baseFirst): float
    {
        if ($amount <= 0) {
            throw new InvalidArgumentException('A rate must be greater than zero.');
        }

        return $baseFirst ? $amount : 1 / $amount;
    }
}

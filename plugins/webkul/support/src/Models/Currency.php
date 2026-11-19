<?php

namespace Webkul\Support\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Webkul\Field\Traits\HasCustomFields;
use Webkul\Support\Database\Factories\CurrencyFactory;

class Currency extends Model
{
    use HasCustomFields, HasFactory;

    protected $fillable = [
        'name',
        'symbol',
        'iso_numeric',
        'decimal_places',
        'full_name',
        'rounding',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function getCodeAttribute(): ?string
    {
        return $this->name;
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('active', true);
    }

    /**
     * Currencies a company may pick on a document (CR-6, 2026-11-13).
     *
     * No list for the company means no restriction, which is what every
     * company had before. With a list, the base currency is always added, so
     * a company can never be locked out of its own accounting currency.
     * Both reads skip global scopes: the actor's company scope must not
     * decide what another company's document may use.
     */
    public function scopePermittedFor(Builder $query, ?int $companyId): Builder
    {
        if (! $companyId) {
            return $query;
        }

        $ids = DB::table('company_currencies')->where('company_id', $companyId)->pluck('currency_id');

        if ($ids->isEmpty()) {
            return $query;
        }

        $baseId = DB::table('companies')->where('id', $companyId)->value('currency_id');

        return $query->whereIn($query->qualifyColumn('id'), $ids->push($baseId)->filter()->unique()->all());
    }

    public static function findByCode(?string $code): ?self
    {
        if (blank($code)) {
            return null;
        }

        return static::query()->where('name', $code)->first();
    }

    public static function resolveDefault(?Country $country = null): ?self
    {
        return $country?->currency
            ?? static::findByCode(config('app.currency'))
            ?? static::query()->first();
    }

    public function rates(): HasMany
    {
        return $this->hasMany(CurrencyRate::class);
    }

    public function companies(): HasMany
    {
        return $this->hasMany(Company::class);
    }

    public function isInUse(): bool
    {
        return $this->companies()->exists();
    }

    public function convert(float|int $fromAmount, Currency $toCurrency, ?Company $company = null, $date = null, bool $round = true): float
    {
        $base = $this ?? $toCurrency;

        $toCurrency = $toCurrency ?? $this;

        if ($fromAmount) {
            $rate = $this->getConversionRate($base, $toCurrency, $company, $date);

            $toAmount = $fromAmount * $rate;
        } else {
            return 0.0;
        }

        return $round ? $toCurrency->round($toAmount) : $toAmount;
    }

    public function getConversionRate($fromCurrency, $toCurrency, $company = null, $date = null)
    {
        if ($fromCurrency->id === $toCurrency->id) {
            return 1;
        }

        $company = $company ?? current_company();

        $date = $date ?? now()->toDateString();

        $fromRate = $this->resolveRate($fromCurrency, $company, $date);

        $toRate = $this->resolveRate($toCurrency, $company, $date);

        return $toRate / $fromRate;
    }

    /**
     * Whether an amount can be converted from this currency into another
     * with a real rate. resolveRate() falls back to 1.0 when no rate is set,
     * so without this check a 1,000 GHS price silently became 1,000 USD on a
     * USD invoice (client report, 2026-11-19). A company's own base currency
     * counts as rate 1; any other currency needs a rate on or before the date.
     */
    public function canConvertTo(self $toCurrency, ?Company $company = null, $date = null): bool
    {
        if ($this->id === $toCurrency->id) {
            return true;
        }

        $company = $company ?? current_company();
        $date = $date ?? now()->toDateString();

        foreach ([$this, $toCurrency] as $currency) {
            if ($company && (int) $company->currency_id === (int) $currency->id) {
                continue;
            }

            $hasRate = $currency->rates()
                ->where(function ($query) use ($company) {
                    $query->whereNull('company_id');

                    if ($company) {
                        $query->orWhere('company_id', $company->id);
                    }
                })
                ->whereDate('name', '<=', $date)
                ->where('rate', '>', 0)
                ->exists();

            if (! $hasRate) {
                return false;
            }
        }

        return true;
    }

    protected function resolveRate(self $currency, ?Company $company, string $date): float
    {
        $rate = $currency->rates()
            ->where(function ($query) use ($company) {
                $query->whereNull('company_id');

                if ($company) {
                    $query->orWhere('company_id', $company->id);
                }
            })
            ->whereDate('name', '<=', $date)
            ->orderByDesc('name')
            ->value('rate');

        return $rate > 0 ? (float) $rate : 1.0;
    }

    public function round(float $amount): float
    {
        return float_round($amount, precisionRounding: $this->rounding);
    }

    public function compareAmounts($amount1, $amount2)
    {
        return float_compare($amount1, $amount2, precisionRounding: $this->rounding);
    }

    public function isZero($amount)
    {
        return $this->floatIsZero($amount, precisionRounding: $this->rounding);
    }

    protected function floatIsZero($value, $precisionDigits = null, $precisionRounding = null)
    {
        $epsilon = $this->floatCheckPrecision($precisionDigits, $precisionRounding);

        return $value == 0.0 || abs($this->floatRound($value, $epsilon)) < $epsilon;
    }

    protected function floatCheckPrecision($precisionDigits = null, $precisionRounding = null)
    {
        if ($precisionRounding !== null && $precisionDigits === null) {
            if ($precisionRounding <= 0) {
                throw new InvalidArgumentException("precision_rounding must be positive, got {$precisionRounding}");
            }

            return $precisionRounding;
        } elseif ($precisionDigits !== null && $precisionRounding === null) {
            if (! is_int($precisionDigits) && ! $this->isInteger($precisionDigits)) {
                throw new InvalidArgumentException("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }

            if ($precisionDigits < 0) {
                throw new InvalidArgumentException("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }

            return pow(10, -$precisionDigits);
        } else {
            throw new InvalidArgumentException('exactly one of precision_digits and precision_rounding must be specified');
        }
    }

    protected function floatRound($value, $precisionRounding)
    {
        if ($precisionRounding == 0) {
            return $value;
        }

        return round($value / $precisionRounding) * $precisionRounding;
    }

    protected function isInteger($value)
    {
        return is_numeric($value) && floatval($value) == intval($value);
    }

    protected static function newFactory()
    {
        return CurrencyFactory::new();
    }
}

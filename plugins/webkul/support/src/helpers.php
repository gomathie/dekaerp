<?php

use ArPHP\I18N\Arabic;
use Filament\Forms\Components\Field;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;
use Webkul\Security\Settings\CurrencySettings;
use Webkul\Support\Database\Dialects\DatabaseDialect;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;
use Webkul\Support\Services\CompanyContext;
use Webkul\Support\SettingsRegistry;
use Webkul\Support\SupportServiceProvider;

if (! function_exists('settings')) {
    function settings(string $settings): object
    {
        return app(SettingsRegistry::class)->get($settings);
    }
}

if (! function_exists('default_currency_code')) {
    /**
     * The ISO code money amounts are displayed in when no currency is given.
     */
    function default_currency_code(): string
    {
        return once(function (): string {
            try {
                $currencyId = settings(CurrencySettings::class)->default_currency_id;

                $code = $currencyId ? Currency::find($currencyId)?->code : null;
            } catch (Throwable) {
                $code = null;
            }

            return $code ?? config('app.currency') ?? 'USD';
        });
    }
}

if (! function_exists('db_dialect')) {
    function db_dialect(): DatabaseDialect
    {
        return app(DatabaseDialect::class);
    }
}

if (! function_exists('money')) {
    function money(float|Closure $amount, string|Closure|null $currency = null, int $divideBy = 0, string|Closure|null $locale = null): string
    {
        $amount = $amount instanceof Closure ? $amount() : $amount;

        $currency = $currency instanceof Closure ? $currency() : ($currency ?? config('app.currency'));

        // The session locale lives on the application, not in config: SetLocale
        // calls App::setLocale() and leaves app.locale at the .env default.
        // Reading the config here kept every dashboard and PDF in English
        // after the user switched language.
        $locale = $locale instanceof Closure ? $locale() : ($locale ?? app()->getLocale());

        if ($divideBy > 0) {
            $amount /= $divideBy;
        }

        if (SupportServiceProvider::isRtl()) {
            return Number::currency($amount, $currency, 'en');
        }

        if (! str_contains($locale, '-u-nu-')) {
            $locale .= '-u-nu-latn';
        }

        return Number::currency($amount, $currency, $locale);
    }

    if (! function_exists('random_color')) {
        function random_color(string $type = 'hex'): string
        {
            return match (strtolower($type)) {
                'rgb' => sprintf(
                    'rgb(%d, %d, %d)',
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 255)
                ),

                'rgba' => sprintf(
                    'rgba(%d, %d, %d, %.2f)',
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 100) / 100
                ),

                'hsl' => sprintf(
                    'hsl(%d, %d%%, %d%%)',
                    random_int(0, 360),
                    random_int(30, 100),
                    random_int(20, 80)
                ),

                'hex' => sprintf(
                    '#%02X%02X%02X',
                    random_int(0, 255),
                    random_int(0, 255),
                    random_int(0, 255)
                ),

                default => throw new InvalidArgumentException(
                    'Invalid color type. Use: hex, rgb, rgba, or hsl'
                ),
            };
        }
    }
}

if (! function_exists('prepare_rtl_html')) {
    function prepare_rtl_html(string $html): string
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="UTF-8">'.$html);
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        foreach ($xpath->query('//tr') as $row) {
            $cells = [];

            foreach ($row->childNodes as $child) {
                if (! $child instanceof DOMElement || ! in_array(strtolower($child->nodeName), ['td', 'th'], true)) {
                    continue;
                }

                if ($child->hasAttribute('colspan') || $child->hasAttribute('rowspan')) {
                    continue 2;
                }

                $cells[] = $child;
            }

            for ($i = count($cells) - 1; $i > 0; $i--) {
                $row->insertBefore($cells[$i], $cells[0]);
            }
        }

        if (class_exists(Arabic::class)) {
            $arabic = new Arabic;

            foreach ($xpath->query('//text()[not(ancestor::style) and not(ancestor::script)]') as $node) {
                if (trim($node->nodeValue) === '' || ! preg_match('/\p{Arabic}/u', $node->nodeValue)) {
                    continue;
                }

                $node->nodeValue = $arabic->utf8Glyphs($node->nodeValue, PHP_INT_MAX, false);
            }
        }

        return preg_replace('/<\?xml[^>]*>\s*/', '', $dom->saveHTML());
    }
}

if (! function_exists('format_float_time')) {
    function format_float_time(mixed $state, string $unit = 'minutes'): string
    {
        $value = (float) ($state ?? 0);
        $primary = (int) floor($value);
        $secondary = (int) round(($value - $primary) * 60);

        if ($secondary === 60) {
            $primary++;
            $secondary = 0;
        }

        return sprintf('%02d:%02d', $primary, $secondary);
    }
}

if (! function_exists('parse_float_time')) {
    function parse_float_time(?string $state, string $unit = 'minutes'): string
    {
        if (! is_string($state) || ! preg_match('/^(?<primary>\d+):(?<secondary>\d{2})$/', $state, $matches)) {
            return '60';
        }

        $secondary = (int) $matches['secondary'];

        if ($secondary > 59) {
            return '60';
        }

        $primary = (int) $matches['primary'];

        return (string) ($primary + ($secondary / 60));
    }
}

if (! function_exists('float_is_zero')) {
    function float_is_zero($value, $precisionDigits = null, $precisionRounding = null)
    {
        $epsilon = float_check_precision($precisionDigits, $precisionRounding);

        if ($value == 0.0) {
            return true;
        }

        return abs(float_round($value, precisionRounding: $epsilon)) < $epsilon;
    }
}

if (! function_exists('float_compare')) {
    function float_compare($value1, $value2, $precisionDigits = null, $precisionRounding = null)
    {
        $roundingFactor = float_check_precision($precisionDigits, $precisionRounding);

        if ($value1 == $value2) {
            return 0;
        }

        $value1 = float_round($value1, precisionRounding: $roundingFactor);
        $value2 = float_round($value2, precisionRounding: $roundingFactor);

        $delta = $value1 - $value2;

        if (float_is_zero($delta, null, precisionRounding: $roundingFactor)) {
            return 0;
        }

        return $delta < 0.0 ? -1 : 1;
    }
}

if (! function_exists('float_check_precision')) {
    function float_check_precision($precisionDigits = null, $precisionRounding = null)
    {
        if (! is_null($precisionRounding) && is_null($precisionDigits)) {
            if ($precisionRounding <= 0) {
                throw new AssertionError("precision_rounding must be positive, got {$precisionRounding}");
            }
        } elseif (! is_null($precisionDigits) && is_null($precisionRounding)) {
            if (! is_int($precisionDigits) && (float) $precisionDigits != floor($precisionDigits)) {
                throw new AssertionError("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }

            if ($precisionDigits < 0) {
                throw new AssertionError("precision_digits must be a non-negative integer, got {$precisionDigits}");
            }

            $precisionRounding = pow(10, -$precisionDigits);
        } else {
            throw new AssertionError('exactly one of precision_digits and precision_rounding must be specified');
        }

        return $precisionRounding;
    }
}

if (! function_exists('float_round')) {
    function float_round($value, $precisionDigits = null, $precisionRounding = null, $roundingMethod = 'HALF-UP')
    {
        $roundingFactor = float_check_precision($precisionDigits, $precisionRounding);

        if ($roundingFactor == 0 || $value == 0) {
            return 0.0;
        }

        $scaled = $value / $roundingFactor;
        $roundingMethod = strtoupper($roundingMethod);

        switch ($roundingMethod) {
            case 'HALF-UP':
                $rounded = ($scaled > 0)
                    ? floor($scaled + 0.5)
                    : ceil($scaled - 0.5);
                break;

            case 'HALF-DOWN':
                $rounded = ($scaled > 0)
                    ? ceil($scaled - 0.5)
                    : floor($scaled + 0.5);
                break;

            case 'HALF-EVEN':
                $floor = floor($scaled);
                $diff = abs($scaled - $floor);
                if ($diff == 0.5) {
                    $rounded = ($floor % 2 == 0)
                        ? $floor
                        : $floor + ($scaled > 0 ? 1 : -1);
                } else {
                    $rounded = round($scaled);
                }
                break;

            case 'UP':
                $rounded = ($scaled > 0)
                    ? ceil($scaled)
                    : floor($scaled);
                break;

            case 'DOWN':
                $rounded = ($scaled > 0)
                    ? floor($scaled)
                    : ceil($scaled);
                break;

            default:
                throw new InvalidArgumentException("Unknown rounding method: {$roundingMethod}");
        }

        return $rounded * $roundingFactor;
    }
}

if (! function_exists('make_aware')) {
    function make_aware(Carbon\Carbon $dt): array
    {
        $tz = $dt->getTimezone();

        return [$dt, fn (Carbon\Carbon $val) => $val->clone()->setTimezone($tz)];
    }
}

if (! function_exists('current_company')) {
    function current_company(): ?Company
    {
        return app(CompanyContext::class)->currentCompany();
    }
}

if (! function_exists('current_company_id')) {
    function current_company_id(): ?int
    {
        return app(CompanyContext::class)->currentId();
    }
}

if (! function_exists('owned_by_company')) {
    function owned_by_company($companyId = null): Closure
    {
        $companyId = $companyId ?: current_company_id();

        return function ($query) use ($companyId) {
            $model = $query->getModel();

            if (method_exists($model, 'companyScopeRelation')) {
                $relation = $model->companyScopeRelation();

                return $query
                    ->whereHas($relation, fn ($related) => $related->where('companies.id', $companyId))
                    ->orWhereDoesntHave($relation);
            }

            $column = $model->getTable().'.company_id';

            return $query->whereNull($column)->orWhere($column, $companyId);
        };
    }
}

if (! function_exists('reapply_company_defaults')) {
    function reapply_company_defaults($component, array $fields): void
    {
        $container = $component->getContainer();

        $prefix = $container->getStatePath();

        $root = $component->getRootContainer();

        foreach ($fields as $field) {
            $path = filled($prefix) ? $prefix.'.'.$field : $field;

            $target = $root->getComponent(
                fn ($candidate) => $candidate instanceof Field
                    && $candidate->getStatePath() === $path,
                withHidden: true,
            );

            $target?->state($target->getDefaultState());
        }
    }
}

if (! function_exists('clear_foreign_company_values')) {
    function clear_foreign_company_values($set, $get, array $fields, $companyId = null): void
    {
        $companyId = $companyId ?: current_company_id();

        foreach ($fields as $field => $model) {
            $state = $get($field);

            if (blank($state)) {
                continue;
            }

            $keyName = (new $model)->getKeyName();

            $allowed = $model::query()
                ->withoutGlobalScopes()
                ->whereKey($state)
                ->where(owned_by_company($companyId))
                ->pluck($keyName)
                ->all();

            if (is_array($state)) {
                $kept = array_values(array_filter($state, fn ($key) => in_array($key, $allowed)));

                if (count($kept) !== count($state)) {
                    $set($field, $kept);
                }

                continue;
            }

            if (! in_array($state, $allowed)) {
                $set($field, null);
            }
        }
    }
}

if (! function_exists('allowed_companies')) {
    function allowed_companies(): Collection
    {
        return app(CompanyContext::class)->allowedCompanies();
    }
}

if (! function_exists('allowed_company_ids')) {
    function allowed_company_ids(): array
    {
        return app(CompanyContext::class)->allowedIds();
    }
}

if (! function_exists('app_number_locale')) {
    /**
     * Locale ICU should use for money and counts.
     *
     * Arabic keeps Latin digits, matching the existing money() fallback.
     * Russian and Swahili (and the other languages) are passed through with
     * Latin digits as well: ru and sw are already region-neutral, the same
     * way DashboardPeriod::browserLocale() leaves them, so a Russian session
     * formats as ru and a Swahili session as sw.
     */
    function app_number_locale(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        if (str_starts_with($locale, 'ar')) {
            $locale = 'en';
        }

        if (! str_contains($locale, '-u-nu-')) {
            $locale .= '-u-nu-latn';
        }

        return $locale;
    }
}

if (! function_exists('local_number')) {
    /**
     * A count or plain number in the active locale, with a non-ICU fallback.
     *
     * ext-intl is not required. Without it, number_format() still renders.
     */
    function local_number(float|int $number): string
    {
        if (! class_exists(MessageFormatter::class)) {
            return number_format($number);
        }

        $formatted = MessageFormatter::formatMessage(
            app_number_locale(),
            '{0, number}',
            [$number],
        );

        return $formatted !== false ? $formatted : number_format($number);
    }
}

if (! function_exists('locale_flag_code')) {
    /**
     * Flag file for a locale switcher entry.
     *
     * The switcher used to look up flags/{locale}.svg, and the files that
     * exist are en, ar, es and pt_BR. Swahili is the locale sw (D-LANG-1)
     * and its flag is Tanzania, tz. Prefer the configured flag when that
     * svg is present, otherwise the locale code.
     */
    function locale_flag_code(string $locale): string
    {
        $configured = (string) data_get(config('app.supported_locales'), $locale.'.flag', '');

        foreach ([$configured, $locale] as $candidate) {
            if ($candidate !== '' && is_file(public_path('flags/'.$candidate.'.svg'))) {
                return $candidate;
            }
        }

        return $configured !== '' ? $configured : $locale;
    }
}

if (! function_exists('compact_money')) {
    /**
     * Abbreviate a money amount to a compact, locale-aware form (for example $786K).
     *
     * Ported from upstream for the module dashboards. Arabic falls back to
     * Latin digits, as upstream does, and anything ICU cannot format (or a
     * host without ext-intl, which composer.json does not require) falls back
     * to the full money() string rather than failing the dashboard.
     */
    function compact_money(float $amount, ?string $currency = null): string
    {
        $currency ??= config('app.currency');

        if (! class_exists(MessageFormatter::class)) {
            return money($amount, $currency);
        }

        $locale = app_number_locale();

        $formatted = MessageFormatter::formatMessage(
            $locale,
            '{0, number, :: compact-short currency/'.$currency.'}',
            [$amount],
        );

        return $formatted !== false ? $formatted : money($amount, $currency);
    }
}

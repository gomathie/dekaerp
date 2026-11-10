<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Schemas;

use DateTimeImmutable;
use DateTimeZone;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Schemas\PlanForm;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Support\Money;
use Webkul\Support\Models\Company;

class SubscriptionForm
{
    protected static string $lang = 'platform-billing::filament/billing.subscriptions.form';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')
                ->label(__(static::$lang.'.company'))
                ->options(fn (): array => Company::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->required(),
            Select::make('price_id')
                ->label(__(static::$lang.'.price'))
                ->options(fn (): array => static::priceOptions())
                ->searchable()
                ->required(),
            DateTimePicker::make('anchor_at')
                ->label(__(static::$lang.'.anchor'))
                ->helperText(__(static::$lang.'.anchor-help'))
                ->default(fn (): string => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'))
                ->required(),
        ]);
    }

    /**
     * @return array<int, string>
     */
    public static function priceOptions(): array
    {
        return Price::query()
            ->where('is_active', true)
            ->where('currency', config('platform-billing.currency'))
            ->whereHas('plan', fn ($query) => $query->where('is_active', true))
            ->with('plan:id,name')
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (Price $price): array {
                $amount = Money::of((int) $price->amount_minor, (string) $price->currency)->toDecimal();
                $interval = PlanForm::intervals()[(int) $price->interval_months] ?? (string) $price->interval_months;

                return [$price->getKey() => $price->plan->name.' — '.$amount.' '.$price->currency.' / '.$interval];
            })
            ->all();
    }
}

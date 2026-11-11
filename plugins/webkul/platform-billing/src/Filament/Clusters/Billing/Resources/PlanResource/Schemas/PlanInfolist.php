<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Schemas\PlanForm as PlanFields;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Support\Money;

class PlanInfolist
{
    protected static string $lang = 'platform-billing::filament/billing.plans';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('name')
                ->label(__(static::$lang.'.columns.name')),
            TextEntry::make('code')
                ->label(__(static::$lang.'.columns.code')),
            IconEntry::make('is_active')
                ->label(__(static::$lang.'.columns.active'))
                ->boolean(),
            TextEntry::make('seat_limit')
                ->label(__(static::$lang.'.columns.seats'))
                ->placeholder(__(static::$lang.'.infolist.unlimited')),
            TextEntry::make('included_plugins')
                ->label(__(static::$lang.'.infolist.modules'))
                ->state(function (Plan $record): string {
                    $names = is_array($record->included_plugins) ? $record->included_plugins : [];

                    return $names === [] ? __(static::$lang.'.infolist.none') : implode(', ', $names);
                }),
            TextEntry::make('prices_summary')
                ->label(__(static::$lang.'.infolist.prices'))
                ->state(function (Plan $record): string {
                    return $record->prices
                        ->map(function ($price): string {
                            $amount = Money::of((int) $price->amount_minor, (string) $price->currency)->toDecimal();
                            $interval = PlanFields::intervals()[(int) $price->interval_months] ?? (string) $price->interval_months;
                            $state = $price->is_active
                                ? __(static::$lang.'.columns.active')
                                : __(static::$lang.'.columns.inactive');

                            return $interval.' — '.$amount.' '.$price->currency.' ('.$state.')';
                        })
                        ->implode("\n");
                })
                ->placeholder(__(static::$lang.'.infolist.no-prices')),
        ]);
    }
}

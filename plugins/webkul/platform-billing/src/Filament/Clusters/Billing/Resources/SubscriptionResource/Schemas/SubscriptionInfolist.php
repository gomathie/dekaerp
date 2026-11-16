<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Schemas\PlanForm;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Support\Money;

class SubscriptionInfolist
{
    protected static string $lang = 'platform-billing::filament/billing.subscriptions';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('company.name')
                ->label(__(static::$lang.'.columns.company')),
            TextEntry::make('plan_name')
                ->label(__(static::$lang.'.columns.plan')),
            TextEntry::make('status')
                ->label(__(static::$lang.'.columns.status'))
                ->formatStateUsing(fn (mixed $state): string => __(static::$lang.'.statuses.'.$state)),
            TextEntry::make('amount_minor')
                ->label(__(static::$lang.'.columns.amount'))
                ->formatStateUsing(fn (mixed $state, Subscription $record): string => Money::of((int) $record->amount_minor, (string) $record->currency)->toDecimal()),
            TextEntry::make('currency')
                ->label(__(static::$lang.'.columns.currency')),
            TextEntry::make('interval_months')
                ->label(__(static::$lang.'.columns.interval'))
                ->formatStateUsing(fn (mixed $state): string => PlanForm::intervals()[(int) $state] ?? (string) $state),
            TextEntry::make('anchor_at')
                ->label(__(static::$lang.'.columns.anchor'))
                ->dateTime(),
            TextEntry::make('period_start_at')
                ->label(__(static::$lang.'.columns.period-start'))
                ->dateTime()
                ->placeholder('—'),
            TextEntry::make('period_end_at')
                ->label(__(static::$lang.'.columns.period-end'))
                ->dateTime()
                ->placeholder('—'),
            TextEntry::make('next_invoice_at')
                ->label(__(static::$lang.'.columns.next-invoice'))
                ->dateTime()
                ->placeholder('—'),
            IconEntry::make('cancel_at_period_end')
                ->label(__(static::$lang.'.columns.cancel'))
                ->boolean(),
            TextEntry::make('scheduled_price_id')
                ->label(__(static::$lang.'.columns.scheduled'))
                ->placeholder(__(static::$lang.'.infolist.none')),
            TextEntry::make('seat_limit')
                ->label(__(static::$lang.'.columns.seats'))
                ->placeholder(__(static::$lang.'.infolist.unlimited')),
        ]);
    }
}

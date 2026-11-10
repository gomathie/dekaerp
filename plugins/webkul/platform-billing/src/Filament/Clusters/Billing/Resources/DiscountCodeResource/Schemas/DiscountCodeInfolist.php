<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class DiscountCodeInfolist
{
    protected static string $lang = 'platform-billing::filament/billing.discounts';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('code')->label(__(static::$lang.'.columns.code')),
            TextEntry::make('discount_type')->label(__(static::$lang.'.columns.type')),
            TextEntry::make('discount_value')->label(__(static::$lang.'.columns.value')),
            TextEntry::make('duration_type')->label(__(static::$lang.'.columns.duration')),
            TextEntry::make('eligibility_type')->label(__(static::$lang.'.columns.eligibility')),
            IconEntry::make('is_active')->label(__(static::$lang.'.columns.active'))->boolean(),
            TextEntry::make('redemption_count')->label(__(static::$lang.'.columns.redeemed')),
        ]);
    }
}

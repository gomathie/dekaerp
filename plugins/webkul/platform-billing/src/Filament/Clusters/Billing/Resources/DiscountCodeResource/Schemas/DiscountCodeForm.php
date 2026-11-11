<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Webkul\Support\Models\Company;

class DiscountCodeForm
{
    protected static string $lang = 'platform-billing::filament/billing.discounts.form';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('code')
                ->label(__(static::$lang.'.code'))
                ->required()
                ->maxLength(100),
            Select::make('discount_type')
                ->label(__(static::$lang.'.type'))
                ->options([
                    'percentage'  => __(static::$lang.'.types.percentage'),
                    'fixed_usd'   => __(static::$lang.'.types.fixed'),
                    'free_months' => __(static::$lang.'.types.free'),
                ])
                ->required()
                ->native(false),
            TextInput::make('discount_value')
                ->label(__(static::$lang.'.value'))
                ->helperText(__(static::$lang.'.value-help'))
                ->required(),
            Select::make('duration_type')
                ->label(__(static::$lang.'.duration'))
                ->options([
                    'one_invoice' => __(static::$lang.'.durations.one'),
                    'months'      => __(static::$lang.'.durations.months'),
                    'forever'     => __(static::$lang.'.durations.forever'),
                ])
                ->required()
                ->native(false),
            TextInput::make('duration_months')
                ->label(__(static::$lang.'.months'))
                ->integer()
                ->minValue(1),
            Select::make('eligibility_type')
                ->label(__(static::$lang.'.eligibility'))
                ->options([
                    'any_company'  => __(static::$lang.'.eligibilities.any'),
                    'company_list' => __(static::$lang.'.eligibilities.list'),
                ])
                ->required()
                ->native(false),
            Select::make('company_ids')
                ->label(__(static::$lang.'.companies'))
                ->options(fn (): array => Company::query()->orderBy('name')->pluck('name', 'id')->all())
                ->multiple()
                ->native(false),
            TextInput::make('max_redemptions')
                ->label(__(static::$lang.'.limit'))
                ->integer()
                ->minValue(1),
        ]);
    }
}

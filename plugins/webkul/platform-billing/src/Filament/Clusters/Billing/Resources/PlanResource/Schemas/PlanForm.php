<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Schemas;

use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Webkul\PluginManager\Models\Plugin;

class PlanForm
{
    protected static string $lang = 'platform-billing::filament/billing.plans.form';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->label(__(static::$lang.'.name'))
                ->required()
                ->maxLength(255),
            TextInput::make('code')
                ->label(__(static::$lang.'.code'))
                ->required()
                ->maxLength(255),
            CheckboxList::make('included_plugins')
                ->label(__(static::$lang.'.modules'))
                ->options(fn (): array => Plugin::query()
                    ->where('is_installed', true)
                    ->orderBy('name')
                    ->pluck('name', 'name')
                    ->all())
                ->columns(2)
                ->bulkToggleable(),
            TextInput::make('seat_limit')
                ->label(__(static::$lang.'.seats'))
                ->helperText(__(static::$lang.'.seats-help'))
                ->integer()
                ->minValue(0),
            TextInput::make('amount')
                ->label(__(static::$lang.'.amount'))
                ->helperText(__(static::$lang.'.amount-help'))
                ->required(),
            Select::make('interval_months')
                ->label(__(static::$lang.'.interval'))
                ->options(static::intervals())
                ->required()
                ->native(false),
        ]);
    }

    /**
     * @return array<int, string>
     */
    public static function intervals(): array
    {
        return [
            1  => __(static::$lang.'.intervals.1'),
            3  => __(static::$lang.'.intervals.3'),
            6  => __(static::$lang.'.intervals.6'),
            12 => __(static::$lang.'.intervals.12'),
        ];
    }
}

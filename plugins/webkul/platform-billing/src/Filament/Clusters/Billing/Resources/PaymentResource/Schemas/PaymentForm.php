<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Webkul\Support\Models\Company;

class PaymentForm
{
    protected static string $lang = 'platform-billing::filament/billing.payments.form';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')
                ->label(__(static::$lang.'.company'))
                ->options(fn (): array => Company::query()->orderBy('name')->pluck('name', 'id')->all())
                ->searchable()
                ->required()
                ->native(false),
            TextInput::make('amount')
                ->label(__(static::$lang.'.amount'))
                ->helperText(__(static::$lang.'.amount-help'))
                ->required(),
            TextInput::make('reference')
                ->label(__(static::$lang.'.reference'))
                ->helperText(__(static::$lang.'.reference-help'))
                ->required()
                ->maxLength(255),
            DateTimePicker::make('received_at')
                ->label(__(static::$lang.'.received'))
                ->required(),
        ]);
    }
}

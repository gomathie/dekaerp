<?php

namespace Webkul\Account;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Services\BaseCurrencyChange;
use Webkul\Support\Filament\Resources\CompanyResource\Support\CompanySchemaRegistry;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\Currency;

/**
 * "Change base currency" on the company edit page (D-CR-4, 2026-11-13).
 *
 * The plain currency field stays refused by CompanyObserver once entries
 * exist; this action is the only way past it, and every rule lives in
 * BaseCurrencyChange. Contributed through CompanySchemaRegistry because
 * `support` must not reference `accounts`.
 */
class CompanyCurrencyContributions
{
    /**
     * Registry contributions accumulate and packageBooted() can run more than
     * once, which would render the section twice.
     */
    protected static bool $registered = false;

    public static function register(): void
    {
        if (static::$registered) {
            return;
        }

        static::$registered = true;

        CompanySchemaRegistry::form('main.after', fn (): array => [static::baseCurrencySection()]);
    }

    protected static function baseCurrencySection(): Section
    {
        return Section::make(__('accounts::filament/contributions/company.base-currency.title'))
            ->description(__('accounts::filament/contributions/company.base-currency.description'))
            ->icon('heroicon-o-banknotes')
            ->visible(fn (string $operation): bool => $operation === 'edit' && BaseCurrencyChange::mayChange(Auth::guard('web')->user()))
            ->schema([
                Actions::make([
                    Action::make('changeBaseCurrency')
                        ->label(__('accounts::filament/contributions/company.base-currency.action'))
                        ->icon('heroicon-o-arrows-right-left')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('accounts::filament/contributions/company.base-currency.action'))
                        ->modalDescription(fn (?Company $record): string => __(
                            'accounts::filament/contributions/company.base-currency.warning',
                            ['count' => $record ? app(BaseCurrencyChange::class)->postedEntryCount($record) : 0],
                        ))
                        ->schema([
                            Select::make('currency_id')
                                ->label(__('accounts::filament/contributions/company.base-currency.fields.currency'))
                                ->options(fn (): array => Currency::query()->where('active', true)->orderBy('name')->pluck('full_name', 'id')->all())
                                ->searchable()
                                ->required(),
                            Textarea::make('reason')
                                ->label(__('accounts::filament/contributions/company.base-currency.fields.reason'))
                                ->maxLength(1000),
                        ])
                        ->action(function (array $data, ?Company $record): void {
                            if (! $record instanceof Company) {
                                return;
                            }

                            app(BaseCurrencyChange::class)->change($record, (int) $data['currency_id'], (string) ($data['reason'] ?? ''));

                            Notification::make()
                                ->success()
                                ->title(__('accounts::filament/contributions/company.base-currency.changed'))
                                ->send();
                        }),
                ]),
            ])
            ->collapsible()
            ->collapsed();
    }
}

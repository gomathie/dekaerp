<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource\Schemas\PlanForm;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Models\Plan;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Services\PlanCatalog;
use Webkul\PlatformBilling\Support\Money;
use Webkul\Security\Models\User;

class ViewPlan extends ViewRecord
{
    protected static string $resource = PlanResource::class;

    protected static string $lang = 'platform-billing::filament/billing.plans';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addPrice')
                ->label(__(static::$lang.'.actions.add-price'))
                ->visible(fn (Plan $record): bool => (bool) $record->is_active)
                ->schema([
                    TextInput::make('amount')
                        ->label(__(static::$lang.'.form.amount'))
                        ->required(),
                    Select::make('interval_months')
                        ->label(__(static::$lang.'.form.interval'))
                        ->options(PlanForm::intervals())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data, Plan $record): void {
                    $this->run(function (User $actor) use ($data, $record): void {
                        app(PlanCatalog::class)->addPrice($actor, $record, $data['amount'] ?? null, $data['interval_months'] ?? null);
                    });
                }),
            Action::make('deactivatePrice')
                ->label(__(static::$lang.'.actions.deactivate-price'))
                ->requiresConfirmation()
                ->schema([
                    Select::make('price_id')
                        ->label(__(static::$lang.'.form.amount'))
                        ->options(fn (Plan $record): array => $record->prices()
                            ->where('is_active', true)
                            ->orderBy('interval_months')
                            ->get()
                            ->mapWithKeys(function (Price $price): array {
                                $amount = Money::of((int) $price->amount_minor, (string) $price->currency)->toDecimal();
                                $interval = PlanForm::intervals()[(int) $price->interval_months] ?? (string) $price->interval_months;

                                return [$price->getKey() => $interval.' — '.$amount.' '.$price->currency];
                            })
                            ->all())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data, Plan $record): void {
                    $price = $record->prices()->whereKey($data['price_id'] ?? 0)->first();

                    if (! $price instanceof Price) {
                        return;
                    }

                    $this->run(function (User $actor) use ($price): void {
                        app(PlanCatalog::class)->deactivatePrice($actor, $price);
                    });
                }),
            Action::make('deactivatePlan')
                ->label(__(static::$lang.'.actions.deactivate-plan'))
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (Plan $record): bool => (bool) $record->is_active)
                ->action(function (Plan $record): void {
                    $this->run(function (User $actor) use ($record): void {
                        app(PlanCatalog::class)->deactivatePlan($actor, $record);
                    });
                }),
        ];
    }

    protected function run(callable $callback): void
    {
        $actor = auth()->user();

        if (! $actor instanceof User) {
            abort(403);
        }

        try {
            $callback($actor);
            Notification::make()->success()->title(__(static::$lang.'.actions.saved'))->send();
        } catch (Throwable $exception) {
            ReportsServiceFailure::notify($exception);
        }
    }
}

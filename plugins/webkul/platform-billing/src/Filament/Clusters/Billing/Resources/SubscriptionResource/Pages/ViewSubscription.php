<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Pages;

use DateTimeImmutable;
use DateTimeZone;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Schemas\SubscriptionForm;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Models\Subscription;
use Webkul\PlatformBilling\Services\SubscriptionLifecycle;
use Webkul\Security\Models\User;

class ViewSubscription extends ViewRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected static string $lang = 'platform-billing::filament/billing.subscriptions';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('activate')
                ->label(__(static::$lang.'.actions.activate'))
                ->requiresConfirmation()
                ->visible(fn (Subscription $record): bool => $record->status === 'draft')
                ->action(function (Subscription $record): void {
                    $this->run(fn (User $actor) => app(SubscriptionLifecycle::class)->activate(
                        $record,
                        $actor,
                        new DateTimeImmutable('now', new DateTimeZone('UTC')),
                    ));
                }),
            Action::make('schedulePrice')
                ->label(__(static::$lang.'.actions.schedule'))
                ->requiresConfirmation()
                ->visible(fn (Subscription $record): bool => $record->status === 'active')
                ->schema([
                    Select::make('price_id')
                        ->label(__(static::$lang.'.form.price'))
                        ->options(fn (): array => SubscriptionForm::priceOptions())
                        ->required()
                        ->native(false),
                ])
                ->action(function (array $data, Subscription $record): void {
                    $price = Price::query()->find($data['price_id'] ?? 0);

                    if (! $price instanceof Price) {
                        return;
                    }

                    $this->run(fn (User $actor) => app(SubscriptionLifecycle::class)->schedulePrice($record, $price, $actor));
                }),
            Action::make('cancelAtPeriodEnd')
                ->label(__(static::$lang.'.actions.cancel'))
                ->color('danger')
                ->requiresConfirmation()
                ->action(function (Subscription $record): void {
                    $this->run(fn (User $actor) => app(SubscriptionLifecycle::class)->cancelAtPeriodEnd($record, $actor));
                }),
            Action::make('reactivate')
                ->label(__(static::$lang.'.actions.reactivate'))
                ->requiresConfirmation()
                ->visible(fn (Subscription $record): bool => ($record->status === 'active' && $record->cancel_at_period_end) || $record->status === 'expired')
                ->action(function (Subscription $record): void {
                    $this->run(fn (User $actor) => app(SubscriptionLifecycle::class)->reactivate(
                        $record,
                        $actor,
                        new DateTimeImmutable('now', new DateTimeZone('UTC')),
                    ));
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

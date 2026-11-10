<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource\Pages;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Models\Price;
use Webkul\PlatformBilling\Services\SubscriptionDrafts;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $actor = Auth::user();

        if (! $actor instanceof User) {
            abort(403);
        }

        try {
            $company = Company::query()->findOrFail($data['company_id'] ?? 0);
            $price = Price::query()->findOrFail($data['price_id'] ?? 0);

            return app(SubscriptionDrafts::class)->create($actor, $company, $price, $this->anchor($data['anchor_at'] ?? null));
        } catch (Throwable $exception) {
            ReportsServiceFailure::haltOnForm($this, $exception);
        }
    }

    protected function anchor(mixed $value): DateTimeImmutable
    {
        if ($value instanceof DateTimeInterface) {
            return DateTimeImmutable::createFromInterface($value);
        }

        if (is_string($value) && $value !== '') {
            return new DateTimeImmutable($value);
        }

        return new DateTimeImmutable('now', new DateTimeZone('UTC'));
    }
}

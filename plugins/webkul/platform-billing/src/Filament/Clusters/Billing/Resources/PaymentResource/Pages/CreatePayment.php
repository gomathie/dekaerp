<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Pages;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Services\PlatformPayments;
use Webkul\PlatformBilling\Support\Money;
use Webkul\Support\Models\Company;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $company = Company::query()->findOrFail($data['company_id'] ?? 0);

            return app(PlatformPayments::class)->post(
                ReportsServiceFailure::actor(),
                $company,
                Money::fromDecimal((string) ($data['amount'] ?? ''), 'USD')->minor,
                (string) ($data['reference'] ?? ''),
                $this->receivedAt($data['received_at'] ?? null),
            );
        } catch (Throwable $exception) {
            ReportsServiceFailure::haltOnForm($this, $exception);
        }
    }

    protected function receivedAt(mixed $value): DateTimeImmutable
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

<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\DiscountCodeResource;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Services\DiscountCodeService;
use Webkul\PlatformBilling\Support\Money;

class CreateDiscountCode extends CreateRecord
{
    protected static string $resource = DiscountCodeResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $type = (string) ($data['discount_type'] ?? '');
            $duration = (string) ($data['duration_type'] ?? '');
            $months = ReportsServiceFailure::wholeNumber($data['duration_months'] ?? null);
            $limit = ReportsServiceFailure::wholeNumber($data['max_redemptions'] ?? null);

            return app(DiscountCodeService::class)->create(ReportsServiceFailure::actor(), [
                'code'             => (string) ($data['code'] ?? ''),
                'discount_type'    => $type,
                'discount_value'   => $this->value($type, $data['discount_value'] ?? null),
                'duration_type'    => $duration,
                'duration_months'  => $duration === 'months' ? $months : null,
                'eligibility_type' => (string) ($data['eligibility_type'] ?? ''),
                'company_ids'      => array_map('intval', (array) ($data['company_ids'] ?? [])),
                'max_redemptions'  => $limit === null || $limit === '' ? null : $limit,
            ]);
        } catch (Throwable $exception) {
            ReportsServiceFailure::haltOnForm($this, $exception);
        }
    }

    /**
     * The service stores an integer: basis points, cents, or a month count.
     * The field is the decimal a person types. A float would drift.
     */
    protected function value(string $type, mixed $raw): int
    {
        if ($type === 'free_months') {
            $whole = ReportsServiceFailure::wholeNumber($raw);

            return is_int($whole) ? $whole : 0;
        }

        return Money::fromDecimal((string) $raw, 'USD')->minor;
    }
}

<?php

namespace Webkul\Logistics\Support;

use Webkul\Support\Enums\SequenceResetFrequency;
use Webkul\Support\Services\SequenceService;

/**
 * Reference numbers for logistics records, always per company.
 *
 * SequenceService::next() falls back to - and creates - a counter shared by all
 * companies when a company has none, so the company's own counter is ensured
 * first.
 */
class LogisticsSequences
{
    public const SHIPMENT = 'logistics.shipment';

    public const TRIP = 'logistics.trip';

    public const WAYBILL = 'logistics.waybill';

    public const CODES = [self::SHIPMENT, self::TRIP, self::WAYBILL];

    public static function defaults(string $code): array
    {
        return match ($code) {
            self::SHIPMENT => ['name' => 'Logistics Shipment', 'prefix' => 'SHP/%(year)/', 'padding' => 5, 'reset_frequency' => SequenceResetFrequency::YEARLY],
            self::TRIP     => ['name' => 'Logistics Trip', 'prefix' => 'TRP/%(year)/', 'padding' => 5, 'reset_frequency' => SequenceResetFrequency::YEARLY],
            self::WAYBILL  => ['name' => 'Logistics Waybill', 'prefix' => 'WB/%(year)/', 'padding' => 5, 'reset_frequency' => SequenceResetFrequency::YEARLY],
        };
    }

    public static function ensure(string $code, int $companyId): void
    {
        SequenceService::ensure($code, $companyId, static::defaults($code));
    }

    public static function next(string $code, int $companyId): string
    {
        static::ensure($code, $companyId);

        return SequenceService::next($code, $companyId, static::defaults($code));
    }
}

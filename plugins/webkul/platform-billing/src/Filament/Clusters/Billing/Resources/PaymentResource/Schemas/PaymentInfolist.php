<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Webkul\PlatformBilling\Models\Payment;
use Webkul\PlatformBilling\Services\PlatformPayments;
use Webkul\PlatformBilling\Support\Money;

class PaymentInfolist
{
    protected static string $lang = 'platform-billing::filament/billing.payments';

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('company.name')->label(__(static::$lang.'.columns.company')),
            TextEntry::make('idempotency_key')->label(__(static::$lang.'.columns.reference')),
            TextEntry::make('status')->label(__(static::$lang.'.columns.status')),
            TextEntry::make('amount_minor')
                ->label(__(static::$lang.'.columns.amount'))
                ->state(fn (Payment $record): string => Money::of((int) $record->amount_minor, (string) $record->currency)->toDecimal().' '.$record->currency),
            TextEntry::make('unapplied')
                ->label(__(static::$lang.'.infolist.unapplied'))
                ->state(function (Payment $record): string {
                    $left = app(PlatformPayments::class)->unappliedMinor($record);

                    return Money::of($left, (string) $record->currency)->toDecimal().' '.$record->currency;
                }),
            TextEntry::make('reconciliation')
                ->label(__(static::$lang.'.infolist.reconciliation'))
                ->state(function (Payment $record): string {
                    $row = app(PlatformPayments::class)->reconcile((int) $record->company_id);
                    $currency = (string) $record->currency;

                    return __(static::$lang.'.infolist.reconciliation-values', [
                        'credit'   => Money::of($row['unapplied_minor'], $currency)->toDecimal(),
                        'open'     => Money::of($row['open_minor'], $currency)->toDecimal(),
                        'drift'    => Money::of($row['drift_minor'], $currency)->toDecimal(),
                        'currency' => $currency,
                    ]);
                }),
            TextEntry::make('received_at')->label(__(static::$lang.'.columns.received'))->dateTime(),
        ]);
    }
}

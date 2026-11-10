<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource\Pages;

use DateTimeImmutable;
use DateTimeZone;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PaymentResource;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Models\Payment;
use Webkul\PlatformBilling\Services\PlatformPayments;
use Webkul\PlatformBilling\Support\Money;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected static string $lang = 'platform-billing::filament/billing.payments.actions';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('allocate')
                ->label(__(static::$lang.'.allocate'))
                ->requiresConfirmation()
                ->schema([
                    Select::make('invoice_id')
                        ->label(__(static::$lang.'.invoice'))
                        ->options(fn (Payment $record): array => Invoice::query()
                            ->where('company_id', $record->company_id)
                            ->orderBy('number')
                            ->pluck('number', 'id')
                            ->all())
                        ->required()
                        ->native(false),
                    TextInput::make('amount')
                        ->label(__(static::$lang.'.amount'))
                        ->required(),
                ])
                ->action(function (Payment $record, array $data, Action $action): void {
                    try {
                        $invoice = Invoice::query()->findOrFail($data['invoice_id'] ?? 0);
                        app(PlatformPayments::class)->allocate(
                            ReportsServiceFailure::actor(),
                            $record,
                            [[
                                'invoice_id'   => (int) $invoice->getKey(),
                                'amount_minor' => Money::fromDecimal((string) ($data['amount'] ?? ''), 'USD')->minor,
                            ]],
                            new DateTimeImmutable('now', new DateTimeZone('UTC')),
                        );
                    } catch (Throwable $exception) {
                        ReportsServiceFailure::onAction($action, $exception);
                    }
                }),
            Action::make('reversePayment')
                ->label(__(static::$lang.'.reverse'))
                ->color('danger')
                ->requiresConfirmation()
                ->schema([
                    TextInput::make('reference')
                        ->label(__(static::$lang.'.reference'))
                        ->required(),
                    Textarea::make('reason')
                        ->label(__(static::$lang.'.reason'))
                        ->required(),
                ])
                ->action(function (Payment $record, array $data, Action $action): void {
                    try {
                        app(PlatformPayments::class)->reversePayment(
                            ReportsServiceFailure::actor(),
                            $record,
                            (string) ($data['reference'] ?? ''),
                            (string) ($data['reason'] ?? ''),
                            new DateTimeImmutable('now', new DateTimeZone('UTC')),
                        );
                    } catch (Throwable $exception) {
                        ReportsServiceFailure::onAction($action, $exception);
                    }
                }),
            Action::make('refundCredit')
                ->label(__(static::$lang.'.refund'))
                ->color('danger')
                ->requiresConfirmation()
                ->schema([
                    TextInput::make('amount')
                        ->label(__(static::$lang.'.amount'))
                        ->required(),
                    TextInput::make('reference')
                        ->label(__(static::$lang.'.reference'))
                        ->required(),
                    Textarea::make('reason')
                        ->label(__(static::$lang.'.reason'))
                        ->required(),
                ])
                ->action(function (Payment $record, array $data, Action $action): void {
                    try {
                        app(PlatformPayments::class)->refundCredit(
                            ReportsServiceFailure::actor(),
                            $record,
                            Money::fromDecimal((string) ($data['amount'] ?? ''), 'USD')->minor,
                            (string) ($data['reference'] ?? ''),
                            (string) ($data['reason'] ?? ''),
                            new DateTimeImmutable('now', new DateTimeZone('UTC')),
                        );
                    } catch (Throwable $exception) {
                        ReportsServiceFailure::onAction($action, $exception);
                    }
                }),
        ];
    }
}

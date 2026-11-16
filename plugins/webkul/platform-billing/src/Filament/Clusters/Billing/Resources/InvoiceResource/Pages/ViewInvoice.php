<?php

namespace Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource\Pages;

use DateTimeImmutable;
use DateTimeZone;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Throwable;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\InvoiceResource;
use Webkul\PlatformBilling\Filament\Support\ReportsServiceFailure;
use Webkul\PlatformBilling\Models\Invoice;
use Webkul\PlatformBilling\Services\OverdueExtensions;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected static string $lang = 'platform-billing::filament/billing.invoices.actions';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('extendAsSuperAdmin')
                ->label(__(static::$lang.'.extend'))
                ->requiresConfirmation()
                ->schema([
                    TextInput::make('length')
                        ->label(__(static::$lang.'.length'))
                        ->integer()
                        ->minValue(1)
                        ->required(),
                    Select::make('unit')
                        ->label(__(static::$lang.'.unit'))
                        ->options([
                            'day'   => __(static::$lang.'.units.day'),
                            'week'  => __(static::$lang.'.units.week'),
                            'month' => __(static::$lang.'.units.month'),
                        ])
                        ->required()
                        ->native(false),
                    Textarea::make('reason')
                        // Not required() here. Laravel treats a blank reason as
                        // empty before the action runs, so the service's own
                        // "needs a reason" message would never be shown.
                        ->label(__(static::$lang.'.reason')),
                ])
                ->action(function (Invoice $record, array $data, Action $action): void {
                    try {
                        app(OverdueExtensions::class)->extendAsSuperAdmin(
                            ReportsServiceFailure::actor(),
                            $record,
                            (int) ReportsServiceFailure::wholeNumber($data['length'] ?? 0),
                            (string) ($data['unit'] ?? ''),
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

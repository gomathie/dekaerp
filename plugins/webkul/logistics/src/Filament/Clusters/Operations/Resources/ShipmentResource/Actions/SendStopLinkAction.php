<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Enums\StopState;
use Webkul\Logistics\Enums\StopType;
use Webkul\Logistics\Models\CompanySetting;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\Stop;
use Webkul\Logistics\Services\StopLinkService;

/**
 * Issues a one-time POD capture link for a shipment's open delivery stop (D15).
 *
 * No SMS or WhatsApp provider is built in, by decision: the link is shown for
 * the dispatcher to copy and send however they already talk to their drivers.
 * That also keeps the credential out of any third-party message log this
 * application would be responsible for.
 */
class SendStopLinkAction extends Action
{
    protected string $lang = 'logistics::stop-link.actions.send-pod-link';

    public static function getDefaultName(): ?string
    {
        return 'sendStopLink';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-link')
            ->modalHeading(__($this->lang.'.heading'))
            ->modalDescription(fn (Shipment $record): string => __($this->lang.'.description', [
                'hours' => CompanySetting::forCompany((int) $record->company_id)->stop_link_ttl_hours,
            ]))
            // Both halves matter: the permission, and a stop that is still open.
            // The service checks the permission again and re-reads the stop under
            // the company scope, because an action carries no policy check.
            ->visible(fn (Shipment $record): bool => (Auth::user()?->can('sendPodLink', $record) ?? false)
                && static::openDeliveryStop($record) !== null)
            ->action(function (Shipment $record): void {
                $stop = static::openDeliveryStop($record);

                if (! $stop) {
                    Notification::make()
                        ->warning()
                        ->title(__($this->lang.'.no-stop'))
                        ->send();

                    return;
                }

                $url = app(StopLinkService::class)->issue($stop);

                // Shown once, in a modal, and never stored: only the hash is
                // kept, so there is nowhere to look this up again afterwards.
                Notification::make()
                    ->success()
                    ->title(__($this->lang.'.notification'))
                    ->body($url)
                    ->persistent()
                    ->send();
            });
    }

    protected static function openDeliveryStop(Shipment $shipment): ?Stop
    {
        return $shipment->stops()
            ->where('type', StopType::DELIVERY)
            ->whereNotIn('state', [StopState::DEPARTED->value, StopState::SKIPPED->value])
            ->orderBy('sequence')
            ->first();
    }
}

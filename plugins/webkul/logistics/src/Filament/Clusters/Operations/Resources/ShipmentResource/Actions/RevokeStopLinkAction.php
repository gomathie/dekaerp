<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Services\StopLinkService;

/**
 * Kills a POD link that is still live (D15).
 *
 * Issuing a replacement already revokes the previous link, but that is not the
 * case this is for: a dispatcher who sent the URL to the wrong number wants it
 * dead, not replaced. Without this the link is revocable only in principle.
 *
 * Shown only while there is something to revoke, so it does not sit there
 * offering to do nothing.
 */
class RevokeStopLinkAction extends Action
{
    protected string $lang = 'logistics::stop-link.actions.revoke-pod-link';

    public static function getDefaultName(): ?string
    {
        return 'revokeStopLink';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__($this->lang.'.label'))
            ->icon('heroicon-o-link-slash')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading(__($this->lang.'.heading'))
            ->modalDescription(__($this->lang.'.description'))
            ->visible(fn (Shipment $record): bool => (Auth::user()?->can('sendPodLink', $record) ?? false)
                && app(StopLinkService::class)->hasLiveLink($record))
            ->action(function (Shipment $record): void {
                $revoked = app(StopLinkService::class)->revokeForShipment($record);

                Notification::make()
                    ->success()
                    ->title(trans_choice($this->lang.'.notification', $revoked, ['count' => $revoked]))
                    ->send();
            });
    }
}

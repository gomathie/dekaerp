<?php

namespace Webkul\Logistics\Policies;

use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Support\LogisticsAccess;
use Webkul\Security\Models\User;

class ShipmentPolicy extends LogisticsPolicy
{
    protected function subject(): string
    {
        return 'shipment';
    }

    public function confirm(User $user, Shipment $shipment): bool
    {
        return $this->recordAbility($user, 'confirm', $shipment);
    }

    public function assign(User $user, Shipment $shipment): bool
    {
        return $this->recordAbility($user, 'assign', $shipment);
    }

    public function markPickedUp(User $user, Shipment $shipment): bool
    {
        return $this->recordAbility($user, 'mark_picked_up', $shipment);
    }

    public function markDelivered(User $user, Shipment $shipment): bool
    {
        return $this->recordAbility($user, 'mark_delivered', $shipment);
    }

    public function capturePod(User $user, Shipment $shipment): bool
    {
        return $this->recordAbility($user, 'capture_pod', $shipment);
    }

    public function sendPodLink(User $user, Shipment $shipment): bool
    {
        return $this->recordAbility($user, 'send_pod_link', $shipment);
    }

    public function cancel(User $user, Shipment $shipment): bool
    {
        return $this->recordAbility($user, 'cancel', $shipment);
    }

    public function createOpening(User $user): bool
    {
        return $this->allows($user, 'create_opening') && LogisticsAccess::enabledForCurrent();
    }

    public function createInvoice(User $user, Shipment $shipment): bool
    {
        return $this->recordAbility($user, 'create_invoice', $shipment);
    }

    public function viewFinancials(User $user, ?Shipment $shipment = null): bool
    {
        return $this->allows($user, 'view_financials');
    }
}

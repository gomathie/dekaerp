<?php

namespace Webkul\Logistics\Filament;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\HtmlString;
use Webkul\Logistics\Models\Shipment;
use Webkul\Logistics\Models\ShipmentCharge;
use Webkul\Logistics\Services\CustomerPage;
use Webkul\Partner\Filament\Resources\PartnerResource\Support\PartnerSchemaRegistry;
use Webkul\Partner\Models\Partner;

/**
 * Shipments on the customer page (D11).
 *
 * PartnerSchemaRegistry has form, infolist, table and action slots, and no
 * relation-manager slot. A tab on the existing infolist is the extension
 * point. Partners is not edited, so uninstalling Logistics removes the tab
 * with this class.
 */
class PartnerContributions
{
    /**
     * packageBooted() runs again from the installer and the test bootstrapper.
     * The registry is static and does not reset, so a second call would draw
     * the tab twice.
     */
    protected static bool $registered = false;

    public static function register(): void
    {
        if (static::$registered) {
            return;
        }

        static::$registered = true;

        PartnerSchemaRegistry::infolist('tabs.append', fn (): array => [static::tab()]);
    }

    protected static function tab(): Tab
    {
        $lang = 'logistics::filament/partner';

        return Tab::make(__($lang.'.tab'))
            ->icon('heroicon-o-truck')
            // Hiding the tab is not the check. The entries below call the
            // same gate, so a rendered state cannot skip it.
            ->visible(fn (?Partner $record): bool => $record instanceof Partner && CustomerPage::visible($record))
            ->schema([
                TextEntry::make('logistics_recent_shipments')
                    ->label(__($lang.'.shipments'))
                    ->html()
                    ->state(fn (?Partner $record): HtmlString => static::shipments($record)),
                TextEntry::make('logistics_open_charges')
                    ->label(__($lang.'.charges'))
                    ->html()
                    ->visible(fn (): bool => CustomerPage::showsCharges())
                    ->state(fn (?Partner $record): HtmlString => static::charges($record)),
            ]);
    }

    protected static function shipments(?Partner $record): HtmlString
    {
        if (! $record instanceof Partner) {
            return new HtmlString('');
        }

        $shipments = CustomerPage::recentShipments($record);

        if ($shipments->isEmpty()) {
            return new HtmlString(e(__('logistics::filament/partner.empty-shipments')));
        }

        $lines = $shipments->map(function (Shipment $shipment): string {
            $url = e(CustomerPage::shipmentUrl($shipment));
            $name = e((string) $shipment->name);
            $state = e((string) ($shipment->state?->getLabel() ?? ''));

            return '<a href="'.$url.'">'.$name.'</a> — '.$state;
        });

        return new HtmlString($lines->implode('<br>'));
    }

    protected static function charges(?Partner $record): HtmlString
    {
        if (! $record instanceof Partner || ! CustomerPage::showsCharges()) {
            return new HtmlString('');
        }

        $charges = CustomerPage::openCharges($record);

        if ($charges->isEmpty()) {
            return new HtmlString(e(__('logistics::filament/partner.empty-charges')));
        }

        $lines = $charges->map(function (ShipmentCharge $charge): string {
            $shipment = e((string) $charge->shipment?->name);
            $description = e((string) $charge->description);
            $amount = e(number_format((float) $charge->total, 2).' '.($charge->currency?->symbol ?? ''));

            return trim($shipment.' — '.$description.' — '.$amount);
        });

        return new HtmlString($lines->implode('<br>'));
    }
}

<?php

namespace Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\CancelAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\ConfirmAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\CreateInvoiceAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\DeliverAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\FailDeliveryAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\HoldAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\PickupAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\PrintWaybillAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\ReleaseAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\RevokeStopLinkAction;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource\Actions\SendStopLinkAction;
use Webkul\Support\Traits\HasRecordNavigationTabs;

class ViewShipment extends ViewRecord
{
    use HasRecordNavigationTabs;

    protected static string $resource = ShipmentResource::class;

    protected static string $lang = 'logistics::filament/clusters/operations/resources/shipment';

    protected function getHeaderActions(): array
    {
        return [
            ConfirmAction::make(),
            ReleaseAction::make(),
            HoldAction::make(),
            CancelAction::make(),
            // WP-5's delivery actions, in the order a shipment moves through
            // them. Each hides itself on state and policy, so only the step
            // that is actually available shows.
            PickupAction::make(),
            DeliverAction::make(),
            FailDeliveryAction::make(),
            SendStopLinkAction::make(),
            RevokeStopLinkAction::make(),
            PrintWaybillAction::make(),
            CreateInvoiceAction::make(),
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            EditAction::make(),
            DeleteAction::make(),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__(static::$lang.'.infolist.summary'))
                ->columns(3)
                ->schema([
                    TextEntry::make('name')->label(__(static::$lang.'.form.fields.number')),
                    TextEntry::make('state')->label(__(static::$lang.'.table.columns.state'))->badge(),
                    TextEntry::make('priority')->label(__(static::$lang.'.form.fields.priority'))->badge(),
                    TextEntry::make('customer.name')->label(__(static::$lang.'.form.fields.customer')),
                    TextEntry::make('customer_reference')->label(__(static::$lang.'.form.fields.customer-reference'))->placeholder('—'),
                    TextEntry::make('serviceType.name')->label(__(static::$lang.'.form.fields.service-type'))->placeholder('—'),
                ]),

            Section::make(__(static::$lang.'.infolist.route'))
                ->columns(3)
                ->schema([
                    TextEntry::make('origin_label')->label(__(static::$lang.'.form.fields.origin'))->placeholder('—'),
                    TextEntry::make('destination_label')->label(__(static::$lang.'.form.fields.destination'))->placeholder('—'),
                    TextEntry::make('transport_mode')->label(__(static::$lang.'.form.fields.transport-mode'))->badge(),
                    TextEntry::make('planned_pickup_at')->label(__(static::$lang.'.form.fields.planned-pickup-at'))->dateTime()->placeholder('—'),
                    TextEntry::make('actual_pickup_at')->label(__(static::$lang.'.infolist.actual-pickup-at'))->dateTime()->placeholder('—'),
                    TextEntry::make('expected_delivery_at')->label(__(static::$lang.'.form.fields.expected-delivery-at'))->dateTime()->placeholder('—'),
                    TextEntry::make('actual_delivery_at')->label(__(static::$lang.'.infolist.actual-delivery-at'))->dateTime()->placeholder('—'),
                ]),

            Section::make(__(static::$lang.'.infolist.cargo'))
                ->columns(3)
                ->schema([
                    TextEntry::make('total_packages')->label(__(static::$lang.'.table.columns.packages'))->numeric(),
                    TextEntry::make('total_weight_kg')->label(__(static::$lang.'.infolist.total-weight'))->numeric()->suffix(' kg'),
                    TextEntry::make('total_volume_m3')->label(__(static::$lang.'.infolist.total-volume'))->numeric()->suffix(' m³'),
                    IconEntry::make('is_fragile')->label(__(static::$lang.'.form.fields.is-fragile'))->boolean(),
                    IconEntry::make('is_hazardous')->label(__(static::$lang.'.form.fields.is-hazardous'))->boolean(),
                    TextEntry::make('instructions')->label(__(static::$lang.'.form.fields.instructions'))->placeholder('—')->columnSpanFull(),
                ]),

            Section::make(__(static::$lang.'.infolist.assignment'))
                ->columns(3)
                ->schema([
                    TextEntry::make('dispatcher.name')->label(__(static::$lang.'.form.fields.dispatcher'))->placeholder('—'),
                    TextEntry::make('carrier.name')->label(__(static::$lang.'.form.fields.carrier'))->placeholder('—'),
                    TextEntry::make('carrier_reference')->label(__(static::$lang.'.form.fields.carrier-reference'))->placeholder('—'),
                    TextEntry::make('waybill_no')->label(__(static::$lang.'.form.fields.waybill-no'))->placeholder('—'),
                    TextEntry::make('company.name')->label(__(static::$lang.'.form.fields.company')),
                ]),
        ]);
    }
}

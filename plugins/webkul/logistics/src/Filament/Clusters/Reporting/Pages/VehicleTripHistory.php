<?php

namespace Webkul\Logistics\Filament\Clusters\Reporting\Pages;

use BackedEnum;

/**
 * Trip history from the fleet's side (WP-11).
 */
class VehicleTripHistory extends TripHistory
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-truck';

    protected static ?int $navigationSort = 4;

    protected static string $lang = 'logistics::reports.vehicle-trips';

    protected static function pagePermission(): string
    {
        return 'page_logistics_vehicle_trip_history';
    }

    protected static function subject(): string
    {
        return 'vehicle';
    }
}

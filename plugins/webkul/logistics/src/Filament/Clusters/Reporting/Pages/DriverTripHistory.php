<?php

namespace Webkul\Logistics\Filament\Clusters\Reporting\Pages;

use BackedEnum;

/**
 * The same trips, from the driver's side (WP-11).
 */
class DriverTripHistory extends TripHistory
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';

    protected static ?int $navigationSort = 5;

    protected static string $lang = 'logistics::reports.driver-trips';

    protected static function pagePermission(): string
    {
        return 'page_logistics_driver_trip_history';
    }

    protected static function subject(): string
    {
        return 'driver';
    }
}

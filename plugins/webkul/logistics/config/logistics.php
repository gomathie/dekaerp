<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Uninstall with data
    |--------------------------------------------------------------------------
    |
    | Uninstalling the plugin drops every logistics table. While any shipment
    | exists, uninstall is refused (from the Plugins page and the console) unless
    | this is explicitly switched on. Back up the database before enabling it.
    |
    */

    'allow_uninstall_with_data' => (bool) env('LOGISTICS_ALLOW_UNINSTALL_WITH_DATA', false),
];

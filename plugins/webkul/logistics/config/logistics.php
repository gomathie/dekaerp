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

    /*
    |--------------------------------------------------------------------------
    | Stop link (POD capture)
    |--------------------------------------------------------------------------
    |
    | The public POD capture route is throttled on two keys at once. The token
    | is the primary one: this is a multi-tenant application and drivers share
    | mobile carrier NAT addresses, so keying only on the IP would let one
    | company's drivers spend another's budget. A company can raise or lower its
    | own limit (logistics_company_settings.stop_link_rate_limit).
    |
    | The per-IP limit stays as a second layer, set much higher: it is there to
    | blunt someone walking the token space from one address, not to police a
    | driver filling in one form.
    |
    */

    'stop_link' => [
        'per_token_per_minute' => (int) env('LOGISTICS_STOP_LINK_TOKEN_RATE', 12),
        'per_ip_per_minute'    => (int) env('LOGISTICS_STOP_LINK_IP_RATE', 120),
    ],
];

<?php

/*
 * Daily exchange rates (user, 2026-11-22: "we can get a free popular API").
 *
 * Named by provider, not by vendor, so another source can be added without a
 * new setting. "open_er_api" is ExchangeRate-API's open endpoint: free, no
 * key, daily, and it carries GHS, ZMW and CDF, which the ECB-based free
 * sources do not. Its terms ask for attribution, given on the Currencies page.
 */
return [
    'enabled' => (bool) env('EXCHANGE_RATES_SYNC', true),

    'provider' => env('EXCHANGE_RATES_PROVIDER', 'open_er_api'),

    'providers' => [
        'open_er_api' => [
            'url'     => env('EXCHANGE_RATES_URL', 'https://open.er-api.com/v6/latest/USD'),
            'base'    => 'USD',
            'timeout' => 15,
        ],
    ],
];

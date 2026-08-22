<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Without this file Laravel falls back to its defaults, which allow every
    | origin ('*') over 'api/*'. Two things are wrong with that here: the
    | wildcard, and the path - this application serves its API from
    | 'admin/api/v1/*', which 'api/*' does not match, so the defaults were
    | simultaneously too permissive and aimed at the wrong routes.
    |
    | Origins are read from CORS_ALLOWED_ORIGINS (comma separated) and fall
    | back to APP_URL, so a deployment that sets neither permits only itself.
    |
    */

    'paths' => [
        'admin/api/*',
        'api/*',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('CORS_ALLOWED_ORIGINS', (string) env('APP_URL', '')))
    ))),

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Accept',
        'Authorization',
        'Content-Type',
        'Origin',
        'X-Requested-With',
        'X-XSRF-TOKEN',
    ],

    'exposed_headers' => [],

    'max_age' => 3600,

    /*
     | Only needed for a cookie-authenticated SPA on another origin. Token
     | authentication does not use it, and enabling it forbids a wildcard
     | origin, so it stays off until something actually requires it.
     */
    'supports_credentials' => (bool) env('CORS_SUPPORTS_CREDENTIALS', false),

];

<?php

return [

    /*
    |--------------------------------------------------------------------------
    | API Rate Limit
    |--------------------------------------------------------------------------
    |
    | Requests per minute allowed against the plugin API routes under
    | admin/api/*. The limit is applied per Sanctum token, so raising it
    | raises the ceiling for each client integration independently rather
    | than for all of them combined. See the "api" limiter in
    | App\Providers\AppServiceProvider.
    |
    */

    'rate_limit' => (int) env('API_RATE_LIMIT', 120),

];

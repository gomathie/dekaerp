<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Help Page Links
    |--------------------------------------------------------------------------
    |
    | Destinations for the cards on the in-app Help page. Upstream hardcodes
    | these to aureuserp.com and store.webkul.com, which points customers of
    | this product at a different vendor's sales pages.
    |
    | A card with no URL is not rendered. That is deliberate: the marketing
    | site is being built out page by page, and a card linking to a page that
    | does not exist yet is worse than no card at all. Fill each value in as
    | the corresponding page ships - these are env-driven, so it takes a
    | variable change rather than a deploy.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Plugin Manager
    |--------------------------------------------------------------------------
    |
    | permission_timeout caps the shield:generate run that follows a plugin
    | install. It writes a permission row per resource action across every
    | installed plugin, so the wall time scales with plugin count and with
    | database latency - a managed database in another region is far slower
    | than a local one. Too low and the install reports success while the
    | admin role never receives the new permissions, leaving the plugin's
    | resources invisible in the panel.
    |
    */

    'plugins' => [
        'permission_timeout' => (int) env('DEKA_PLUGIN_PERMISSION_TIMEOUT', 900),
    ],

    'help' => [

        /*
        | Accepts a mailto: address as readily as a URL, so support can be a
        | working contact from day one without a contact page existing.
        */
        'contact_url' => env('DEKA_HELP_CONTACT_URL'),

        'services' => [
            'cloud'   => env('DEKA_HELP_CLOUD_URL'),
            'support' => env('DEKA_HELP_SUPPORT_URL'),
            'paid'    => env('DEKA_HELP_PAID_URL'),
        ],

        'resources' => [
            'extensions' => env('DEKA_HELP_EXTENSIONS_URL'),
            'docs'       => env('DEKA_HELP_DOCS_URL'),

            /*
            | Deliberately unset. dekaerp.com currently serves the same
            | single-page app for every path - /useguide returns byte-for-byte
            | what / returns - so pointing the User Guide card there would send
            | people to the marketing homepage while looking like it worked.
            | Set this once a real guide page exists.
            */
            'guide' => env('DEKA_HELP_GUIDE_URL'),

            /*
            | The product site does exist, so this card is the one thing the
            | page can always show. Overridable, but it should rarely need to be.
            */
            'website' => env('DEKA_HELP_WEBSITE_URL', 'https://dekaerp.com'),
        ],

    ],

];

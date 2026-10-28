<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Plugins a new company receives
    |--------------------------------------------------------------------------
    |
    | Entitlement is an installation-level decision: a company created from now
    | on should start with the modules it is meant to have, not with whatever
    | happens to be installed. List plugin names (the `plugins.name` column,
    | e.g. "products", "accounts") to define that set.
    |
    | `null` means "every installed plugin", which is the behaviour that existed
    | before this setting and is kept as the default so installing a plugin does
    | not silently stop reaching new companies. Setting an explicit list never
    | revokes anything from a company that already has it - see
    | PluginEntitlementService::grantDefaults().
    |
    */

    'default_company_plugins' => null,

    /*
    |--------------------------------------------------------------------------
    | Plugins that are never an entitlement
    |--------------------------------------------------------------------------
    |
    | Core plugins (`->isCore()` in their service provider) are part of the
    | installation, not a module a company buys: the base UI, the permission
    | layer, the plugin manager itself, contacts, table views, custom fields,
    | analytics, calendars and chatter. They are listed explicitly rather than
    | derived at runtime so the boundary is readable and testable - a new core
    | plugin must be added here deliberately, and forgetting it fails closed
    | (its routes get guarded) rather than open.
    |
    */

    'entitlements' => [
        'exempt' => [
            'analytics',
            'chatter',
            'fields',
            'full-calendar',
            'partners',
            'plugin-manager',
            'security',
            'support',
            'table-views',
        ],

        /*
         * A super admin administers the installation, not a tenant, and may
         * have no company membership at all - the recovery path. Entitlement is
         * still enforced for everyone else.
         */
        'super_admin_bypass' => true,
    ],
];

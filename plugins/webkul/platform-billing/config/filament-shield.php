<?php

use Webkul\PlatformBilling\Filament\Clusters\Billing;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\PlanResource;
use Webkul\PlatformBilling\Filament\Clusters\Billing\Resources\SubscriptionResource;

/*
 * PackageServiceProvider merges resources.manage and the exclude lists only.
 * pages.manage in this file would be ignored. The cluster is excluded because
 * Filament clusters extend Page and Shield rejects a page that is some page's
 * cluster. Resource permissions are generated as <affix>_platform_billing_<model>.
 */

$basic = ['view_any', 'view', 'create', 'update'];

return [
    'resources' => [
        'manage' => [
            PlanResource::class         => $basic,
            SubscriptionResource::class => $basic,
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Billing::class,
        ],
    ],
];

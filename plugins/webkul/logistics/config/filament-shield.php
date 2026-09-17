<?php

use Webkul\Logistics\Filament\Clusters\Configurations;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ExpenseCategoryResource;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\PackageTypeResource;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\ServiceTypeResource;
use Webkul\Logistics\Filament\Clusters\Configurations\Resources\VehicleTypeResource;
use Webkul\Logistics\Filament\Clusters\Finance;
use Webkul\Logistics\Filament\Clusters\Finance\Resources\ExpenseResource;
use Webkul\Logistics\Filament\Clusters\Fleet;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\DriverResource;
use Webkul\Logistics\Filament\Clusters\Fleet\Resources\VehicleResource;
use Webkul\Logistics\Filament\Clusters\Operations;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\ShipmentResource;
use Webkul\Logistics\Filament\Clusters\Operations\Resources\TripResource;
use Webkul\Logistics\Filament\Clusters\Reporting;

/*
 * Every Logistics permission, including those of resources later packages add
 * (WP-2 ... WP-8a), so no package has to edit this file.
 *
 * Custom abilities live in resources.manage: PackageServiceProvider only merges
 * resources.manage and the exclude lists from a plugin config, so pages.manage
 * and custom_permissions would be ignored. Page permissions are generated
 * automatically as page_logistics_<page>.
 */

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$forceDelete = ['force_delete', 'force_delete_any'];
$restore = ['restore', 'restore_any'];
$reorder = ['reorder'];

return [
    'resources' => [
        'manage' => [
            ShipmentResource::class => [
                ...$basic, ...$delete, ...$restore, ...$forceDelete,
                'confirm', 'assign', 'mark_picked_up', 'mark_delivered', 'capture_pod',
                'send_pod_link', 'cancel', 'create_opening', 'create_invoice', 'view_financials',
            ],
            TripResource::class              => [...$basic, ...$delete, ...$restore, ...$forceDelete, 'dispatch', 'complete'],
            VehicleResource::class           => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            DriverResource::class            => [...$basic, ...$delete, ...$restore, ...$forceDelete],
            ExpenseResource::class           => [...$basic, ...$delete, ...$restore, ...$forceDelete, 'approve', 'post_bill'],
            ServiceTypeResource::class       => [...$basic, ...$delete, ...$restore, ...$forceDelete, ...$reorder],
            VehicleTypeResource::class       => [...$basic, ...$delete, ...$reorder],
            PackageTypeResource::class       => [...$basic, ...$delete, ...$reorder],
            ExpenseCategoryResource::class   => [...$basic, ...$delete, ...$reorder],
        ],
        'exclude' => [],
    ],

    'pages' => [
        'exclude' => [
            Operations::class,
            Fleet::class,
            Finance::class,
            Reporting::class,
            Configurations::class,
        ],
    ],
];

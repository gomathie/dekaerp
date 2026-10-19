<?php

$dateFilters = [
    'from'    => 'From',
    'until'   => 'Until',
    'between' => 'From :from to :until',
    'since'   => 'From :from',
    'up-to'   => 'Up to :until',
];

/*
 * Vehicle and driver trip history are the same table with a different filter in
 * front, so they share their column and filter labels rather than keeping two
 * copies to drift apart. TripHistoryExporter labels its file from the
 * vehicle-trips keys whichever page it was started from, which is only correct
 * while the two sets are identical - keep them so.
 */
$tripColumns = [
    'reference' => 'Trip',
    'vehicle'   => 'Vehicle',
    'driver'    => 'Driver',
    'state'     => 'Status',
    'started'   => 'Started',
    'ended'     => 'Ended',
    'shipments' => 'Shipments',
    'distance'  => 'Distance',
];

$tripFilters = $dateFilters + [
    'vehicle' => 'Vehicle',
    'driver'  => 'Driver',
    'state'   => 'Status',
];

return [
    'export'          => 'Export',
    'export-complete' => 'Exported :count rows.',

    'shipment-register' => [
        'title'  => 'Shipment register',
        'export' => 'Export',

        'columns' => [
            'reference'   => 'Shipment',
            'customer'    => 'Customer',
            'state'       => 'Status',
            'origin'      => 'From',
            'destination' => 'To',
            'expected'    => 'Expected',
            'delivered'   => 'Delivered',
            'company'     => 'Company',
        ],

        'filters' => $dateFilters + [
            'state'    => 'Status',
            'customer' => 'Customer',
        ],
    ],

    'delivery-performance' => [
        'title'      => 'Delivery performance',
        'subheading' => 'Measured against the expected delivery date. Shipments with no expected date are not scored.',
        'export'     => 'Export',

        'columns' => [
            'reference' => 'Shipment',
            'customer'  => 'Customer',
            'expected'  => 'Expected',
            'delivered' => 'Delivered',
            'outcome'   => 'Outcome',
            'delay'     => 'Late by',
        ],

        'outcomes' => [
            'on-time'      => 'On time',
            'late'         => 'Late',
            'failed'       => 'Failed',
            'overdue'      => 'Overdue',
            'pending'      => 'In progress',
            'not-measured' => 'No date promised',
        ],

        'filters' => $dateFilters + [
            'customer'    => 'Customer',
            'late-only'   => 'Late only',
            'failed-only' => 'Failed only',
        ],
    ],

    'profitability' => [
        'title'      => 'Shipment profitability',
        'subheading' => 'Billable charges against approved costs, each in the shipment’s own currency. Draft costs are not counted.',
        'export'     => 'Export',

        'columns' => [
            'reference' => 'Shipment',
            'customer'  => 'Customer',
            'revenue'   => 'Revenue',
            'costs'     => 'Costs',
            'margin'    => 'Margin',
            'currency'  => 'Currency',
        ],

        'filters' => $dateFilters + [
            'customer'     => 'Customer',
            'loss-making'  => 'Loss-making only',
        ],
    ],

    'vehicle-trips' => [
        'title'   => 'Vehicle trip history',
        'export'  => 'Export',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],

    'driver-trips' => [
        'title'   => 'Driver trip history',
        'export'  => 'Export',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],
];

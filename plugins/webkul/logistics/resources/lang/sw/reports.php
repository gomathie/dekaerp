<?php

$dateFilters = [
    'from'    => 'Kutoka',
    'until'   => 'Mpaka',
    'between' => 'Kutoka :from hadi :until',
    'since'   => 'Kutoka :from',
    'up-to'   => 'Juu hadi :until',
];

/*
 * Vehicle and driver trip history are the same table with a different filter in
 * front, so they share their column and filter labels rather than keeping two
 * copies to drift apart. TripHistoryExporter labels its file from the
 * vehicle-trips keys whichever page it was started from, which is only correct
 * while the two sets are identical - keep them so.
 */
$tripColumns = [
    'reference' => 'Safari',
    'vehicle'   => 'Gari',
    'driver'    => 'Dereva',
    'state'     => 'Hali',
    'started'   => 'Started',
    'ended'     => 'Ended',
    'shipments' => 'Shehena',
    'distance'  => 'Distance',
];

$tripFilters = $dateFilters + [
    'vehicle' => 'Gari',
    'driver'  => 'Dereva',
    'state'   => 'Hali',
];

return [
    'export'          => 'Hamisha',
    'export-complete' => 'Exported :count mistari.',

    'shipment-register' => [
        'title'  => 'Shehena jisajili',
        'export' => 'Hamisha',

        'columns' => [
            'reference'   => 'Shehena',
            'customer'    => 'Mteja',
            'state'       => 'Hali',
            'origin'      => 'Kutoka',
            'destination' => 'Hadi',
            'expected'    => 'Inatarajiwa',
            'delivered'   => 'Delivered',
            'company'     => 'Kampuni',
        ],

        'filters' => $dateFilters + [
            'state'    => 'Hali',
            'customer' => 'Mteja',
        ],
    ],

    'delivery-performance' => [
        'title'      => 'Usafirishaji performance',
        'subheading' => 'Measured dhidi ya inatarajiwa usafirishaji tarehe. Shehena na hakuna inatarajiwa tarehe ni si scored.',
        'export'     => 'Hamisha',

        'columns' => [
            'reference' => 'Shehena',
            'customer'  => 'Mteja',
            'expected'  => 'Inatarajiwa',
            'delivered' => 'Delivered',
            'outcome'   => 'Outcome',
            'delay'     => 'Late na',
        ],

        'outcomes' => [
            'on-time'      => 'Washa time',
            'late'         => 'Late',
            'failed'       => 'Imeshindwa',
            'overdue'      => 'Imechelewa',
            'pending'      => 'Katika maendeleo',
            'not-measured' => 'Hakuna tarehe promised',
        ],

        'filters' => $dateFilters + [
            'customer'    => 'Mteja',
            'late-only'   => 'Late tu',
            'failed-only' => 'Imeshindwa tu',
        ],
    ],

    'profitability' => [
        'title'      => 'Shehena profitability',
        'subheading' => 'Billable charges dhidi ya imeidhinishwa gharama, kila katika shehena’s yake sarafu. Rasimu gharama ni si counted.',
        'export'     => 'Hamisha',

        'columns' => [
            'reference' => 'Shehena',
            'customer'  => 'Mteja',
            'revenue'   => 'Mapato',
            'costs'     => 'Gharama',
            'margin'    => 'Pembe',
            'currency'  => 'Sarafu',
        ],

        'filters' => $dateFilters + [
            'customer'     => 'Mteja',
            'loss-making'  => 'Hasara-making tu',
        ],
    ],

    'vehicle-trips' => [
        'title'   => 'Gari safari historia',
        'export'  => 'Hamisha',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],

    'driver-trips' => [
        'title'   => 'Dereva safari historia',
        'export'  => 'Hamisha',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],
];

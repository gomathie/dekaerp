<?php

$dateFilters = [
    'from'    => 'Kuanzia',
    'until'   => 'Hadi',
    'between' => 'Kuanzia :from hadi :until',
    'since'   => 'Kuanzia :from',
    'up-to'   => 'Hadi :until',
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
    'started'   => 'Ilianza',
    'ended'     => 'Iliisha',
    'shipments' => 'Usafirishaji',
    'distance'  => 'Umbali',
];

$tripFilters = $dateFilters + [
    'vehicle' => 'Gari',
    'driver'  => 'Dereva',
    'state'   => 'Hali',
];

return [
    'export'          => 'Hamisha',
    'export-complete' => 'Safu :count zimehamishwa.',

    'shipment-register' => [
        'title'  => 'Rejista ya usafirishaji',
        'export' => 'Hamisha kwenda Excel',

        'columns' => [
            'reference'   => 'Usafirishaji',
            'customer'    => 'Mteja',
            'state'       => 'Hali',
            'origin'      => 'Kutoka',
            'destination' => 'Kwenda',
            'expected'    => 'Inatarajiwa',
            'delivered'   => 'Imewasilishwa',
            'company'     => 'Kampuni',
        ],

        'filters' => $dateFilters + [
            'state'    => 'Hali',
            'customer' => 'Mteja',
        ],
    ],

    'delivery-performance' => [
        'title'      => 'Utendaji wa uwasilishaji',
        'subheading' => 'Hupimwa dhidi ya tarehe ya uwasilishaji inayotarajiwa. Usafirishaji usio na tarehe inayotarajiwa haupimwi.',
        'export'     => 'Hamisha kwenda Excel',

        'columns' => [
            'reference' => 'Usafirishaji',
            'customer'  => 'Mteja',
            'expected'  => 'Inatarajiwa',
            'delivered' => 'Imewasilishwa',
            'outcome'   => 'Matokeo',
            'delay'     => 'Imechelewa kwa',
        ],

        'outcomes' => [
            'on-time'      => 'Kwa wakati',
            'late'         => 'Imechelewa',
            'failed'       => 'Imeshindwa',
            'overdue'      => 'Imepita muda',
            'pending'      => 'Inaendelea',
            'not-measured' => 'Hakuna tarehe iliyoahidiwa',
        ],

        'filters' => $dateFilters + [
            'customer'    => 'Mteja',
            'late-only'   => 'Zilizochelewa pekee',
            'failed-only' => 'Zilizoshindwa pekee',
        ],
    ],

    'profitability' => [
        'title'      => 'Faida ya usafirishaji',
        'subheading' => 'Tozo za mteja dhidi ya gharama zilizoidhinishwa, kila moja katika sarafu ya usafirishaji husika. Gharama za rasimu hazihesabiwi.',
        'export'     => 'Hamisha kwenda Excel',

        'columns' => [
            'reference' => 'Usafirishaji',
            'customer'  => 'Mteja',
            'revenue'   => 'Mapato',
            'costs'     => 'Gharama',
            'margin'    => 'Faida',
            'currency'  => 'Sarafu',
        ],

        'filters' => $dateFilters + [
            'customer'     => 'Mteja',
            'loss-making'  => 'Zenye hasara pekee',
        ],
    ],

    'vehicle-trips' => [
        'title'   => 'Historia ya safari za magari',
        'export'  => 'Hamisha kwenda Excel',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],

    'driver-trips' => [
        'title'   => 'Historia ya safari za madereva',
        'export'  => 'Hamisha kwenda Excel',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],
];

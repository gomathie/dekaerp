<?php

return [
    'title' => 'Dispatch board',

    'tabs' => [
        'unassigned'      => 'Unassigned',
        'awaiting-pickup' => 'Awaiting pickup',
        'active'          => 'Active trips',
        'due-today'       => 'Due today',
        'overdue'         => 'Overdue',
        'failed'          => 'Failed',
    ],

    'columns' => [
        'number'               => 'Shipment',
        'state'                => 'Status',
        'customer'             => 'Customer',
        'destination'          => 'Destination',
        'expected-delivery-at' => 'Expected delivery',
    ],
];

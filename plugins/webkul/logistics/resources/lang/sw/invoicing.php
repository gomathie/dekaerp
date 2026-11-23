<?php

return [
    'waiting-time' => [
        // A suggestion only: D14 bills detention when a person confirms it,
        // never automatically.
        'description' => 'Waiting time saa :stop (:minutes min zaidi ya free allowance)',
    ],

    'charges' => [
        'title'  => 'Charges',
        'fields' => [
            'product'            => 'Service',
            'description'        => 'Maelezo',
            'quantity'           => 'Kiasi',
            'price-unit'         => 'Bei ya uniti',
            'discount'           => 'Punguzo (%)',
            'taxes'              => 'Kodi',
            'is-billable'        => 'Billable',
            'is-billable-helper' => 'Tu billable charges ni picked juu wakati ankara ni raised.',
            'invoiced'           => 'Invoiced',
        ],
    ],

    'invoices' => [
        'title'   => 'Ankara',
        'columns' => [
            'number'        => 'Ankara',
            'state'         => 'Hali',
            'payment-state' => 'Malipo',
            'date'          => 'Tarehe',
            'total'         => 'Jumla',
        ],
        'actions' => [
            'open' => 'Wazi katika Uhasibu',
        ],
    ],

    'unbilled' => [
        'title'   => 'Unbilled charges',
        'columns' => [
            'shipment'    => 'Shehena',
            'customer'    => 'Mteja',
            'description' => 'Charge',
            'quantity'    => 'Qty',
            'subtotal'    => 'Kiasi',
            'total'       => 'Jumla',
        ],
        'filters' => [
            'currency' => 'Sarafu',
            'shipment' => 'Shehena',
        ],
    ],

    'actions' => [
        'create-invoice' => [
            'label'              => 'Unda ankara',
            'heading'            => 'Ankara hii shehena?',
            'description'        => 'Kila billable charge hiyo ina si invoiced bado becomes mstari washa mpya Ankara ya mteja. Charges tayari invoiced ni iliyobaki alone.',
            'notification'       => 'Ankara imeundwa.',
            'notification-body'  => 'Ankara :invoice ni katika Uhasibu, kama rasimu wewe unaweza review kabla ya posting.',
            'nothing-to-invoice' => 'Nothing hadi ankara',
        ],
    ],
];

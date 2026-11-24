<?php

return [
    'waiting-time' => [
        // A suggestion only: D14 bills detention when a person confirms it,
        // never automatically.
        'description' => 'Muda wa kusubiri katika :stop (dakika :minutes zaidi ya muda wa bure)',
    ],

    'charges' => [
        'title'  => 'Tozo',
        'fields' => [
            'product'            => 'Huduma',
            'description'        => 'Maelezo',
            'quantity'           => 'Idadi',
            'price-unit'         => 'Bei ya kipimo',
            'discount'           => 'Punguzo (%)',
            'taxes'              => 'Kodi',
            'is-billable'        => 'Inatozwa',
            'is-billable-helper' => 'Tozo zinazotozwa pekee ndizo huchukuliwa ankara inapotolewa.',
            'invoiced'           => 'Imetolewa ankara',
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
            'open' => 'Fungua katika Uhasibu',
        ],
    ],

    'unbilled' => [
        'title'   => 'Tozo zisizotolewa ankara',
        'columns' => [
            'shipment'    => 'Usafirishaji',
            'customer'    => 'Mteja',
            'description' => 'Tozo',
            'quantity'    => 'Idadi',
            'subtotal'    => 'Kiasi',
            'total'       => 'Jumla',
        ],
        'filters' => [
            'currency' => 'Sarafu',
            'shipment' => 'Usafirishaji',
        ],
    ],

    'actions' => [
        'create-invoice' => [
            'label'              => 'Unda ankara',
            'heading'            => 'Toa ankara kwa usafirishaji huu?',
            'description'        => 'Kila tozo inayotozwa ambayo bado haijatolewa ankara inakuwa mstari kwenye ankara mpya ya mteja. Tozo zilizokwisha tolewa ankara haziguswi.',
            'notification'       => 'Ankara imeundwa.',
            'notification-body'  => 'Ankara :invoice iko katika Uhasibu, kama rasimu unayoweza kukagua kabla ya kurekodi.',
            'nothing-to-invoice' => 'Hakuna cha kutolea ankara',
        ],
    ],
];

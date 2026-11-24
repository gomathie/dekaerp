<?php

return [
    'unknown'       => 'Haijulikani',
    'other'         => 'Nyingine',
    'share'         => 'Sehemu',
    'status'        => 'Hali',
    'receipt-status'=> 'Hali ya upokeaji',
    'stats'         => [
        'heading'              => 'Vipimo muhimu',
        'draft-rfqs'           => 'Rasimu za maombi ya nukuu',
        'sent-rfqs'            => 'Maombi ya nukuu yaliyotumwa',
        'confirmed-orders'     => 'Oda zilizothibitishwa',
        'total-purchase-value' => 'Jumla ya thamani ya ununuzi',
        'increase'             => 'ongezeko la :percent%',
        'decrease'             => 'upungufu wa :percent%',
        'no-change'            => 'Hakuna mabadiliko',
    ],

    'trend'        => [
        'heading' => 'Mwenendo wa oda za ununuzi kwa hali',
        'states'  => [
            'draft'    => 'Rasimu',
            'sent'     => 'Imetumwa',
            'purchase' => 'Ununuzi',
            'done'     => 'Imekamilika',
            'canceled' => 'Imeghairiwa',
        ],
    ],

    'vendor-spend' => [
        'heading'         => 'Mgawanyo wa matumizi kwa wasambazaji',
        'total-purchased' => 'Jumla iliyonunuliwa',
    ],

    'top-orders'   => [
        'heading' => 'Oda kubwa zaidi',
        'columns' => [
            'reference'      => 'Nambari ya oda',
            'vendor'         => 'Msambazaji',
            'billing-status' => 'Hali ya bili',
            'amount'         => 'Jumla ya kiasi',
        ],
    ],

    'top-products' => [
        'heading' => 'Bidhaa zinazonunuliwa zaidi',
        'columns' => [
            'name'     => 'Bidhaa',
            'quantity' => 'Jumla ya idadi',
            'value'    => 'Jumla ya thamani',
        ],
    ],
    'top-rfqs'     => [
        'heading' => 'Maombi makubwa ya nukuu',
        'columns' => [
            'reference' => 'Nambari ya ombi la nukuu',
            'status'    => 'Hali',
            'amount'    => 'Jumla ya kiasi',
        ],
    ],

    'top-vendors'  => [
        'heading' => 'Wasambazaji wakuu',
        'columns' => [
            'vendor' => 'Msambazaji',
            'orders' => 'Oda',
            'amount' => 'Jumla ya matumizi',
        ],
    ],
];

<?php

return [
    'navigation' => [
        'title' => 'Benki Upatanisho',
    ],

    'import' => [
        'label'       => 'Ingiza taarifa',
        'description' => 'Pakia CSV faili kutoka yako benki. Give nambari ya kila safu, counting kutoka 1 washa iliyobaki.',
        'journal'     => 'Akaunti ya benki',
        'file'        => 'Taarifa faili (CSV)',
        'name'        => 'Taarifa jina',
        'date-format' => 'Tarehe umbizo',
        'has-header'  => 'Kwanza mstari ni header',
        'amount-help' => 'Au likizo tupu na give Pesa zinazoingia na Pesa zinazotoka safu.',
        'done'        => ':count mistari imported.',

        'columns' => [
            'date'      => 'Tarehe safu',
            'label'     => 'Maelezo safu',
            'amount'    => 'Kiasi safu',
            'money_in'  => 'Pesa zinazoingia safu',
            'money_out' => 'Pesa zinazotoka safu',
            'partner'   => 'Mteja au msambazaji safu',
            'reference' => 'Kumbukumbu safu',
        ],
    ],

    'columns' => [
        'date'    => 'Tarehe',
        'label'   => 'Maelezo',
        'partner' => 'Mteja au msambazaji',
        'journal' => 'Akaunti ya benki',
        'amount'  => 'Kiasi',
        'status'  => 'Hali',
        'payment' => 'Malipo',
    ],

    'status' => [
        'reconciled'   => 'Reconciled',
        'to-reconcile' => 'Hadi patanisha',
    ],

    'match' => [
        'label'       => 'Kilinganacho malipo',
        'description' => 'Malipo katika hii akaunti ya benki kwa ile ile kiasi hiyo ni bado waiting kwa benki.',
        'payment'     => 'Malipo',
        'none'        => 'Hakuna malipo vinavyolingana. Jisajili malipo washa ankara kwanza, au book mstari hadi akaunti.',
        'done'        => 'Mstari matched. malipo ni sasa imelipwa.',
    ],

    'book' => [
        'label'       => 'Book hadi akaunti',
        'description' => 'Kwa benki charges, interest au uhamisho: posts mstari dhidi ya akaunti wewe chagua.',
        'account'     => 'Akaunti',
        'entry-label' => 'Lebo',
        'done'        => 'Mstari booked.',
    ],

    'empty' => [
        'heading'     => 'Nothing hadi patanisha',
        'description' => 'Ingiza taarifa kutoka yako benki hadi anza.',
    ],
];

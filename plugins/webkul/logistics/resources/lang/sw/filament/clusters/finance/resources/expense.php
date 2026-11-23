<?php

return [
    'navigation' => [
        'title' => 'Gharama',
    ],

    'form' => [
        'sections' => [
            'details'   => 'Gharama maelezo',
            'reference' => 'Kumbukumbu na stakabadhi',
        ],
        'fields' => [
            'company'          => 'Kampuni',
            'date'             => 'Tarehe',
            'amount'           => 'Kiasi',
            'currency'         => 'Sarafu',
            'category'         => 'Kategoria',
            'paid-by'          => 'Imelipwa na',
            'employee'         => 'Mfanyakazi',
            'payee'            => 'Msambazaji au carrier',
            'shipment'         => 'Shehena',
            'trip'             => 'Safari',
            'vehicle'          => 'Gari',
            'vendor-reference' => 'Msambazaji kumbukumbu',
            'description'      => 'Maelezo',
            'receipt'          => 'Stakabadhi',
        ],
    ],

    'table' => [
        'columns' => [
            'date'        => 'Tarehe',
            'category'    => 'Kategoria',
            'description' => 'Maelezo',
            'amount'      => 'Kiasi',
            'state'       => 'Hali',
            'shipment'    => 'Shehena',
            'company'     => 'Kampuni',
        ],
        'filters' => [
            'state'    => 'Hali',
            'category' => 'Kategoria',
        ],
    ],

    'infolist' => [
        'sections' => [
            'summary' => 'Gharama',
        ],
        'fields' => [
            'state'       => 'Hali',
            'date'        => 'Tarehe',
            'amount'      => 'Kiasi',
            'category'    => 'Kategoria',
            'shipment'    => 'Shehena',
            'trip'        => 'Safari',
            'vehicle'     => 'Gari',
            'receipt'     => 'Stakabadhi',
            'description' => 'Maelezo',
        ],
    ],

    'actions' => [
        'submitExpense' => [
            'label'        => 'Wasilisha',
            'heading'      => 'Wasilisha hii gharama?',
            'notification' => 'Gharama submitted.',
        ],
        'approveExpense' => [
            'label'        => 'Idhinisha',
            'heading'      => 'Idhinisha hii gharama?',
            'notification' => 'Gharama imeidhinishwa.',
        ],
        'rejectExpense' => [
            'label'        => 'Kataa',
            'heading'      => 'Kataa hii gharama?',
            'notification' => 'Gharama imekataliwa.',
        ],
        'cannot-proceed' => 'Hii gharama ni si ready bado',
    ],
];

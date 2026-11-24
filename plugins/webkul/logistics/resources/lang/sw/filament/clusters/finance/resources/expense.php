<?php

return [
    'navigation' => [
        'title' => 'Gharama',
    ],

    'form' => [
        'sections' => [
            'details'   => 'Maelezo ya gharama',
            'reference' => 'Marejeleo na risiti',
        ],
        'fields' => [
            'company'          => 'Kampuni',
            'date'             => 'Tarehe',
            'amount'           => 'Kiasi',
            'currency'         => 'Sarafu',
            'category'         => 'Kategoria',
            'paid-by'          => 'Imelipwa na',
            'employee'         => 'Mfanyakazi',
            'payee'            => 'Msambazaji au msafirishaji',
            'shipment'         => 'Usafirishaji',
            'trip'             => 'Safari',
            'vehicle'          => 'Gari',
            'vendor-reference' => 'Marejeleo ya msambazaji',
            'description'      => 'Maelezo',
            'receipt'          => 'Risiti',
        ],
    ],

    'table' => [
        'columns' => [
            'date'        => 'Tarehe',
            'category'    => 'Kategoria',
            'description' => 'Maelezo',
            'amount'      => 'Kiasi',
            'state'       => 'Hali',
            'shipment'    => 'Usafirishaji',
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
            'shipment'    => 'Usafirishaji',
            'trip'        => 'Safari',
            'vehicle'     => 'Gari',
            'receipt'     => 'Risiti',
            'description' => 'Maelezo',
        ],
    ],

    'actions' => [
        'submitExpense' => [
            'label'        => 'Wasilisha',
            'heading'      => 'Wasilisha gharama hii?',
            'notification' => 'Gharama imewasilishwa.',
        ],
        'approveExpense' => [
            'label'        => 'Idhinisha',
            'heading'      => 'Idhinisha gharama hii?',
            'notification' => 'Gharama imeidhinishwa.',
        ],
        'rejectExpense' => [
            'label'        => 'Kataa',
            'heading'      => 'Kataa gharama hii?',
            'notification' => 'Gharama imekataliwa.',
        ],
        'cannot-proceed' => 'Gharama hii bado haiko tayari',
    ],
];

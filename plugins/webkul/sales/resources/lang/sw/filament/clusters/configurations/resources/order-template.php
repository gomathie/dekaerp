<?php

return [
    'title' => 'Bidhaa za kiolezo cha oda',

    'navigation' => [
        'title' => 'Bidhaa za kiolezo cha oda',
        'group' => 'Oda za mauzo',
    ],

    'global-search' => [
        'name'    => 'Jina',
    ],

    'form' => [
        'fields' => [
            'sort'           => 'Mpangilio',
            'order-template' => 'Kiolezo cha oda',
            'company'        => 'Kampuni',
            'product'        => 'Bidhaa',
            'product-uom'    => 'Kipimo cha bidhaa',
            'creator'        => 'Muundaji',
            'display-type'   => 'Aina ya onyesho',
            'name'           => 'Jina',
            'quantity'       => 'Idadi',
        ],
    ],

    'table' => [
        'columns' => [
            'sort'           => 'Mpangilio',
            'order-template' => 'Kiolezo cha oda',
            'company'        => 'Kampuni',
            'product'        => 'Bidhaa',
            'product-uom'    => 'Kipimo cha bidhaa',
            'created-by'     => 'Iliundwa na',
            'display-type'   => 'Aina ya onyesho',
            'name'           => 'Jina',
            'quantity'       => 'Idadi',
            'created-at'     => 'Iliundwa',
            'updated-at'     => 'Ilisasishwa',

        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Bidhaa za kiolezo cha oda zimesasishwa',
                    'body'  => 'Bidhaa za kiolezo cha oda zimesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa za kiolezo cha oda zimefutwa',
                    'body'  => 'Bidhaa za kiolezo cha oda zimefutwa kwa mafanikio.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Bidhaa za kiolezo cha oda zimefutwa',
                    'body'  => 'Bidhaa za kiolezo cha oda zimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort'           => 'Mpangilio wa kupanga',
            'order-template' => 'Kiolezo cha oda',
            'company'        => 'Kampuni',
            'product'        => 'Bidhaa',
            'product-uom'    => 'Kipimo cha bidhaa',
            'display-type'   => 'Aina ya onyesho',
            'name'           => 'Jina',
            'quantity'       => 'Idadi',
        ],
    ],
];

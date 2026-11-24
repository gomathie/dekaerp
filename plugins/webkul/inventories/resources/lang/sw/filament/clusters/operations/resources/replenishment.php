<?php

return [
    'navigation' => [
        'title' => 'Ujazaji upya',
        'group' => 'Ununuzi',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Bidhaa',
            'location'          => 'Eneo',
            'route'             => 'Njia',
            'vendor'            => 'Msambazaji',
            'trigger'           => 'Kichochezi',
            'on-hand'           => 'Zilizopo',
            'min'               => 'Chini',
            'max'               => 'Juu',
            'multiple-quantity' => 'Idadi ya kizidisho',
            'to-order'          => 'Za kuagiza',
            'uom'               => 'Kipimo',
            'company'           => 'Kampuni',
        ],

        'groups' => [
            'location' => 'Eneo',
            'product'  => 'Bidhaa',
            'category' => 'Kategoria',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Ongeza ujazaji upya',

                'notification' => [
                    'title' => 'Ujazaji upya umeongezwa',
                    'body'  => 'Ujazaji upya umeongezwa kwa mafanikio.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Ujazaji upya tayari upo',
                        'body'  => 'Ujazaji upya tayari upo kwa mpangilio huu. Tafadhali sasisha uliopo badala yake.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];

<?php

return [
    'navigation' => [
        'title' => 'Replenishment',
        'group' => 'Procurement',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Bidhaa',
            'location'          => 'Mahali',
            'route'             => 'Njia',
            'vendor'            => 'Msambazaji',
            'trigger'           => 'Trigger',
            'on-hand'           => 'Washa Mkono',
            'min'               => 'Min',
            'max'               => 'Max',
            'multiple-quantity' => 'Multiple Kiasi',
            'to-order'          => 'Hadi Agizo',
            'uom'               => 'UOM',
            'company'           => 'Kampuni',
        ],

        'groups' => [
            'location' => 'Mahali',
            'product'  => 'Bidhaa',
            'category' => 'Kategoria',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Ongeza Replenishment',

                'notification' => [
                    'title' => 'Replenishment added',
                    'body'  => 'replenishment ina added successfully.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Replenishment tayari ipo',
                        'body'  => 'replenishment tayari ipo kwa hii usanidi. Tafadhali sasisha existing replenishment instead.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];

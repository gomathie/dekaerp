<?php

return [
    'title' => 'Quantities',

    'tabs' => [
        'internal-locations' => 'Ndani Maeneo',
        'transit-locations'  => 'Transit Maeneo',
        'on-hand'            => 'Washa Mkono',
        'to-count'           => 'Hadi Idadi',
        'to-apply'           => 'Hadi Tumia',
    ],

    'form' => [
        'fields' => [
            'product'          => 'Bidhaa',
            'location'         => 'Mahali',
            'package'          => 'Kifurushi',
            'lot'              => 'Kundi / Nambari ya mfululizo Numbers',
            'on-hand-qty'      => 'Washa Mkono Kiasi',
            'storage-category' => 'Storage Kategoria',
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Bidhaa',
            'location'          => 'Mahali',
            'lot'               => 'Kundi / Nambari ya mfululizo Numbers',
            'storage-category'  => 'Storage Kategoria',
            'quantity'          => 'Kiasi',
            'package'           => 'Kifurushi',
            'on-hand'           => 'Washa Mkono Kiasi',
            'unit'              => 'Uniti',
            'reserved-quantity' => 'Imehifadhiwa Kiasi',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Kiasi imesasishwa',
                    'body'  => 'kiasi ina imesasishwa successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Ongeza Kiasi',

                'notification' => [
                    'title' => 'Kiasi added',
                    'body'  => 'kiasi ina added successfully.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Kiasi tayari ipo',
                        'body'  => 'Tayari ina kiasi kwa ile ile usanidi. Tafadhali sasisha kiasi instead.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kiasi imefutwa',
                    'body'  => 'kiasi ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];

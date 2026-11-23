<?php

return [
    'navigation' => [
        'title' => 'Quantities',
        'group' => 'Adjustments',
    ],

    'form' => [
        'fields' => [
            'location'         => 'Mahali',
            'product'          => 'Bidhaa',
            'package'          => 'Kifurushi',
            'lot'              => 'Kundi / Nambari ya mfululizo Numbers',
            'counted-qty'      => 'Counted Kiasi',
            'scheduled-at'     => 'Scheduled Saa',
            'storage-category' => 'Storage Kategoria',
        ],
    ],

    'table' => [
        'columns' => [
            'location'           => 'Mahali',
            'product'            => 'Bidhaa',
            'product-category'   => 'Bidhaa Kategoria',
            'lot'                => 'Kundi / Nambari ya mfululizo Numbers',
            'storage-category'   => 'Storage Kategoria',
            'available-quantity' => 'Inapatikana Kiasi',
            'quantity'           => 'Kiasi',
            'package'            => 'Kifurushi',
            'last-counted-at'    => 'Mwisho Counted Saa',
            'on-hand'            => 'Washa Mkono Kiasi',
            'uom'                => 'UOM',
            'counted'            => 'Counted Kiasi',
            'difference'         => 'Difference',
            'scheduled-at'       => 'Scheduled Saa',
            'user'               => 'Mtumiaji',
            'company'            => 'Kampuni',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Kiasi imesasishwa',
                    'body'  => 'kiasi ina imesasishwa successfully.',
                ],
            ],
        ],

        'groups' => [
            'product'          => 'Bidhaa',
            'product-category' => 'Bidhaa Kategoria',
            'location'         => 'Mahali',
            'storage-category' => 'Storage Kategoria',
            'lot'              => 'Kundi / Nambari ya mfululizo Numbers',
            'company'          => 'Kampuni',
            'package'          => 'Kifurushi',
        ],

        'filters' => [
            'product'             => 'Bidhaa',
            'uom'                 => 'Kipimo',
            'product-category'    => 'Bidhaa Kategoria',
            'location'            => 'Mahali',
            'storage-category'    => 'Storage Kategoria',
            'lot'                 => 'Kundi / Nambari ya mfululizo Numbers',
            'company'             => 'Kampuni',
            'package'             => 'Kifurushi',
            'on-hand-quantity'    => 'Washa Mkono Kiasi',
            'difference-quantity' => 'Difference Kiasi',
            'incoming-at'         => 'Incoming Saa',
            'scheduled-at'        => 'Scheduled Saa',
            'user'                => 'Mtumiaji',
            'created-at'          => 'Imeundwa',
            'updated-at'          => 'Imesasishwa',
            'company'             => 'Kampuni',
            'creator'             => 'Creator',
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
                        'body'  => 'kiasi tayari ipo kwa hii usanidi. Tafadhali sasisha existing kiasi instead.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'Tumia',

                'notification' => [
                    'title' => 'Kiasi changes imetumika',
                    'body'  => 'kiasi changes ina imetumika successfully.',
                ],
            ],

            'clear' => [
                'label' => 'Futa',

                'notification' => [
                    'title' => 'Kiasi changes cleared',
                    'body'  => 'kiasi changes ina cleared successfully.',
                ],
            ],
        ],
    ],
];

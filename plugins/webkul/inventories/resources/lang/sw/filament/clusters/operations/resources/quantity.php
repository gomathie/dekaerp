<?php

return [
    'navigation' => [
        'title' => 'Idadi',
        'group' => 'Marekebisho',
    ],

    'form' => [
        'fields' => [
            'location'         => 'Eneo',
            'product'          => 'Bidhaa',
            'package'          => 'Kifurushi',
            'lot'              => 'Makundi / Nambari za mfululizo',
            'counted-qty'      => 'Idadi iliyohesabiwa',
            'scheduled-at'     => 'Imepangwa',
            'storage-category' => 'Kategoria ya uhifadhi',
        ],
    ],

    'table' => [
        'columns' => [
            'location'           => 'Eneo',
            'product'            => 'Bidhaa',
            'product-category'   => 'Kategoria ya bidhaa',
            'lot'                => 'Makundi / Nambari za mfululizo',
            'storage-category'   => 'Kategoria ya uhifadhi',
            'available-quantity' => 'Idadi inayopatikana',
            'quantity'           => 'Idadi',
            'package'            => 'Kifurushi',
            'last-counted-at'    => 'Ilihesabiwa mwisho',
            'on-hand'            => 'Idadi iliyopo',
            'uom'                => 'Kipimo',
            'counted'            => 'Idadi iliyohesabiwa',
            'difference'         => 'Tofauti',
            'scheduled-at'       => 'Imepangwa',
            'user'               => 'Mtumiaji',
            'company'            => 'Kampuni',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Idadi imesasishwa',
                    'body'  => 'Idadi imesasishwa kwa mafanikio.',
                ],
            ],
        ],

        'groups' => [
            'product'          => 'Bidhaa',
            'product-category' => 'Kategoria ya bidhaa',
            'location'         => 'Eneo',
            'storage-category' => 'Kategoria ya uhifadhi',
            'lot'              => 'Makundi / Nambari za mfululizo',
            'company'          => 'Kampuni',
            'package'          => 'Kifurushi',
        ],

        'filters' => [
            'product'             => 'Bidhaa',
            'uom'                 => 'Kipimo',
            'product-category'    => 'Kategoria ya bidhaa',
            'location'            => 'Eneo',
            'storage-category'    => 'Kategoria ya uhifadhi',
            'lot'                 => 'Makundi / Nambari za mfululizo',
            'company'             => 'Kampuni',
            'package'             => 'Kifurushi',
            'on-hand-quantity'    => 'Idadi iliyopo',
            'difference-quantity' => 'Tofauti ya idadi',
            'incoming-at'         => 'Inatarajiwa kuingia',
            'scheduled-at'        => 'Imepangwa',
            'user'                => 'Mtumiaji',
            'created-at'          => 'Iliundwa',
            'updated-at'          => 'Ilisasishwa',
            'company'             => 'Kampuni',
            'creator'             => 'Muundaji',
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Ongeza idadi',

                'notification' => [
                    'title' => 'Idadi imeongezwa',
                    'body'  => 'Idadi imeongezwa kwa mafanikio.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Idadi tayari ipo',
                        'body'  => 'Idadi tayari ipo kwa mpangilio huu. Tafadhali sasisha idadi iliyopo badala yake.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'Tumia',

                'notification' => [
                    'title' => 'Mabadiliko ya idadi yametumika',
                    'body'  => 'Mabadiliko ya idadi yametumika kwa mafanikio.',
                ],
            ],

            'clear' => [
                'label' => 'Ondoa',

                'notification' => [
                    'title' => 'Mabadiliko ya idadi yameondolewa',
                    'body'  => 'Mabadiliko ya idadi yameondolewa kwa mafanikio.',
                ],
            ],
        ],
    ],
];

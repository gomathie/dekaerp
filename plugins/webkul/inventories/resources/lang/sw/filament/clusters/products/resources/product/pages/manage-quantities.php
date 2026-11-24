<?php

return [
    'title' => 'Idadi',

    'tabs' => [
        'internal-locations' => 'Maeneo ya ndani',
        'transit-locations'  => 'Maeneo ya mpito',
        'on-hand'            => 'Zilizopo',
        'to-count'           => 'Za kuhesabu',
        'to-apply'           => 'Za kutumia',
    ],

    'form' => [
        'fields' => [
            'product'          => 'Bidhaa',
            'location'         => 'Eneo',
            'package'          => 'Kifurushi',
            'lot'              => 'Makundi / Nambari za mfululizo',
            'on-hand-qty'      => 'Idadi iliyopo',
            'storage-category' => 'Kategoria ya uhifadhi',
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Bidhaa',
            'location'          => 'Eneo',
            'lot'               => 'Makundi / Nambari za mfululizo',
            'storage-category'  => 'Kategoria ya uhifadhi',
            'quantity'          => 'Idadi',
            'package'           => 'Kifurushi',
            'on-hand'           => 'Idadi iliyopo',
            'unit'              => 'Kipimo',
            'reserved-quantity' => 'Idadi iliyotengwa',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Idadi imesasishwa',
                    'body'  => 'Idadi imesasishwa kwa mafanikio.',
                ],
            ],
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
                        'body'  => 'Tayari kuna idadi kwa mpangilio huu. Tafadhali sasisha idadi badala yake.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Idadi imefutwa',
                    'body'  => 'Idadi imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];

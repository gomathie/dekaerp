<?php

return [
    'navigation' => [
        'title' => 'Oda za kazi',
        'group' => 'Operesheni',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'work-order'          => 'Oda ya kazi',
                    'work-center'         => 'Kituo cha kazi',
                    'product'             => 'Bidhaa',
                    'quantity'            => 'Idadi',
                    'manufacturing-order' => 'Oda ya uzalishaji',
                    'lot-serial'          => 'Namba ya kundi/mfululizo',
                    'start-date'          => 'Tarehe ya kuanza',
                    'end-date'            => 'Tarehe ya kumaliza',
                    'date-range-separator'=> 'hadi',
                    'expected-duration'   => 'Muda unaotarajiwa',
                    'duration-suffix'     => 'dakika',
                    'real-duration'       => 'Muda halisi',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'      => 'Ufuatiliaji wa muda',
                'add-action' => 'Ongeza mstari',
                'columns'    => [
                    'user'         => 'Mtumiaji',
                    'duration'     => 'Muda',
                    'start-date'   => 'Tarehe ya kuanza',
                    'end-date'     => 'Tarehe ya kumaliza',
                    'productivity' => 'Tija',
                ],
                'footer' => [
                    'real-duration' => 'Muda halisi',
                ],
            ],
            'components' => [
                'title'      => 'Vipengele',
                'add-action' => 'Ongeza mstari',
                'columns'    => [
                    'product'    => 'Bidhaa',
                    'from'       => 'Kutoka',
                    'to-consume' => 'Ya kutumia',
                    'quantity'   => 'Idadi',
                    'uom'        => 'Kipimo',
                ],
            ],
            'work-instruction' => [
                'title'   => 'Maelekezo ya kazi',
                'entries' => [
                    'operation' => 'Operesheni',
                    'worksheet' => 'Karatasi ya kazi',
                ],
            ],
            'blocked-by' => [
                'title'  => 'Imezuiwa na',
                'fields' => [
                    'work-orders' => 'Oda za kazi',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation'           => 'Operesheni',
            'work-center'         => 'Kituo cha kazi',
            'manufacturing-order' => 'Oda ya uzalishaji',
            'product'             => 'Bidhaa',
            'quantity-remaining'  => 'Idadi iliyobaki',
            'lot-serial'          => 'Kundi/Namba ya mfululizo',
            'start'               => 'Mwanzo',
            'end'                 => 'Mwisho',
            'expected-duration'   => 'Muda unaotarajiwa',
            'real-duration'       => 'Muda halisi',
            'status'              => 'Hali',
        ],
        'groups' => [
            'status'              => 'Hali',
            'work-center'         => 'Kituo cha kazi',
            'manufacturing-order' => 'Oda ya uzalishaji',
            'product'             => 'Bidhaa',
            'start'               => 'Mwanzo',
            'end'                 => 'Mwisho',
        ],
        'filters' => [
            'work-order'          => 'Oda ya kazi',
            'status'              => 'Hali',
            'operation'           => 'Operesheni',
            'work-center'         => 'Kituo cha kazi',
            'manufacturing-order' => 'Oda ya uzalishaji',
            'product'             => 'Bidhaa',
            'start'               => 'Mwanzo',
            'end'                 => 'Mwisho',
            'created-at'          => 'Iliundwa',
            'updated-at'          => 'Ilisasishwa',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla',
                'entries' => [
                    'work-order'          => 'Oda ya kazi',
                    'work-center'         => 'Kituo cha kazi',
                    'product'             => 'Bidhaa',
                    'quantity'            => 'Idadi',
                    'manufacturing-order' => 'Oda ya uzalishaji',
                    'lot-serial'          => 'Namba ya kundi/mfululizo',
                    'start-date'          => 'Tarehe ya kuanza',
                    'end-date'            => 'Tarehe ya kumaliza',
                    'expected-duration'   => 'Muda unaotarajiwa',
                    'real-duration'       => 'Muda halisi',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'  => 'Ufuatiliaji wa muda',
                'footer' => [
                    'real-duration' => 'Muda halisi',
                ],
            ],
            'components' => [
                'title' => 'Vipengele',
            ],
            'work-instruction' => [
                'title'   => 'Maelekezo ya kazi',
                'entries' => [
                    'operation' => 'Operesheni',
                    'worksheet' => 'Karatasi ya kazi',
                ],
            ],
            'blocked-by' => [
                'title'   => 'Imezuiwa na',
                'columns' => [
                    'work-order'  => 'Oda ya kazi',
                    'work-center' => 'Kituo cha kazi',
                    'status'      => 'Hali',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Oda mpya ya kazi',
                ],
            ],
        ],
    ],
];

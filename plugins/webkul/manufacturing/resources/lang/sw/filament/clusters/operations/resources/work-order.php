<?php

return [
    'navigation' => [
        'title' => 'Agizo la kazis',
        'group' => 'Shughuli',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'work-order'          => 'Agizo la kazi',
                    'work-center'         => 'Work Center',
                    'product'             => 'Bidhaa',
                    'quantity'            => 'Kiasi',
                    'manufacturing-order' => 'Uzalishaji Agizo',
                    'lot-serial'          => 'Kundi/Nambari ya mfululizo Nambari',
                    'start-date'          => 'Anza Tarehe',
                    'end-date'            => 'Mwisho Tarehe',
                    'date-range-separator'=> 'hadi',
                    'expected-duration'   => 'Inatarajiwa Duration',
                    'duration-suffix'     => 'dakika',
                    'real-duration'       => 'Real Duration',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'      => 'Time Tracking',
                'add-action' => 'Ongeza mstari',
                'columns'    => [
                    'user'         => 'Mtumiaji',
                    'duration'     => 'Duration',
                    'start-date'   => 'Anza Tarehe',
                    'end-date'     => 'Mwisho Tarehe',
                    'productivity' => 'Productivity',
                ],
                'footer' => [
                    'real-duration' => 'Real Duration',
                ],
            ],
            'components' => [
                'title'      => 'Components',
                'add-action' => 'Ongeza mstari',
                'columns'    => [
                    'product'    => 'Bidhaa',
                    'from'       => 'Kutoka',
                    'to-consume' => 'Hadi Consume',
                    'quantity'   => 'Kiasi',
                    'uom'        => 'UoM',
                ],
            ],
            'work-instruction' => [
                'title'   => 'Work Instruction',
                'entries' => [
                    'operation' => 'Operation',
                    'worksheet' => 'Worksheet',
                ],
            ],
            'blocked-by' => [
                'title'  => 'Blocked Na',
                'fields' => [
                    'work-orders' => 'Agizo la kazis',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation'           => 'Operation',
            'work-center'         => 'Work Center',
            'manufacturing-order' => 'Uzalishaji Agizo',
            'product'             => 'Bidhaa',
            'quantity-remaining'  => 'Kiasi Iliyobaki',
            'lot-serial'          => 'Kundi/Nambari ya mfululizo',
            'start'               => 'Anza',
            'end'                 => 'Mwisho',
            'expected-duration'   => 'Inatarajiwa Duration',
            'real-duration'       => 'Real Duration',
            'status'              => 'Hali',
        ],
        'groups' => [
            'status'              => 'Hali',
            'work-center'         => 'Work Center',
            'manufacturing-order' => 'Uzalishaji Agizo',
            'product'             => 'Bidhaa',
            'start'               => 'Anza',
            'end'                 => 'Mwisho',
        ],
        'filters' => [
            'work-order'          => 'Agizo la kazi',
            'status'              => 'Hali',
            'operation'           => 'Operation',
            'work-center'         => 'Work Center',
            'manufacturing-order' => 'Uzalishaji Agizo',
            'product'             => 'Bidhaa',
            'start'               => 'Anza',
            'end'                 => 'Mwisho',
            'created-at'          => 'Imeundwa',
            'updated-at'          => 'Imesasishwa',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla',
                'entries' => [
                    'work-order'          => 'Agizo la kazi',
                    'work-center'         => 'Work Center',
                    'product'             => 'Bidhaa',
                    'quantity'            => 'Kiasi',
                    'manufacturing-order' => 'Uzalishaji Agizo',
                    'lot-serial'          => 'Kundi/Nambari ya mfululizo Nambari',
                    'start-date'          => 'Anza Tarehe',
                    'end-date'            => 'Mwisho Tarehe',
                    'expected-duration'   => 'Inatarajiwa Duration',
                    'real-duration'       => 'Real Duration',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'  => 'Time Tracking',
                'footer' => [
                    'real-duration' => 'Real Duration',
                ],
            ],
            'components' => [
                'title' => 'Components',
            ],
            'work-instruction' => [
                'title'   => 'Work Instruction',
                'entries' => [
                    'operation' => 'Operation',
                    'worksheet' => 'Worksheet',
                ],
            ],
            'blocked-by' => [
                'title'   => 'Blocked Na',
                'columns' => [
                    'work-order'  => 'Agizo la kazi',
                    'work-center' => 'Work Center',
                    'status'      => 'Hali',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Mpya Agizo la kazi',
                ],
            ],
        ],
    ],
];

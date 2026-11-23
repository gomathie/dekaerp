<?php

return [
    'navigation' => [
        'title' => 'Uzalishaji Maagizo',
        'group' => 'Shughuli',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'product'                => 'Bidhaa',
                    'quantity'               => 'Kiasi',
                    'uom'                    => 'UoM',
                    'bill-of-material'       => 'Bili ya Material',
                    'scheduled-date'         => 'Scheduled Tarehe',
                    'scheduled-end'          => 'Scheduled Mwisho',
                    'responsible'            => 'Responsible',
                    'to-produce'             => 'Hadi Produce',
                    'to-produce-placeholder' => 'Picha preview',
                    'uom-placeholder'        => 'UoM',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Components',
                'add-action'   => 'Ongeza mstari',
                'process-note' => 'Components ita kuwa generated kama uzalishaji process ni built.',
                'columns'      => [
                    'component'          => 'Bidhaa',
                    'from'               => 'Kutoka',
                    'to-consume'         => 'Hadi Consume',
                    'to-consume-tooltip' => 'Insufficient kiasi inapatikana',
                    'quantity'           => 'Kiasi',
                    'uom'                => 'UoM',
                    'forecast'           => 'Utabiri',
                ],
            ],
            'work-orders' => [
                'title'        => 'Agizo la kazis',
                'add-action'   => 'Ongeza mstari',
                'process-note' => 'Agizo la kazis ita kuwa generated baada ya uzalishaji process ni configured.',
                'columns'      => [
                    'operation'          => 'Operation',
                    'work-center'        => 'Work Center',
                    'product'            => 'Bidhaa',
                    'quantity-remaining' => 'Kiasi Iliyobaki',
                    'quantity-produced'  => 'Kiasi Produced',
                    'start'              => 'Anza',
                    'end'                => 'Mwisho',
                    'expected-duration'  => 'Inatarajiwa Duration',
                    'real-duration'      => 'Real Duration',
                    'status'             => 'Hali',
                    'lot-serial'         => 'Kundi/Nambari ya mfululizo',
                ],
                'actions'      => [
                    'open-work-order' => [
                        'tooltip' => 'Wazi agizo la kazi',
                    ],

                    'done' => [
                        'label' => 'Imekamilika',
                    ],
                ],
            ],
            'by-products' => [
                'title'        => 'Na-Bidhaa',
                'process-note' => 'Na-bidhaa ita kuwa generated kama uzalishaji process ni built.',
                'columns'      => [
                    'product'    => 'Bidhaa',
                    'to'         => 'Hadi',
                    'to-produce' => 'Hadi Produce',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Miscellaneous',
                'fields' => [
                    'operation-type'             => 'Operation Aina',
                    'source'                     => 'Chanzo',
                    'finished-products-location' => 'Finished Bidhaa Mahali',
                    'company'                    => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'              => 'Kumbukumbu',
            'start'                  => 'Anza',
            'end'                    => 'Mwisho',
            'deadline'               => 'Deadline',
            'product'                => 'Bidhaa',
            'lot-serial-number'      => 'Kundi/Nambari ya mfululizo Nambari',
            'bill-of-material'       => 'Bili ya Material',
            'source'                 => 'Chanzo',
            'responsible'            => 'Responsible',
            'mo-readiness'           => 'MO Readiness',
            'component-status'       => 'Component Hali',
            'quantity'               => 'Kiasi',
            'uom'                    => 'UoM',
            'consumption-efficiency' => 'Consumption Efficiency',
            'expected-duration'      => 'Inatarajiwa Duration',
            'real-duration'          => 'Real Duration',
            'company'                => 'Kampuni',
            'state'                  => 'Hali',
        ],
        'groups' => [
            'state'            => 'Hali',
            'product'          => 'Bidhaa',
            'bill-of-material' => 'Bili ya Material',
            'responsible'      => 'Responsible',
            'deadline'         => 'Deadline',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla',
                'entries' => [
                    'product'                  => 'Bidhaa',
                    'scheduled-date'           => 'Scheduled Tarehe',
                    'responsible'              => 'Responsible',
                    'quantity'                 => 'Kiasi',
                    'uom'                      => 'UoM',
                    'bill-of-material'         => 'Bili ya Material',
                    'operation-type'           => 'Operation Aina',
                    'consumption-efficiency'   => 'Consumption Efficiency',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Components',
                'process-note' => 'Components ita kuwa inapatikana baada ya uzalishaji process ni configured.',
                'columns'      => [
                    'component' => 'Component',
                    'quantity'  => 'Kiasi',
                    'uom'       => 'UoM',
                ],
            ],
            'work-orders' => [
                'title'        => 'Agizo la kazis',
                'process-note' => 'Agizo la kazis ita kuwa inapatikana baada ya uzalishaji process ni configured.',
                'columns'      => [
                    'operation'          => 'Operation',
                    'work-center'        => 'Work Center',
                    'product'            => 'Bidhaa',
                    'quantity-remaining' => 'Kiasi Iliyobaki',
                    'expected-duration'  => 'Inatarajiwa Duration',
                    'real-duration'      => 'Real Duration',
                    'lot-serial'         => 'Kundi/Nambari ya mfululizo',
                    'start'              => 'Anza',
                    'end'                => 'Mwisho',
                ],
            ],
            'by-products' => [
                'title'        => 'Na-Bidhaa',
                'process-note' => 'Na-bidhaa ita kuwa inapatikana baada ya uzalishaji process ni configured.',
                'columns'      => [
                    'product'    => 'Bidhaa',
                    'to'         => 'Hadi',
                    'to-produce' => 'Hadi Produce',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Miscellaneous',
                'entries' => [
                    'operation-type'             => 'Operation Aina',
                    'source'                     => 'Chanzo',
                    'finished-products-location' => 'Finished Bidhaa Mahali',
                    'company'                    => 'Kampuni',
                ],
            ],
        ],
    ],

    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label'        => 'Thibitisha',
                    'notification' => [
                        'title' => 'Uzalishaji agizo imethibitishwa',
                    ],
                ],

                'cancel' => [
                    'label'        => 'Ghairi',
                    'notification' => [
                        'title' => 'Uzalishaji agizo imeghairiwa',
                    ],
                ],
            ],
        ],
    ],
];

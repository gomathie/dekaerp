<?php

return [
    'title' => 'Sarafu',

    'navigation' => [
        'title' => 'Sarafu',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Sarafu Taarifa',

                'fields' => [
                    'name'         => 'Sarafu Jina',
                    'name-tooltip' => 'Ingiza official sarafu jina',
                    'symbol'       => 'Sarafu Symbol',
                    'full-name'    => 'Kamili Jina',
                    'iso-numeric'  => 'ISO Numeric Msimbo',
                ],
            ],

            'format-information' => [
                'title' => 'Umbizo Usanidi',

                'fields' => [
                    'decimal-places'        => 'Decimal Places',
                    'rounding'              => 'Rounding Precision',
                    'rounding-helper-text'  => 'Weka rounding precision kwa sarafu calculations',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Hali & Usanidi',

                'fields' => [
                    'status' => 'Hali',
                ],
            ],

            'rates' => [
                'title'       => 'Sarafu Viwango',
                'description' => 'Manage historic kiwango cha ubadilishajis kwa hii sarafu relative hadi base sarafu (USD).',

                'fields' => [
                    'name'              => 'Tarehe',
                    'unit-per-currency' => 'Uniti Kwa :currency',
                    'currency-per-unit' => ':currency Kwa Uniti',
                ],

                'add-rate'   => 'Ongeza Kiwango',
                'item-label' => 'Kiwango',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Sarafu Jina',
            'symbol'         => 'Symbol',
            'full-name'      => 'Kamili Jina',
            'iso-numeric'    => 'ISO Msimbo',
            'decimal-places' => 'Decimal Places',
            'rounding'       => 'Rounding',
            'status'         => 'Hali',
            'created-at'     => 'Imeundwa',
            'updated-at'     => 'Imesasishwa',
        ],

        'groups' => [
            'name'           => 'Jina',
            'status'         => 'Hali',
            'decimal-places' => 'Decimal Places',
            'creation-date'  => 'Creation Tarehe',
            'last-update'    => 'Mwisho Sasisha',
        ],

        'filters' => [
            'status' => 'Hali',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title'   => 'Sarafu imefutwa',
                    'body'    => 'sarafu ina imefutwa successfully.',

                    'success' => [
                        'title' => 'Sarafu imefutwa',
                        'body'  => 'sarafu ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Sarafu could si kuwa imefutwa',
                        'body'  => 'sarafu haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],

            'deactivate' => [
                'notification' => [
                    'title' => 'Sarafu haiwezekani kuwa deactivated',
                    'body'  => 'Hii sarafu ni katika tumia na one au zaidi makampuni na haiwezekani kuwa deactivated.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sarafu imefutwa',
                    'body'  => 'sarafu ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Sarafu Taarifa',

                'entries' => [
                    'name'         => 'Sarafu Jina',
                    'symbol'       => 'Sarafu Symbol',
                    'full-name'    => 'Kamili Jina',
                    'iso-numeric'  => 'ISO Numeric Msimbo',
                ],
            ],

            'format-information' => [
                'title' => 'Umbizo Usanidi',

                'entries' => [
                    'decimal-places' => 'Decimal Places',
                    'rounding'       => 'Rounding Precision',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Hali & Usanidi',

                'entries' => [
                    'status' => 'Hali',
                ],
            ],

            'rates' => [
                'title'       => 'Sarafu Viwango',

                'entries' => [
                    'name'              => 'Tarehe',
                    'unit-per-currency' => 'Uniti Kwa :currency',
                    'currency-per-unit' => ':currency Kwa Uniti',
                ],
            ],
        ],
    ],
];

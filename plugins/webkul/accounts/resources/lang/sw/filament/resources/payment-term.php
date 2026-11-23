<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'Masharti ya malipo',
                'company'              => 'Kampuni',
                'company-placeholder'  => 'Zote Makampuni',
                'early-discount'       => 'Early Punguzo',
                'discount-days-prefix' => 'ikiwa imelipwa ndani ya',
                'discount-days-suffix' => 'siku',
                'reduced-tax'          => 'Reduced kodi',
                'note'                 => 'Maelezo',
                'status'               => 'Hali',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Due Terms',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value'                  => 'Thamani',
                            'due'                    => 'Due',
                            'delay-type'             => 'Delay Aina',
                            'days-on-the-next-month' => 'Siku washa ifuatayo mwezi',
                            'days'                   => 'Siku',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term' => 'Masharti ya malipo',
            'company'      => 'Kampuni',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'groups' => [
            'company-name'        => 'Kampuni Jina',
            'discount-days'       => 'Punguzo Siku',
            'early-pay-discount'  => 'Early Pay Punguzo',
            'payment-term'        => 'Masharti ya malipo',
            'display-on-invoice'  => 'Display washa Ankara',
            'early-discount'      => 'Early Punguzo',
            'discount-percentage' => 'Punguzo',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Masharti ya malipo restored',
                    'body'  => 'Masharti ya malipo ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Masharti ya malipo imefutwa',
                    'body'  => 'Masharti ya malipo ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Masharti ya malipo force imefutwa',
                        'body'  => 'Masharti ya malipo ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Masharti ya malipo force deletion imeshindwa',
                        'body'  => 'Masharti ya malipo could si kuwa force imefutwa kwa sababu hii ni associated uingizaji wa jarida.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Masharti ya malipo restored',
                    'body'  => 'masharti ya malipo ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Masharti ya malipo imefutwa',
                    'body'  => 'masharti ya malipo ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Masharti ya malipo force imefutwa',
                        'body'  => 'masharti ya malipo ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Masharti ya malipo force deletion imeshindwa',
                        'body'  => 'masharti ya malipo could si kuwa force imefutwa kwa sababu wao ina associated uingizaji wa jarida.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'         => 'Masharti ya malipo',
                'early-discount'       => 'Early Punguzo',
                'discount-percentage'  => 'Punguzo',
                'discount-days-prefix' => 'ikiwa imelipwa ndani ya',
                'discount-days-suffix' => 'siku',
                'reduced-tax'          => 'Reduced kodi',
                'note'                 => 'Maelezo',
                'status'               => 'Hali',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Due Terms',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value'                  => 'Thamani',
                            'due'                    => 'Due',
                            'delay-type'             => 'Delay Aina',
                            'days-on-the-next-month' => 'Siku washa ifuatayo mwezi',
                            'days'                   => 'Siku',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'Sharti la malipo',
                'company'              => 'Kampuni',
                'company-placeholder'  => 'Kampuni zote',
                'early-discount'       => 'Punguzo la mapema',
                'discount-days-prefix' => 'ikilipwa ndani ya',
                'discount-days-suffix' => 'siku',
                'reduced-tax'          => 'Kodi iliyopunguzwa',
                'note'                 => 'Kidokezo',
                'status'               => 'Hali',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Awamu za malipo',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value'                  => 'Thamani',
                            'due'                    => 'Inadaiwa',
                            'delay-type'             => 'Aina ya muda wa kusubiri',
                            'days-on-the-next-month' => 'Siku za mwezi unaofuata',
                            'days'                   => 'Siku',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term' => 'Sharti la malipo',
            'company'      => 'Kampuni',
            'created-at'   => 'Iliundwa',
            'updated-at'   => 'Ilisasishwa',
        ],

        'groups' => [
            'company-name'        => 'Jina la kampuni',
            'discount-days'       => 'Siku za punguzo',
            'early-pay-discount'  => 'Punguzo la malipo ya mapema',
            'payment-term'        => 'Sharti la malipo',
            'display-on-invoice'  => 'Onyesha kwenye ankara',
            'early-discount'      => 'Punguzo la mapema',
            'discount-percentage' => 'Asilimia ya punguzo',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sharti la malipo limerejeshwa',
                    'body'  => 'Sharti la malipo limerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sharti la malipo limefutwa',
                    'body'  => 'Sharti la malipo limefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Sharti la malipo limefutwa kabisa',
                        'body'  => 'Sharti la malipo limefutwa kabisa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Imeshindwa kufuta kabisa sharti la malipo',
                        'body'  => 'Sharti la malipo halikuweza kufutwa kabisa kwa sababu lina maingizo ya jarida yanayohusiana nalo.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Masharti ya malipo yamerejeshwa',
                    'body'  => 'Masharti ya malipo yamerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Masharti ya malipo yamefutwa',
                    'body'  => 'Masharti ya malipo yamefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Masharti ya malipo yamefutwa kabisa',
                        'body'  => 'Masharti ya malipo yamefutwa kabisa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Imeshindwa kufuta kabisa masharti ya malipo',
                        'body'  => 'Masharti ya malipo hayakuweza kufutwa kabisa kwa sababu yana maingizo ya jarida yanayohusiana nayo.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'         => 'Sharti la malipo',
                'early-discount'       => 'Punguzo la mapema',
                'discount-percentage'  => 'Asilimia ya punguzo',
                'discount-days-prefix' => 'ikilipwa ndani ya',
                'discount-days-suffix' => 'siku',
                'reduced-tax'          => 'Kodi iliyopunguzwa',
                'note'                 => 'Kidokezo',
                'status'               => 'Hali',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Awamu za malipo',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value'                  => 'Thamani',
                            'due'                    => 'Inadaiwa',
                            'delay-type'             => 'Aina ya muda wa kusubiri',
                            'days-on-the-next-month' => 'Siku za mwezi unaofuata',
                            'days'                   => 'Siku',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

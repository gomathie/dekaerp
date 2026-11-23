<?php

return [
    'table' => [
        'columns' => [
            'on-hand'    => 'Washa Mkono',
            'forecasted' => 'Forecasted',
        ],
    ],

    'navigation' => [
        'title' => 'Bidhaa',
        'group' => 'Ghala',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Ghala',

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Tracking',

                        'fields' => [
                            'track-inventory'              => 'Track Ghala',
                            'track-inventory-hint-tooltip' => 'storable bidhaa ni one hiyo requires ghala management..',
                            'track-by'                     => 'Track Na',
                            'expiration-date'              => 'Expiration Tarehe',
                            'expiration-date-hint-tooltip' => 'Ikiwa imechaguliwa, wewe unaweza specify expiration dates kwa bidhaa na yake associated kundi/nambari ya mfululizo numbers.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Shughuli',

                        'fields' => [
                            'routes'              => 'Njia',
                            'routes-hint-tooltip' => 'Based washa installed moduli, hii mpangilio allows wewe hadi define product\'s njia, kama hiyo kama purchasing, uzalishaji, au replenishing washa agizo.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Usafirishaji',

                        'fields' => [
                            'responsible'              => 'Responsible',
                            'responsible-hint-tooltip' => 'Usafirishaji lead time (katika siku) represents promised duration kati ya agizo la mauzo confirmation na bidhaa usafirishaji.',
                            'weight'                   => 'Uzito',
                            'volume'                   => 'Ujazo',
                            'sale-delay'               => 'Mteja Lead Time (Siku)',
                            'sale-delay-hint-tooltip'  => 'Usafirishaji lead time (katika siku) represents promised duration kati ya agizo la mauzo confirmation na bidhaa usafirishaji.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Traceability',

                        'fields' => [
                            'expiration-date'               => 'Expiration Tarehe (Siku)',
                            'expiration-date-hint-tooltip'  => 'Ikiwa imechaguliwa, wewe unaweza weka expiration dates kwa bidhaa na yake associated kundi/nambari ya mfululizo numbers.',
                            'best-before-date'              => 'Best Kabla ya Tarehe (Siku)',
                            'best-before-date-hint-tooltip' => 'nambari ya siku kabla ya expiration tarehe wakati bidhaa begins hadi deteriorate, though hii ni bado safe hadi tumia. Hii ni calculated based washa kundi/nambari ya mfululizo nambari.',
                            'removal-date'                  => 'Removal Tarehe (Siku)',
                            'removal-date-hint-tooltip'     => 'nambari ya siku kabla ya expiration tarehe wakati bidhaa unapaswa kuwa removed kutoka hisa. Hii ni calculated based washa kundi/nambari ya mfululizo nambari.',
                            'alert-date'                    => 'Alert Tarehe (Siku)',
                            'alert-date-hint-tooltip'       => 'nambari ya siku kabla ya expiration tarehe wakati alert unapaswa kuwa triggered kwa kundi/nambari ya mfululizo nambari. Hii ni calculated based washa kundi/nambari ya mfululizo nambari.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Ghala',

                'entries' => [
                ],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Tracking',

                        'entries' => [
                            'track-inventory' => 'Track Ghala',
                            'track-by'        => 'Track Na',
                            'expiration-date' => 'Expiration Tarehe',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Shughuli',

                        'entries' => [
                            'routes' => 'Njia',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Usafirishaji',

                        'entries' => [
                            'responsible' => 'Responsible',
                            'weight'      => 'Uzito',
                            'volume'      => 'Ujazo',
                            'sale-delay'  => 'Mteja Lead Time (Siku)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Traceability',

                        'entries' => [
                            'expiration-date'  => 'Expiration Tarehe (Siku)',
                            'best-before-date' => 'Best Kabla ya Tarehe (Siku)',
                            'removal-date'     => 'Removal Tarehe (Siku)',
                            'alert-date'       => 'Alert Tarehe (Siku)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

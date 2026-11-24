<?php

return [
    'table' => [
        'columns' => [
            'on-hand'    => 'Zilizopo',
            'forecasted' => 'Utabiri',
        ],
    ],

    'navigation' => [
        'title' => 'Bidhaa',
        'group' => 'Bidhaa ghalani',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Bidhaa ghalani',

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Ufuatiliaji',

                        'fields' => [
                            'track-inventory'              => 'Fuatilia bidhaa ghalani',
                            'track-inventory-hint-tooltip' => 'Bidhaa inayohifadhiwa ni ile inayohitaji usimamizi wa bidhaa ghalani.',
                            'track-by'                     => 'Fuatilia kwa',
                            'expiration-date'              => 'Tarehe ya kuisha muda',
                            'expiration-date-hint-tooltip' => 'Ikichaguliwa, unaweza kuweka tarehe za kuisha muda kwa bidhaa na makundi/nambari zake za mfululizo.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operesheni',

                        'fields' => [
                            'routes'              => 'Njia',
                            'routes-hint-tooltip' => 'Kulingana na moduli zilizosakinishwa, mpangilio huu hukuruhusu kuweka njia ya bidhaa, kama vile ununuzi, uzalishaji au kujaza upya kwa oda.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Lojistiki',

                        'fields' => [
                            'responsible'              => 'Msimamizi',
                            'responsible-hint-tooltip' => 'Muda wa uwasilishaji (kwa siku) ni muda ulioahidiwa kati ya kuthibitishwa kwa oda ya mauzo na uwasilishaji wa bidhaa.',
                            'weight'                   => 'Uzito',
                            'volume'                   => 'Ujazo',
                            'sale-delay'               => 'Muda wa uwasilishaji kwa mteja (siku)',
                            'sale-delay-hint-tooltip'  => 'Muda wa uwasilishaji (kwa siku) ni muda ulioahidiwa kati ya kuthibitishwa kwa oda ya mauzo na uwasilishaji wa bidhaa.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Ufuatiliaji wa asili',

                        'fields' => [
                            'expiration-date'               => 'Kuisha muda (siku)',
                            'expiration-date-hint-tooltip'  => 'Ikichaguliwa, unaweza kuweka tarehe za kuisha muda kwa bidhaa na makundi/nambari zake za mfululizo.',
                            'best-before-date'              => 'Bora kabla ya (siku)',
                            'best-before-date-hint-tooltip' => 'Idadi ya siku kabla ya tarehe ya kuisha muda ambapo bidhaa huanza kuharibika, ingawa bado ni salama kutumia. Hukokotolewa kulingana na kundi/nambari ya mfululizo.',
                            'removal-date'                  => 'Tarehe ya kuondoa (siku)',
                            'removal-date-hint-tooltip'     => 'Idadi ya siku kabla ya tarehe ya kuisha muda ambapo bidhaa inapaswa kuondolewa ghalani. Hukokotolewa kulingana na kundi/nambari ya mfululizo.',
                            'alert-date'                    => 'Tarehe ya tahadhari (siku)',
                            'alert-date-hint-tooltip'       => 'Idadi ya siku kabla ya tarehe ya kuisha muda ambapo tahadhari inapaswa kutolewa kwa kundi/nambari ya mfululizo. Hukokotolewa kulingana na kundi/nambari ya mfululizo.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Ziada',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Bidhaa ghalani',

                'entries' => [
                ],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Ufuatiliaji',

                        'entries' => [
                            'track-inventory' => 'Fuatilia bidhaa ghalani',
                            'track-by'        => 'Fuatilia kwa',
                            'expiration-date' => 'Tarehe ya kuisha muda',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operesheni',

                        'entries' => [
                            'routes' => 'Njia',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Lojistiki',

                        'entries' => [
                            'responsible' => 'Msimamizi',
                            'weight'      => 'Uzito',
                            'volume'      => 'Ujazo',
                            'sale-delay'  => 'Muda wa uwasilishaji kwa mteja (siku)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Ufuatiliaji wa asili',

                        'entries' => [
                            'expiration-date'  => 'Kuisha muda (siku)',
                            'best-before-date' => 'Bora kabla ya (siku)',
                            'removal-date'     => 'Tarehe ya kuondoa (siku)',
                            'alert-date'       => 'Tarehe ya tahadhari (siku)',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

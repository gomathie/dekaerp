<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'name'                         => 'Jina',
                'tax-type'                     => 'Aina ya kodi',
                'tax-computation'              => 'Ukokotoaji wa kodi',
                'tax-scope'                    => 'Wigo wa kodi',
                'status'                       => 'Hali',
                'amount'                       => 'Kiasi',
                'formula'                      => 'Fomula',
                'formula-helper-text'          => 'Hesabu pekee: namba, + - * / ( ), vitendakazi :functions na vigeu hivi: :variables',
                'children-taxes'               => 'Kodi tanzu',
                'children-taxes-helper-text'   => 'Kiasi cha kodi hii ni jumla ya kodi zake tanzu.',
                'children-taxes-type-mismatch' => 'Kodi tanzu lazima ziwe za aina ya kodi :type au zisiwe na aina. Hizi haziko hivyo: :taxes',
            ],

            'repeater' => [
                'invoice-repartition-lines' => [
                    'label' => 'Mistari ya mgawanyo wa ankara',
                ],

                'refund-repartition-lines' => [
                    'label' => 'Mistari ya mgawanyo wa marejesho',
                ],

                'fields' => [
                    'type'           => 'Aina',
                    'factor-percent' => 'Kipengele %',
                    'account'        => 'Akaunti',
                ],
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Chaguo za kina',

                    'fields' => [
                        'invoice-label'       => 'Lebo ya ankara',
                        'tax-group'           => 'Kikundi cha kodi',
                        'country'             => 'Nchi',
                        'include-in-price'    => 'Imejumuishwa kwenye bei',
                        'include-base-amount' => 'Huathiri msingi wa kodi zinazofuata',
                        'is-base-affected'    => 'Msingi huathiriwa na kodi zilizotangulia',
                    ],
                ],

                'fields' => [
                    'description' => 'Maelezo',
                    'legal-notes' => 'Vidokezo vya kisheria',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                   => 'Jina',
            'amount-type'            => 'Aina ya kiasi',
            'company'                => 'Kampuni',
            'tax-group'              => 'Kikundi cha kodi',
            'country'                => 'Nchi',
            'tax-type'               => 'Aina ya kodi',
            'tax-scope'              => 'Wigo wa kodi',
            'amount-type'            => 'Aina ya kiasi',
            'invoice-label'          => 'Lebo ya ankara',
            'tax-exigibility'        => 'Wakati kodi inadaiwa',
            'price-include-override' => 'Ubatilishaji wa ujumuishaji wa bei',
            'amount'                 => 'Kiasi',
            'status'                 => 'Hali',
            'include-base-amount'    => 'Jumuisha kiasi cha msingi',
            'is-base-affected'       => 'Msingi umeathiriwa',
        ],

        'groups' => [
            'name'         => 'Jina',
            'company'      => 'Kampuni',
            'tax-group'    => 'Kikundi cha kodi',
            'country'      => 'Nchi',
            'created-by'   => 'Imeundwa na',
            'type-tax-use' => 'Matumizi ya kodi',
            'tax-scope'    => 'Wigo wa kodi',
            'amount-type'  => 'Aina ya kiasi',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kodi imefutwa',
                        'body'  => 'Kodi imefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Kodi haikuweza kufutwa',
                        'body'  => 'Kodi haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kodi zimefutwa',
                        'body'  => 'Kodi zimefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Kodi hazikuweza kufutwa',
                        'body'  => 'Kodi haziwezi kufutwa kwa sababu zinatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'pages' => [
            'create' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Mistari ya mgawanyo si sahihi',
                    ],
                ],
            ],

            'edit' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Mistari ya mgawanyo si sahihi',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'            => 'Jina',
                'tax-type'        => 'Aina ya kodi',
                'tax-computation' => 'Ukokotoaji wa kodi',
                'tax-scope'       => 'Wigo wa kodi',
                'status'          => 'Hali',
                'amount'          => 'Kiasi',
                'formula'         => 'Fomula',
                'children-taxes'  => 'Kodi tanzu',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Chaguo za kina',

                    'entries' => [
                        'invoice-label'       => 'Lebo ya ankara',
                        'tax-group'           => 'Kikundi cha kodi',
                        'country'             => 'Nchi',
                        'include-in-price'    => 'Jumuisha kwenye bei',
                        'include-base-amount' => 'Jumuisha kiasi cha msingi',
                        'is-base-affected'    => 'Msingi umeathiriwa',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'Maelezo na vidokezo vya kisheria vya ankara',
                    'entries' => [
                        'description' => 'Maelezo',
                        'legal-notes' => 'Vidokezo vya kisheria',
                    ],
                ],
            ],
        ],
    ],

];

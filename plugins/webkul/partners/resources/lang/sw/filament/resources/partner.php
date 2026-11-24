<?php

return [
    'global-search' => [
        'email' => 'Barua pepe',
        'phone' => 'Simu',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'company'    => 'Kampuni',
                    'avatar'     => 'Picha',
                    'tax-id'     => 'Namba ya kodi (TIN)',
                    'job-title'  => 'Cheo cha kazi',
                    'phone'      => 'Simu',
                    'mobile'     => 'Simu ya mkononi',
                    'email'      => 'Barua pepe',
                    'website'    => 'Tovuti',
                    'title'      => 'Cheo',
                    'name'       => 'Jina',
                    'short-name' => 'Jina fupi',
                    'tags'       => 'Lebo',
                    'color'      => 'Rangi',
                ],

                'address' => [
                    'title' => 'Anwani',

                    'fields' => [
                        'street1'  => 'Mtaa 1',
                        'street2'  => 'Mtaa 2',
                        'city'     => 'Jiji',
                        'zip'      => 'Msimbo wa posta',
                        'state'    => 'Mkoa',
                        'country'  => 'Nchi',
                        'name'     => 'Jina',
                        'code'     => 'Msimbo',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Mauzo na ununuzi',

                'fields' => [
                    'responsible'           => 'Msimamizi',
                    'responsible-hint-text' => 'Huyu ni muuzaji wa ndani anayemhudumia mteja huyu',
                    'company-id'            => 'Kitambulisho cha kampuni',
                    'company-id-hint-text'  => 'Namba ya usajili wa kampuni, ikiwa ni tofauti na namba ya kodi. Lazima iwe ya kipekee kwa wahusika wote wa nchi moja.',
                    'reference'             => 'Rejea',
                    'industry'              => 'Sekta',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'parent'     => 'Mhusika mzazi',
            'work-email' => 'Barua pepe ya kazini',
            'work-phone' => 'Simu ya kazini',
        ],

        'groups' => [
            'account-type' => 'Aina ya akaunti',
            'parent'       => 'Mhusika mzazi',
            'title'        => 'Cheo',
            'job-title'    => 'Cheo cha kazi',
            'industry'     => 'Sekta',
        ],

        'filters' => [
            'account-type'     => 'Aina ya akaunti',
            'name'             => 'Jina',
            'email'            => 'Barua pepe',
            'parent'           => 'Mhusika mzazi',
            'title'            => 'Cheo',
            'tax-id'           => 'Namba ya kodi (TIN)',
            'phone'            => 'Simu',
            'mobile'           => 'Simu ya mkononi',
            'job-title'        => 'Cheo cha kazi',
            'website'          => 'Tovuti',
            'company-registry' => 'Namba ya usajili',
            'responsible'      => 'Msimamizi',
            'reference'        => 'Rejea',
            'parent'           => 'Mhusika mzazi',
            'creator'          => 'Muundaji',
            'company'          => 'Kampuni',
            'industry'         => 'Sekta',
            'industry'         => 'Sekta',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mhusika amesasishwa',
                    'body'  => 'Mhusika amesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Mhusika amerejeshwa',
                    'body'  => 'Mhusika amerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mhusika amefutwa',
                    'body'  => 'Mhusika amefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mhusika amefutwa kabisa',
                        'body'  => 'Mhusika amefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Mhusika hawezi kufutwa',
                        'body'  => 'Mhusika hawezi kufutwa kwa sababu anatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Wahusika wamerejeshwa',
                    'body'  => 'Wahusika wamerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Wahusika wamefutwa',
                    'body'  => 'Wahusika wamefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Wahusika wamefutwa kabisa',
                        'body'  => 'Wahusika wamefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Wahusika hawawezi kufutwa',
                        'body'  => 'Wahusika hawawezi kufutwa kwa sababu wanatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'company'    => 'Kampuni',
                    'avatar'     => 'Picha',
                    'tax-id'     => 'Namba ya kodi (TIN)',
                    'job-title'  => 'Cheo cha kazi',
                    'phone'      => 'Simu',
                    'mobile'     => 'Simu ya mkononi',
                    'email'      => 'Barua pepe',
                    'website'    => 'Tovuti',
                    'title'      => 'Cheo',
                    'name'       => 'Jina',
                    'short-name' => 'Jina fupi',
                    'tags'       => 'Lebo',
                ],

                'address' => [
                    'title' => 'Anwani',

                    'fields' => [
                        'street1'  => 'Mtaa 1',
                        'street2'  => 'Mtaa 2',
                        'city'     => 'Jiji',
                        'zip'      => 'Msimbo wa posta',
                        'state'    => 'Mkoa',
                        'country'  => 'Nchi',
                        'name'     => 'Jina',
                        'code'     => 'Msimbo',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Mauzo na ununuzi',

                'fields' => [
                    'responsible'           => 'Msimamizi',
                    'responsible-hint-text' => 'Huyu ni muuzaji wa ndani anayemhudumia mteja huyu',
                    'company-id'            => 'Kitambulisho cha kampuni',
                    'company-id-hint-text'  => 'Namba ya usajili wa kampuni. Itumie ikiwa ni tofauti na namba ya kodi. Lazima iwe ya kipekee kwa wahusika wote wa nchi moja.',
                    'reference'             => 'Rejea',
                    'industry'              => 'Sekta',
                ],
            ],
        ],
    ],
];

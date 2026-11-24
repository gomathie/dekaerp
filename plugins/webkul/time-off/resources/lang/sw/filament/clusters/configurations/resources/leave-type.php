<?php

return [
    'title'      => 'Aina ya likizo',
    'navigation' => [
        'title' => 'Aina ya likizo',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Taarifa za jumla',
                'fields' => [
                    'name'                => 'Jina',
                    'approval'            => 'Idhini',
                    'requires-allocation' => 'Inahitaji mgao',
                    'employee-requests'   => 'Maombi ya wafanyakazi',
                    'display-option'      => 'Chaguo la kuonyesha',
                ],
            ],
            'display-option' => [
                'title'  => 'Chaguo la kuonyesha',
                'fields' => [
                    'color' => 'Rangi',
                ],
            ],
            'configuration' => [
                'title' => 'Mipangilio',

                'fields' => [
                    'notified-time-off-officers'          => 'Maafisa wa likizo wanaoarifiwa',
                    'take-time-off-in'                    => 'Chukua likizo kwa',
                    'public-holiday-included'             => 'Sikukuu za umma zimejumuishwa',
                    'allow-to-attach-supporting-document' => 'Ruhusu kuambatisha hati ya ushahidi',
                    'show-on-dashboard'                   => 'Onyesha kwenye dashibodi',
                    'allow-negative-cap'                  => 'Ruhusu salio hasi',
                    'kind-off-time'                       => 'Aina ya muda',
                    'max-negative-cap'                    => 'Kiwango cha juu cha salio hasi',
                    'kind-of-time'                        => 'Aina ya likizo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                      => 'Jina',
            'company-name'              => 'Kampuni',
            'color'                     => 'Rangi',
            'notified-time-officers'    => 'Maafisa wanaoarifiwa',
            'time-off-approval'         => 'Idhini ya likizo',
            'requires-allocation'       => 'Inahitaji mgao',
            'allocation-approval'       => 'Idhini ya mgao',
            'employee-request'          => 'Ombi la mfanyakazi',
        ],

        'filters' => [
            'name'                => 'Jina',
            'company-name'        => 'Kampuni',
            'time-off-approval'   => 'Idhini ya likizo',
            'requires-allocation' => 'Inahitaji mgao',
            'time-type'           => 'Aina ya muda',
            'request-unit'        => 'Kipimo cha ombi',
            'created-by'          => 'Imeundwa na',
            'employee-request'    => 'Ombi la mfanyakazi',
            'created-at'          => 'Iliundwa',
            'updated-at'          => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Aina ya likizo imefutwa',
                    'body'  => 'Aina ya likizo imefutwa kwa mafanikio.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Aina ya likizo imerejeshwa',
                    'body'  => 'Aina ya likizo imerejeshwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Aina ya likizo imerejeshwa',
                    'body'  => 'Aina ya likizo imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aina ya likizo imefutwa',
                    'body'  => 'Aina ya likizo imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Aina ya likizo imefutwa kabisa',
                        'body'  => 'Aina ya likizo imefutwa kabisa kwa mafanikio.',
                    ],
                    'error' => [
                        'title' => 'Aina ya likizo haikuweza kufutwa',
                        'body'  => 'Aina ya likizo haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'name'                => 'Jina',
                    'approval'            => 'Idhini',
                    'requires-allocation' => 'Inahitaji mgao',
                    'employee-requests'   => 'Maombi ya wafanyakazi',
                    'display-option'      => 'Chaguo la kuonyesha',
                ],
            ],
            'display-option' => [
                'title'   => 'Chaguo la kuonyesha',
                'entries' => [
                    'color' => 'Rangi',
                ],
            ],
            'configuration' => [
                'title' => 'Mipangilio',

                'entries' => [
                    'notified-time-off-officers'          => 'Maafisa wa likizo wanaoarifiwa',
                    'take-time-off-in'                    => 'Chukua likizo kwa',
                    'public-holiday-included'             => 'Sikukuu za umma zimejumuishwa',
                    'allow-to-attach-supporting-document' => 'Ruhusu kuambatisha hati ya ushahidi',
                    'show-on-dashboard'                   => 'Onyesha kwenye dashibodi',
                    'kind-off-time'                       => 'Aina ya muda',
                    'max-negative-cap'                    => 'Kiwango cha juu cha salio hasi',
                    'kind-of-time'                        => 'Aina ya likizo',
                ],
            ],
        ],
    ],
];

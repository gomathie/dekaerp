<?php

return [
    'navigation' => [
        'title' => 'Vituo vya kazi',
        'group' => 'Mipangilio',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'name'                     => 'Jina',
                    'name-placeholder'         => 'mf. Mstari wa kuunganisha 1',
                    'code'                     => 'Msimbo',
                    'code-placeholder'         => 'mf. AL1',
                    'working-state'            => 'Hali ya kazi',
                    'color'                    => 'Rangi',
                    'tags'                     => 'Lebo',
                    'alternative-work-centers' => 'Vituo mbadala vya kazi',
                    'company'                  => 'Kampuni',
                    'calendar'                 => 'Saa za kazi',
                ],
            ],

            'information' => [
                'title'     => 'Taarifa za jumla',
                'fieldsets' => [
                    'production-information' => 'Taarifa za uzalishaji',
                    'costing-information'    => 'Taarifa za gharama',
                ],
                'fields' => [
                    'default-capacity' => 'Uwezo chaguo-msingi',
                    'time-efficiency'  => 'Ufanisi wa muda',
                    'oee-target'       => 'Lengo la OEE',
                    'costs-per-hour'   => 'Gharama kwa saa',
                    'cost-suffix'      => 'kwa saa',
                    'setup-time'       => 'Muda wa maandalizi',
                    'cleanup-time'     => 'Muda wa kusafisha',
                    'time-suffix'      => 'dakika',
                ],
            ],

            'description' => [
                'title'  => 'Maelezo',
                'fields' => [
                    'note'             => 'Maelezo',
                    'note-placeholder' => 'Maelezo ya kituo cha kazi...',
                ],
            ],

            'specific-capacity' => [
                'title'  => 'Uwezo maalum',
                'fields' => [
                    'records' => 'Uwezo maalum',
                ],
                'columns' => [
                    'product'      => 'Bidhaa',
                    'product-uom'  => 'Kipimo',
                    'capacity'     => 'Uwezo',
                    'setup-time'   => 'Muda wa maandalizi',
                    'cleanup-time' => 'Muda wa kusafisha',
                ],
                'actions' => [
                    'add' => 'Ongeza mstari',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Jina',
            'code'             => 'Msimbo',
            'company'          => 'Kampuni',
            'calendar'         => 'Saa za kazi',
            'working-state'    => 'Hali ya kazi',
            'default-capacity' => 'Uwezo',
            'time-efficiency'  => 'Ufanisi',
            'costs-per-hour'   => 'Gharama kwa saa',
            'deleted-at'       => 'Kilifutwa',
            'created-at'       => 'Kiliundwa',
            'updated-at'       => 'Kilisasishwa',
        ],

        'groups' => [
            'company' => 'Kampuni',
        ],

        'filters' => [
            'company'       => 'Kampuni',
            'working-state' => 'Hali ya kazi',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kituo cha kazi kimerejeshwa',
                    'body'  => 'Kituo cha kazi kimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kituo cha kazi kimewekwa kwenye kumbukumbu',
                    'body'  => 'Kituo cha kazi kimewekwa kwenye kumbukumbu kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kituo cha kazi kimefutwa',
                        'body'  => 'Kituo cha kazi kimefutwa kabisa.',
                    ],

                    'error' => [
                        'title' => 'Kituo cha kazi hakikuweza kufutwa',
                        'body'  => 'Kituo cha kazi hakiwezi kufutwa kwa sababu kinatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Vituo vya kazi vimerejeshwa',
                    'body'  => 'Vituo vya kazi vilivyochaguliwa vimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Vituo vya kazi vimewekwa kwenye kumbukumbu',
                    'body'  => 'Vituo vya kazi vilivyochaguliwa vimewekwa kwenye kumbukumbu kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Vituo vya kazi vimefutwa',
                        'body'  => 'Vituo vya kazi vilivyochaguliwa vimefutwa kabisa.',
                    ],

                    'error' => [
                        'title' => 'Vituo vya kazi havikuweza kufutwa',
                        'body'  => 'Kituo kimoja au zaidi kati ya vilivyochaguliwa kinatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'name'                     => 'Jina la kituo cha kazi',
                    'code'                     => 'Msimbo',
                    'working-state'            => 'Hali ya kazi',
                    'tags'                     => 'Lebo',
                    'alternative-work-centers' => 'Vituo mbadala vya kazi',
                    'company'                  => 'Kampuni',
                    'calendar'                 => 'Saa za kazi',
                ],
            ],

            'information' => [
                'title'     => 'Taarifa za jumla',
                'fieldsets' => [
                    'production-information' => 'Taarifa za uzalishaji',
                    'costing-information'    => 'Taarifa za gharama',
                ],

                'entries' => [
                    'default-capacity' => 'Uwezo chaguo-msingi',
                    'time-efficiency'  => 'Ufanisi wa muda',
                    'oee-target'       => 'Lengo la OEE',
                    'costs-per-hour'   => 'Gharama kwa saa',
                    'cost-suffix'      => 'kwa kituo cha kazi',
                    'setup-time'       => 'Muda wa maandalizi',
                    'cleanup-time'     => 'Muda wa kusafisha',
                    'time-suffix'      => 'dakika',
                ],
            ],

            'description' => [
                'title'   => 'Maelezo',
                'entries' => [
                    'note' => 'Maelezo',
                ],
            ],

            'specific-capacity' => [
                'title'   => 'Uwezo maalum',
                'columns' => [
                    'product'      => 'Bidhaa',
                    'product-uom'  => 'Kipimo',
                    'capacity'     => 'Uwezo',
                    'setup-time'   => 'Muda wa maandalizi',
                    'cleanup-time' => 'Muda wa kusafisha',
                ],
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'created-by'   => 'Imeundwa na',
                    'created-at'   => 'Kiliundwa',
                    'last-updated' => 'Kilisasishwa mwisho',
                ],
            ],
        ],
    ],
];

<?php

return [
    'navigation' => [
        'title' => 'Chakavu',
        'group' => 'Marekebisho',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'product'              => 'Bidhaa',
                    'package'              => 'Kifurushi',
                    'quantity'             => 'Idadi',
                    'unit'                 => 'Kipimo',
                    'lot'                  => 'Kundi/Mfululizo',
                    'tags'                 => 'Tagi',
                    'name'                 => 'Jina',
                    'color'                => 'Rangi',
                    'owner'                => 'Mmiliki',
                    'source-location'      => 'Eneo la chanzo',
                    'destination-location' => 'Eneo la chakavu',
                    'source-document'      => 'Hati chanzo',
                    'company'              => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Tarehe',
            'reference'       => 'Marejeleo',
            'product'         => 'Bidhaa',
            'package'         => 'Kifurushi',
            'quantity'        => 'Idadi',
            'uom'             => 'Kipimo',
            'source-location' => 'Eneo la chanzo',
            'scrap-location'  => 'Eneo la chakavu',
            'unit'            => 'Kipimo',
            'lot'             => 'Kundi/Mfululizo',
            'tags'            => 'Tagi',
            'state'           => 'Hali',
        ],

        'groups' => [
            'product'              => 'Bidhaa',
            'source-location'      => 'Eneo la chanzo',
            'destination-location' => 'Eneo la chakavu',
        ],

        'filters' => [
            'source-location'      => 'Eneo la chanzo',
            'destination-location' => 'Eneo la chakavu',
            'product'              => 'Bidhaa',
            'state'                => 'Hali',
            'product-category'     => 'Kategoria ya bidhaa',
            'uom'                  => 'Kipimo',
            'lot'                  => 'Kundi/Mfululizo',
            'package'              => 'Kifurushi',
            'tags'                 => 'Tagi',
            'company'              => 'Kampuni',
            'quantity'             => 'Idadi',
            'creator'              => 'Muundaji',
            'closed-at'            => 'Ilifungwa',
            'created-at'           => 'Iliundwa',
            'updated-at'           => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rekodi ya chakavu imefutwa',
                        'body'  => 'Rekodi ya chakavu imefutwa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Rekodi ya chakavu haikuweza kufutwa',
                        'body'  => 'Rekodi ya chakavu haiwezi kufutwa kwa sababu inatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rekodi za chakavu zimefutwa',
                        'body'  => 'Rekodi za chakavu zilizochaguliwa zimefutwa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Rekodi za chakavu hazikuweza kufutwa',
                        'body'  => 'Rekodi za chakavu haziwezi kufutwa kwa sababu zinatumika.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Maelezo ya chakavu',

                'entries' => [
                    'product'              => 'Bidhaa',
                    'quantity'             => 'Idadi',
                    'lot'                  => 'Kundi',
                    'tags'                 => 'Tagi',
                    'package'              => 'Kifurushi',
                    'owner'                => 'Mmiliki',
                    'source-location'      => 'Eneo la chanzo',
                    'destination-location' => 'Eneo la chakavu',
                    'source-document'      => 'Hati chanzo',
                    'company'              => 'Kampuni',
                ],
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'created-by'   => 'Imeundwa na',
                    'created-at'   => 'Iliundwa',
                    'last-updated' => 'Ilisasishwa mwisho',
                ],
            ],
        ],
    ],
];

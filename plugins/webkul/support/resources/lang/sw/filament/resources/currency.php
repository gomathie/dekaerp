<?php

return [
    'title' => 'Sarafu',

    'navigation' => [
        'title' => 'Sarafu',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Taarifa za sarafu',

                'fields' => [
                    'name'         => 'Jina la sarafu',
                    'name-tooltip' => 'Weka jina rasmi la sarafu',
                    'symbol'       => 'Alama ya sarafu',
                    'full-name'    => 'Jina kamili',
                    'iso-numeric'  => 'Msimbo wa namba wa ISO',
                ],
            ],

            'format-information' => [
                'title' => 'Mipangilio ya muundo',

                'fields' => [
                    'decimal-places'        => 'Nafasi za desimali',
                    'rounding'              => 'Usahihi wa ukadiriaji',
                    'rounding-helper-text'  => 'Weka usahihi wa ukadiriaji kwa hesabu za sarafu',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Hali na mipangilio',

                'fields' => [
                    'status' => 'Hali',
                ],
            ],

            'rates' => [
                'title'       => 'Viwango vya sarafu',
                'description' => 'Simamia historia ya viwango vya ubadilishaji vya sarafu hii ikilinganishwa na sarafu ya msingi (USD).',

                'fields' => [
                    'name'              => 'Tarehe',
                    'unit-per-currency' => 'Vizio kwa kila :currency',
                    'currency-per-unit' => ':currency kwa kila kizio',
                ],

                'add-rate'   => 'Ongeza kiwango',
                'item-label' => 'Kiwango',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Jina la sarafu',
            'symbol'         => 'Alama',
            'full-name'      => 'Jina kamili',
            'iso-numeric'    => 'Msimbo wa ISO',
            'decimal-places' => 'Nafasi za desimali',
            'rounding'       => 'Ukadiriaji',
            'status'         => 'Hali',
            'created-at'     => 'Iliundwa',
            'updated-at'     => 'Ilisasishwa',
        ],

        'groups' => [
            'name'           => 'Jina',
            'status'         => 'Hali',
            'decimal-places' => 'Nafasi za desimali',
            'creation-date'  => 'Tarehe ya kuundwa',
            'last-update'    => 'Sasisho la mwisho',
        ],

        'filters' => [
            'status' => 'Hali',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title'   => 'Sarafu imefutwa',
                    'body'    => 'Sarafu imefutwa kikamilifu.',

                    'success' => [
                        'title' => 'Sarafu imefutwa',
                        'body'  => 'Sarafu imefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Sarafu haikuweza kufutwa',
                        'body'  => 'Sarafu haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                    ],
                ],
            ],

            'deactivate' => [
                'notification' => [
                    'title' => 'Sarafu haiwezi kuzimwa',
                    'body'  => 'Sarafu hii inatumiwa na kampuni moja au zaidi na haiwezi kuzimwa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sarafu zimefutwa',
                    'body'  => 'Sarafu zimefutwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Taarifa za sarafu',

                'entries' => [
                    'name'         => 'Jina la sarafu',
                    'symbol'       => 'Alama ya sarafu',
                    'full-name'    => 'Jina kamili',
                    'iso-numeric'  => 'Msimbo wa namba wa ISO',
                ],
            ],

            'format-information' => [
                'title' => 'Mipangilio ya muundo',

                'entries' => [
                    'decimal-places' => 'Nafasi za desimali',
                    'rounding'       => 'Usahihi wa ukadiriaji',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Hali na mipangilio',

                'entries' => [
                    'status' => 'Hali',
                ],
            ],

            'rates' => [
                'title'       => 'Viwango vya sarafu',

                'entries' => [
                    'name'              => 'Tarehe',
                    'unit-per-currency' => 'Vizio kwa kila :currency',
                    'currency-per-unit' => ':currency kwa kila kizio',
                ],
            ],
        ],
    ],
];

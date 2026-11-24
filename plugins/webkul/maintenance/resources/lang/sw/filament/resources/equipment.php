<?php

return [
    'navigation' => [
        'title' => 'Vifaa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Taarifa za jumla',
                'fields' => [
                    'name' => 'Jina',
                    'note' => 'Maelezo',
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',
                'fields' => [
                    'category'   => 'Kategoria ya kifaa',
                    'team'       => 'Timu ya matengenezo',
                    'company'    => 'Kampuni',
                    'technician' => 'Fundi',
                    'owner'      => 'Mmiliki',
                    'location'   => 'Mahali kinapotumika',
                ],
            ],

            'product-information' => [
                'title'  => 'Taarifa za bidhaa',
                'fields' => [
                    'partner'                     => 'Msambazaji',
                    'partner-ref'                 => 'Rejea ya msambazaji',
                    'model'                       => 'Modeli',
                    'serial-no'                   => 'Nambari ya mfululizo',
                    'effective-date'              => 'Tarehe ya kuanza kutumika',
                    'effective-date-hint-tooltip' => 'Hutumika kama mwanzo wa kukokotoa wastani wa muda kati ya hitilafu.',
                    'cost'                        => 'Gharama',
                    'warranty-date'               => 'Mwisho wa dhamana',
                ],
            ],

            'maintenance' => [
                'title'  => 'Matengenezo',
                'fields' => [
                    'expected-mtbf' => 'Wastani unaotarajiwa wa muda kati ya hitilafu',
                ],
                'suffixes' => [
                    'days' => 'siku',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina la kifaa',
            'owner'      => 'Mmiliki',
            'serial-no'  => 'Nambari ya mfululizo',
            'category'   => 'Kategoria ya kifaa',
            'technician' => 'Fundi',
            'company'    => 'Kampuni',
            'created-at' => 'Iliundwa',
        ],

        'filters' => [
            'category'   => 'Kategoria ya kifaa',
            'team'       => 'Timu ya matengenezo',
            'technician' => 'Fundi',
        ],

        'groups' => [
            'category'   => 'Kategoria ya kifaa',
            'owner'      => 'Mmiliki',
            'technician' => 'Fundi',
            'vendor'     => 'Msambazaji',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kifaa kimesasishwa',
                    'body'  => 'Kifaa kimesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kifaa kimerejeshwa',
                    'body'  => 'Kifaa kimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kifaa kimewekwa kumbukumbu',
                    'body'  => 'Kifaa kimewekwa kumbukumbu kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kifaa kimefutwa',
                        'body'  => 'Kifaa kimefutwa kabisa.',
                    ],

                    'error' => [
                        'title' => 'Kifaa hakikuweza kufutwa',
                        'body'  => 'Kifaa hiki kinatumiwa na rekodi nyingine.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Vifaa vimerejeshwa',
                    'body'  => 'Vifaa vilivyochaguliwa vimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Vifaa vimewekwa kumbukumbu',
                    'body'  => 'Vifaa vilivyochaguliwa vimewekwa kumbukumbu kwa mafanikio.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Kifaa kimeundwa',
                    'body'  => 'Kifaa kimeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'name' => 'Jina',
                    'note' => 'Maelezo',
                ],
            ],

            'settings' => [
                'title'   => 'Mipangilio',
                'entries' => [
                    'category'   => 'Kategoria ya kifaa',
                    'team'       => 'Timu ya matengenezo',
                    'company'    => 'Kampuni',
                    'technician' => 'Fundi',
                    'owner'      => 'Mmiliki',
                    'location'   => 'Mahali kinapotumika',
                ],
            ],

            'product-information' => [
                'title'   => 'Taarifa za bidhaa',
                'entries' => [
                    'partner'        => 'Msambazaji',
                    'partner-ref'    => 'Rejea ya msambazaji',
                    'model'          => 'Modeli',
                    'serial-no'      => 'Nambari ya mfululizo',
                    'effective-date' => 'Tarehe ya kuanza kutumika',
                    'cost'           => 'Gharama',
                    'warranty-date'  => 'Mwisho wa dhamana',
                ],
            ],

            'maintenance' => [
                'title'   => 'Matengenezo',
                'entries' => [
                    'expected-mtbf'          => 'Wastani unaotarajiwa wa muda kati ya hitilafu',
                    'maintenance-count'      => 'Idadi ya matengenezo',
                    'maintenance-open-count' => 'Matengenezo yaliyo wazi',
                    'assigned-at'            => 'Tarehe ya kugawiwa',
                    'scraped-at'             => 'Tarehe ya kuondolewa',
                ],
                'suffixes' => [
                    'days' => 'siku',
                ],
            ],
        ],
    ],
];

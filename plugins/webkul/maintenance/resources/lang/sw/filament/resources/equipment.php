<?php

return [
    'navigation' => [
        'title' => 'Equipment',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla Taarifa',
                'fields' => [
                    'name' => 'Jina',
                    'note' => 'Maelezo',
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',
                'fields' => [
                    'category'   => 'Equipment Kategoria',
                    'team'       => 'Matengenezo Timu',
                    'company'    => 'Kampuni',
                    'technician' => 'Technician',
                    'owner'      => 'Mmiliki',
                    'location'   => 'Inatumika katika mahali',
                ],
            ],

            'product-information' => [
                'title'  => 'Bidhaa Taarifa',
                'fields' => [
                    'partner'                     => 'Msambazaji',
                    'partner-ref'                 => 'Msambazaji Kumbukumbu',
                    'model'                       => 'Model',
                    'serial-no'                   => 'Nambari ya mfululizo Nambari',
                    'effective-date'              => 'Effective Tarehe',
                    'effective-date-hint-tooltip' => 'Inatumika kama starting point kwa calculating Mean Time Kati ya Kushindwa.',
                    'cost'                        => 'Gharama',
                    'warranty-date'               => 'Warranty Expiration Tarehe',
                ],
            ],

            'maintenance' => [
                'title'  => 'Matengenezo',
                'fields' => [
                    'expected-mtbf' => 'Inatarajiwa Mean Time Kati ya Kushindwa',
                ],
                'suffixes' => [
                    'days' => 'siku',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Equipment Jina',
            'owner'      => 'Mmiliki',
            'serial-no'  => 'Nambari ya mfululizo Nambari',
            'category'   => 'Equipment Kategoria',
            'technician' => 'Technician',
            'company'    => 'Kampuni',
            'created-at' => 'Imeundwa',
        ],

        'filters' => [
            'category'   => 'Equipment Kategoria',
            'team'       => 'Matengenezo Timu',
            'technician' => 'Technician',
        ],

        'groups' => [
            'category'   => 'Equipment Kategoria',
            'owner'      => 'Mmiliki',
            'technician' => 'Technician',
            'vendor'     => 'Msambazaji',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipment imesasishwa',
                    'body'  => 'equipment ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Equipment restored',
                    'body'  => 'equipment ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipment imehifadhiwa',
                    'body'  => 'equipment ina imehifadhiwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Equipment imefutwa',
                        'body'  => 'equipment ina permanently imefutwa.',
                    ],

                    'error' => [
                        'title' => 'Equipment could si kuwa imefutwa',
                        'body'  => 'Hii equipment ni referenced na another rekodi.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Equipment restored',
                    'body'  => 'imechaguliwa equipment ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipment imehifadhiwa',
                    'body'  => 'imechaguliwa equipment ina imehifadhiwa successfully.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipment imeundwa',
                    'body'  => 'equipment ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla Taarifa',
                'entries' => [
                    'name' => 'Jina',
                    'note' => 'Maelezo',
                ],
            ],

            'settings' => [
                'title'   => 'Mipangilio',
                'entries' => [
                    'category'   => 'Equipment Kategoria',
                    'team'       => 'Matengenezo Timu',
                    'company'    => 'Kampuni',
                    'technician' => 'Technician',
                    'owner'      => 'Mmiliki',
                    'location'   => 'Inatumika katika mahali',
                ],
            ],

            'product-information' => [
                'title'   => 'Bidhaa Taarifa',
                'entries' => [
                    'partner'        => 'Msambazaji',
                    'partner-ref'    => 'Msambazaji Kumbukumbu',
                    'model'          => 'Model',
                    'serial-no'      => 'Nambari ya mfululizo Nambari',
                    'effective-date' => 'Effective Tarehe',
                    'cost'           => 'Gharama',
                    'warranty-date'  => 'Warranty Expiration Tarehe',
                ],
            ],

            'maintenance' => [
                'title'   => 'Matengenezo',
                'entries' => [
                    'expected-mtbf'          => 'Inatarajiwa Mean Time Kati ya Kushindwa',
                    'maintenance-count'      => 'Matengenezo Idadi',
                    'maintenance-open-count' => 'Wazi Matengenezo Idadi',
                    'assigned-at'            => 'Amekabidhiwa Tarehe',
                    'scraped-at'             => 'Scrap Tarehe',
                ],
                'suffixes' => [
                    'days' => 'siku',
                ],
            ],
        ],
    ],
];

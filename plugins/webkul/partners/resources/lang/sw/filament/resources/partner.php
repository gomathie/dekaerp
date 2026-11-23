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
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Kodi Kitambulisho',
                    'job-title'  => 'Kazi Kichwa',
                    'phone'      => 'Simu',
                    'mobile'     => 'Mobile',
                    'email'      => 'Barua pepe',
                    'website'    => 'Tovuti',
                    'title'      => 'Kichwa',
                    'name'       => 'Jina',
                    'short-name' => 'Short Jina',
                    'tags'       => 'Lebo',
                    'color'      => 'Rangi',
                ],

                'address' => [
                    'title' => 'Anwani',

                    'fields' => [
                        'street1'  => 'Street 1',
                        'street2'  => 'Street 2',
                        'city'     => 'Jiji',
                        'zip'      => 'ZIP',
                        'state'    => 'Hali',
                        'country'  => 'Nchi',
                        'name'     => 'Jina',
                        'code'     => 'Msimbo',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Mauzo na Ununuzi',

                'fields' => [
                    'responsible'           => 'Responsible',
                    'responsible-hint-text' => 'Hii ni ndani salesperson responsible kwa hii mteja',
                    'company-id'            => 'Kampuni Kitambulisho',
                    'company-id-hint-text'  => 'company\'s registry nambari, inatumika ikiwa different kutoka Kodi Kitambulisho. Hii lazima kuwa unique among zote washirika ndani ya ile ile nchi.',
                    'reference'             => 'Kumbukumbu',
                    'industry'              => 'Industry',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'parent'     => 'Parent',
            'work-email' => 'Work Barua pepe',
            'work-phone' => 'Work Simu',
        ],

        'groups' => [
            'account-type' => 'Akaunti Aina',
            'parent'       => 'Parent',
            'title'        => 'Kichwa',
            'job-title'    => 'Kazi Kichwa',
            'industry'     => 'Industry',
        ],

        'filters' => [
            'account-type'     => 'Akaunti Aina',
            'name'             => 'Jina',
            'email'            => 'Barua pepe',
            'parent'           => 'Parent',
            'title'            => 'Kichwa',
            'tax-id'           => 'Kodi Kitambulisho',
            'phone'            => 'Simu',
            'mobile'           => 'Mobile',
            'job-title'        => 'Kazi Kichwa',
            'website'          => 'Tovuti',
            'company-registry' => 'Kampuni Registry',
            'responsible'      => 'Responsible',
            'reference'        => 'Kumbukumbu',
            'parent'           => 'Parent',
            'creator'          => 'Creator',
            'company'          => 'Kampuni',
            'industry'         => 'Industry',
            'industry'         => 'Industry',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mwasiliani imesasishwa',
                    'body'  => 'mwasiliani ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Mwasiliani restored',
                    'body'  => 'mwasiliani ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mwasiliani imefutwa',
                    'body'  => 'mwasiliani ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mwasiliani force imefutwa',
                        'body'  => 'mwasiliani ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Mwasiliani could si kuwa imefutwa',
                        'body'  => 'mwasiliani haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mawasiliano restored',
                    'body'  => 'mawasiliano ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mawasiliano imefutwa',
                    'body'  => 'mawasiliano ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mawasiliano force imefutwa',
                        'body'  => 'mawasiliano ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Mawasiliano could si kuwa imefutwa',
                        'body'  => 'mawasiliano haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
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
                    'avatar'     => 'Avatar',
                    'tax-id'     => 'Kodi Kitambulisho',
                    'job-title'  => 'Kazi Kichwa',
                    'phone'      => 'Simu',
                    'mobile'     => 'Mobile',
                    'email'      => 'Barua pepe',
                    'website'    => 'Tovuti',
                    'title'      => 'Kichwa',
                    'name'       => 'Jina',
                    'short-name' => 'Short Jina',
                    'tags'       => 'Lebo',
                ],

                'address' => [
                    'title' => 'Anwani',

                    'fields' => [
                        'street1'  => 'Street 1',
                        'street2'  => 'Street 2',
                        'city'     => 'Jiji',
                        'zip'      => 'ZIP',
                        'state'    => 'Hali',
                        'country'  => 'Nchi',
                        'name'     => 'Jina',
                        'code'     => 'Msimbo',
                    ],
                ],
            ],
        ],

        'tabs' => [
            'sales-purchase' => [
                'title' => 'Mauzo na Ununuzi',

                'fields' => [
                    'responsible'           => 'Responsible',
                    'responsible-hint-text' => 'Hii ni ndani salesperson responsible kwa hii mteja',
                    'company-id'            => 'Kampuni Kitambulisho',
                    'company-id-hint-text'  => 'registry nambari ya kampuni. Tumia hii ikiwa hii ni different kutoka Kodi Kitambulisho. Hii lazima kuwa unique katika zote washirika ya ile ile nchi',
                    'reference'             => 'Kumbukumbu',
                    'industry'              => 'Industry',
                ],
            ],
        ],
    ],
];

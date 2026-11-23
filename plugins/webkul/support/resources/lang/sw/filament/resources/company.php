<?php

return [
    'title' => 'Makampuni',

    'navigation' => [
        'title' => 'Makampuni',
    ],

    'global-search' => [
        'email' => 'Barua pepe',
    ],

    'form' => [
        'sections' => [
            'company-information' => [
                'title'  => 'Kampuni Taarifa',
                'fields' => [
                    'name'                  => 'Kampuni Jina',
                    'registration-number'   => 'Registration Nambari',
                    'company-id'            => 'Kampuni Kitambulisho',
                    'tax-id'                => 'Kodi Kitambulisho',
                    'tax-id-tooltip'        => 'Kodi Kitambulisho ni unique identifier kwa yako kampuni.',
                    'website'               => 'Tovuti',
                ],
            ],

            'address-information' => [
                'title'  => 'Anwani Taarifa',

                'fields' => [
                    'street1'        => 'Street 1',
                    'street2'        => 'Street 2',
                    'city'           => 'Jiji',
                    'zipcode'        => 'Zip Msimbo',
                    'country'        => 'Nchi',
                    'currency-name'  => 'Sarafu Jina',
                    'phone-code'     => 'Simu Msimbo',
                    'code'           => 'Msimbo',
                    'country-name'   => 'Nchi Jina',
                    'state-required' => 'Hali Inahitajika',
                    'zip-required'   => 'Zip Inahitajika',
                    'create-country' => 'Unda Nchi',
                    'state'          => 'Hali',
                    'state-name'     => 'Hali Jina',
                    'state-code'     => 'Hali Msimbo',
                    'create-state'   => 'Unda Hali',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Taarifa',

                'fields' => [
                    'default-currency'        => 'Chaguo-msingi Sarafu',
                    'currency-name'           => 'Sarafu Jina',
                    'currency-full-name'      => 'Sarafu Kamili Jina',
                    'currency-symbol'         => 'Sarafu Symbol',
                    'currency-iso-numeric'    => 'Sarafu ISO Numeric',
                    'currency-decimal-places' => 'Sarafu Decimal Places',
                    'currency-rounding'       => 'Sarafu Rounding',
                    'currency-status'         => 'Sarafu Hali',
                    'company-foundation-date' => 'Kampuni Foundation Tarehe',
                    'currency-create'         => 'Unda Sarafu',
                    'status'                  => 'Hali',
                ],
            ],

            'branding' => [
                'title'  => 'Branding',
                'fields' => [
                    'company-logo' => 'Kampuni Nembo',
                    'color'        => 'Rangi',
                ],
            ],

            'contact-information' => [
                'title'  => 'Mwasiliani Taarifa',
                'fields' => [
                    'email'  => 'Barua pepe Anwani',
                    'phone'  => 'Simu Nambari',
                    'mobile' => 'Simu Nambari',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Nembo',
            'company-name'         => 'Kampuni Jina',
            'branches'             => 'Branches',
            'email'                => 'Barua pepe',
            'city'                 => 'Jiji',
            'country'              => 'Nchi',
            'currency'             => 'Sarafu',
            'status'               => 'Hali',
            'created-by'           => 'Imeundwa Na',
            'created-at'           => 'Imeundwa',
            'updated-at'           => 'Imesasishwa',
        ],

        'groups' => [
            'company-name' => 'Kampuni Jina',
            'city'         => 'Jiji',
            'country'      => 'Nchi',
            'state'        => 'Hali',
            'email'        => 'Barua pepe',
            'phone'        => 'Simu',
            'currency'     => 'Sarafu',
            'created-by'   => 'Imeundwa Na',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'filters' => [
            'status'  => 'Hali',
            'country' => 'Nchi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kampuni edited',
                    'body'  => 'kampuni ina edited successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kampuni imefutwa',
                    'body'  => 'kampuni ina imefutwa successfully.',

                    'default-company' => [
                        'title' => 'Haiwezekani futa chaguo-msingi kampuni',
                        'body'  => 'Wewe haiwezekani futa kampuni currently weka kama yako chaguo-msingi kampuni.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kampuni restored',
                    'body'  => 'kampuni ina restored successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kampuni force imefutwa',
                        'body'  => 'kampuni ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kampuni force deletion imeshindwa',
                        'body'  => 'kampuni could si kuwa force imefutwa kwa sababu hii ni associated na nyingine rekodi.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Makampuni restored',
                    'body'  => 'makampuni ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Makampuni imefutwa',
                    'body'  => 'makampuni ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Makampuni force imefutwa',
                    'body'  => 'makampuni ina force imefutwa successfully.',

                    'error' => [
                        'title' => 'Makampuni force deletion imeshindwa',
                        'body'  => 'makampuni could si kuwa force imefutwa kwa sababu wao ni associated na nyingine rekodi.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Makampuni imeundwa',
                    'body'  => 'makampuni ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title'   => 'Kampuni Taarifa',
                'entries' => [
                    'name'                  => 'Kampuni Jina',
                    'registration-number'   => 'Registration Nambari',
                    'company-id'            => 'Kampuni Kitambulisho',
                    'tax-id'                => 'Kodi Kitambulisho',
                    'tax-id-tooltip'        => 'Kodi Kitambulisho ni unique identifier kwa yako kampuni.',
                    'website'               => 'Tovuti',
                ],
            ],

            'address-information' => [
                'title'  => 'Anwani Taarifa',

                'entries' => [
                    'street1'        => 'Street 1',
                    'street2'        => 'Street 2',
                    'city'           => 'Jiji',
                    'zipcode'        => 'Zip Msimbo',
                    'country'        => 'Nchi',
                    'currency-name'  => 'Sarafu Jina',
                    'phone-code'     => 'Simu Msimbo',
                    'code'           => 'Msimbo',
                    'country-name'   => 'Nchi Jina',
                    'state-required' => 'Hali Inahitajika',
                    'zip-required'   => 'Zip Inahitajika',
                    'create-country' => 'Unda Nchi',
                    'state'          => 'Hali',
                    'state-name'     => 'Hali Jina',
                    'state-code'     => 'Hali Msimbo',
                    'create-state'   => 'Unda Hali',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Taarifa',

                'entries' => [
                    'default-currency'        => 'Chaguo-msingi Sarafu',
                    'currency-name'           => 'Sarafu Jina',
                    'currency-full-name'      => 'Sarafu Kamili Jina',
                    'currency-symbol'         => 'Sarafu Symbol',
                    'currency-iso-numeric'    => 'Sarafu ISO Numeric',
                    'currency-decimal-places' => 'Sarafu Decimal Places',
                    'currency-rounding'       => 'Sarafu Rounding',
                    'currency-status'         => 'Sarafu Hali',
                    'company-foundation-date' => 'Kampuni Foundation Tarehe',
                    'currency-create'         => 'Unda Sarafu',
                    'status'                  => 'Hali',
                ],
            ],

            'branding' => [
                'title'   => 'Branding',
                'entries' => [
                    'company-logo' => 'Kampuni Nembo',
                    'color'        => 'Rangi',
                ],
            ],

            'contact-information' => [
                'title'   => 'Mwasiliani Taarifa',
                'entries' => [
                    'email'  => 'Barua pepe Anwani',
                    'phone'  => 'Simu Nambari',
                    'mobile' => 'Simu Nambari',
                ],
            ],
        ],
    ],
];

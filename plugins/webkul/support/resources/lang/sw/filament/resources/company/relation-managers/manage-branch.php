<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Jumla Taarifa',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Branch Taarifa',

                        'fields' => [
                            'company-name'                => 'Kampuni Jina',
                            'registration-number'         => 'Registration Nambari',
                            'tax-id'                      => 'Kodi Kitambulisho',
                            'tax-id-tooltip'              => 'Kodi Kitambulisho ni unique identifier kwa yako kampuni.',
                            'color'                       => 'Rangi',
                            'company-id'                  => 'Kampuni Kitambulisho',
                            'company-id-tooltip'          => 'Kampuni Kitambulisho ni unique identifier kwa yako kampuni.',
                        ],
                    ],

                    'branding' => [
                        'title'  => 'Branding',
                        'fields' => [
                            'branch-logo' => 'Branch Nembo',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Anwani Taarifa',

                'sections' => [
                    'address-information' => [
                        'title' => 'Anwani Taarifa',

                        'fields' => [
                            'street1'                => 'Street 1',
                            'street2'                => 'Street 2',
                            'city'                   => 'Jiji',
                            'zip'                    => 'Zip Msimbo',
                            'country'                => 'Nchi',
                            'country-currency-name'  => 'Sarafu Jina',
                            'country-phone-code'     => 'Simu Msimbo',
                            'country-code'           => 'Msimbo',
                            'country-name'           => 'Nchi Jina',
                            'country-state-required' => 'Hali Inahitajika',
                            'country-zip-required'   => 'Zip Inahitajika',
                            'country-create'         => 'Unda Nchi',
                            'state'                  => 'Hali',
                            'state-name'             => 'Hali Jina',
                            'state-code'             => 'Hali Msimbo',
                            'zip-code'               => 'Zip Msimbo',
                            'state-create'           => 'Unda Hali',
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
                            'currency-create'         => 'Unda Sarafu',
                            'company-foundation-date' => 'Kampuni Foundation Tarehe',
                            'status'                  => 'Hali',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Mwasiliani Taarifa',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Mwasiliani Taarifa',

                        'fields' => [
                            'email-address' => 'Barua pepe Anwani',
                            'phone-number'  => 'Simu Nambari',
                            'mobile-number' => 'Simu Nambari',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Nembo',
            'company-name'         => 'Branch Jina',
            'branches'             => 'Branches',
            'email'                => 'Barua pepe',
            'city'                 => 'Jiji',
            'country'              => 'Nchi',
            'currency'             => 'Sarafu',
            'status'               => 'Hali',
            'created-at'           => 'Imeundwa',
            'updated-at'           => 'Imesasishwa',
        ],

        'groups' => [
            'company-name' => 'Branch Jina',
            'city'         => 'Jiji',
            'country'      => 'Nchi',
            'state'        => 'Hali',
            'email'        => 'Barua pepe',
            'phone'        => 'Simu',
            'currency'     => 'Sarafu',
            'created-at'   => 'Imeundwa',
            'updated-at'   => 'Imesasishwa',
        ],

        'filters' => [
            'trashed' => 'Trashed',
            'status'  => 'Hali',
            'country' => 'Nchi',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Branch imeundwa',
                    'body'  => 'branch ina imeundwa successfully.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Branch imesasishwa',
                    'body'  => 'branch ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Branch imefutwa',
                    'body'  => 'branch ina imefutwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Branch restored',
                    'body'  => 'branch ina restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Branches restored',
                    'body'  => 'branches ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Branches imefutwa',
                    'body'  => 'branches ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Branches force imefutwa',
                    'body'  => 'branches ina force imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Jumla Taarifa',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Branch Taarifa',

                        'entries' => [
                            'company-name'                => 'Kampuni Jina',
                            'registration-number'         => 'Registration Nambari',
                            'tax-id'                      => 'Kodi Kitambulisho',
                            'registration-number-tooltip' => 'Kodi Kitambulisho ni unique identifier kwa yako kampuni.',
                            'color'                       => 'Rangi',
                        ],
                    ],

                    'branding' => [
                        'title'   => 'Branding',
                        'entries' => [
                            'branch-logo' => 'Branch Nembo',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Anwani Taarifa',

                'sections' => [
                    'address-information' => [
                        'title' => 'Anwani Taarifa',

                        'entries' => [
                            'street1'                => 'Street 1',
                            'street2'                => 'Street 2',
                            'city'                   => 'Jiji',
                            'zip'                    => 'Zip Msimbo',
                            'country'                => 'Nchi',
                            'country-currency-name'  => 'Sarafu Jina',
                            'country-phone-code'     => 'Simu Msimbo',
                            'country-code'           => 'Msimbo',
                            'country-name'           => 'Nchi Jina',
                            'country-state-required' => 'Hali Inahitajika',
                            'country-zip-required'   => 'Zip Inahitajika',
                            'country-create'         => 'Unda Nchi',
                            'state'                  => 'Hali',
                            'state-name'             => 'Hali Jina',
                            'state-code'             => 'Hali Msimbo',
                            'zip-code'               => 'Zip Msimbo',
                            'state-create'           => 'Unda Hali',
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
                            'currency-create'         => 'Unda Sarafu',
                            'company-foundation-date' => 'Kampuni Foundation Tarehe',
                            'status'                  => 'Hali',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Mwasiliani Taarifa',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Mwasiliani Taarifa',

                        'entries' => [
                            'email-address' => 'Barua pepe Anwani',
                            'phone-number'  => 'Simu Nambari',
                            'mobile-number' => 'Simu Nambari',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

<?php

return [
    'form' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Taarifa za jumla',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Taarifa za tawi',

                        'fields' => [
                            'company-name'                => 'Jina la kampuni',
                            'registration-number'         => 'Namba ya usajili',
                            'tax-id'                      => 'Namba ya mlipakodi',
                            'tax-id-tooltip'              => 'Namba ya mlipakodi ni kitambulisho cha kipekee cha kampuni yako.',
                            'color'                       => 'Rangi',
                            'company-id'                  => 'Kitambulisho cha kampuni',
                            'company-id-tooltip'          => 'Kitambulisho cha kampuni ni kitambulisho cha kipekee cha kampuni yako.',
                        ],
                    ],

                    'branding' => [
                        'title'  => 'Chapa',
                        'fields' => [
                            'branch-logo' => 'Nembo ya tawi',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Taarifa za anwani',

                'sections' => [
                    'address-information' => [
                        'title' => 'Taarifa za anwani',

                        'fields' => [
                            'street1'                => 'Mtaa 1',
                            'street2'                => 'Mtaa 2',
                            'city'                   => 'Jiji',
                            'zip'                    => 'Msimbo wa posta',
                            'country'                => 'Nchi',
                            'country-currency-name'  => 'Jina la sarafu',
                            'country-phone-code'     => 'Msimbo wa simu',
                            'country-code'           => 'Msimbo',
                            'country-name'           => 'Jina la nchi',
                            'country-state-required' => 'Mkoa unahitajika',
                            'country-zip-required'   => 'Msimbo wa posta unahitajika',
                            'country-create'         => 'Unda nchi',
                            'state'                  => 'Mkoa',
                            'state-name'             => 'Jina la mkoa',
                            'state-code'             => 'Msimbo wa mkoa',
                            'zip-code'               => 'Msimbo wa posta',
                            'state-create'           => 'Unda mkoa',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Taarifa za ziada',

                        'fields' => [
                            'default-currency'        => 'Sarafu chaguo-msingi',
                            'currency-name'           => 'Jina la sarafu',
                            'currency-full-name'      => 'Jina kamili la sarafu',
                            'currency-symbol'         => 'Alama ya sarafu',
                            'currency-iso-numeric'    => 'Namba ya ISO ya sarafu',
                            'currency-decimal-places' => 'Nafasi za desimali',
                            'currency-rounding'       => 'Ukadiriaji wa sarafu',
                            'currency-status'         => 'Hali ya sarafu',
                            'currency-create'         => 'Unda sarafu',
                            'company-foundation-date' => 'Tarehe ya kuanzishwa kwa kampuni',
                            'status'                  => 'Hali',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Taarifa za mawasiliano',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Taarifa za mawasiliano',

                        'fields' => [
                            'email-address' => 'Anwani ya barua pepe',
                            'phone-number'  => 'Namba ya simu',
                            'mobile-number' => 'Namba ya simu',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Nembo',
            'company-name'         => 'Jina la tawi',
            'branches'             => 'Matawi',
            'email'                => 'Barua pepe',
            'city'                 => 'Jiji',
            'country'              => 'Nchi',
            'currency'             => 'Sarafu',
            'status'               => 'Hali',
            'created-at'           => 'Iliundwa',
            'updated-at'           => 'Ilisasishwa',
        ],

        'groups' => [
            'company-name' => 'Jina la tawi',
            'city'         => 'Jiji',
            'country'      => 'Nchi',
            'state'        => 'Mkoa',
            'email'        => 'Barua pepe',
            'phone'        => 'Simu',
            'currency'     => 'Sarafu',
            'created-at'   => 'Iliundwa',
            'updated-at'   => 'Ilisasishwa',
        ],

        'filters' => [
            'trashed' => 'Zilizofutwa',
            'status'  => 'Hali',
            'country' => 'Nchi',
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tawi limeundwa',
                    'body'  => 'Tawi limeundwa kikamilifu.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tawi limesasishwa',
                    'body'  => 'Tawi limesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tawi limefutwa',
                    'body'  => 'Tawi limefutwa kikamilifu.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tawi limerejeshwa',
                    'body'  => 'Tawi limerejeshwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Matawi yamerejeshwa',
                    'body'  => 'Matawi yamerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Matawi yamefutwa',
                    'body'  => 'Matawi yamefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Matawi yamefutwa kabisa',
                    'body'  => 'Matawi yamefutwa kabisa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'general-information' => [
                'title' => 'Taarifa za jumla',

                'sections' => [
                    'branch-information' => [
                        'title' => 'Taarifa za tawi',

                        'entries' => [
                            'company-name'                => 'Jina la kampuni',
                            'registration-number'         => 'Namba ya usajili',
                            'tax-id'                      => 'Namba ya mlipakodi',
                            'registration-number-tooltip' => 'Namba ya mlipakodi ni kitambulisho cha kipekee cha kampuni yako.',
                            'color'                       => 'Rangi',
                        ],
                    ],

                    'branding' => [
                        'title'   => 'Chapa',
                        'entries' => [
                            'branch-logo' => 'Nembo ya tawi',
                        ],
                    ],
                ],
            ],

            'address-information' => [
                'title' => 'Taarifa za anwani',

                'sections' => [
                    'address-information' => [
                        'title' => 'Taarifa za anwani',

                        'entries' => [
                            'street1'                => 'Mtaa 1',
                            'street2'                => 'Mtaa 2',
                            'city'                   => 'Jiji',
                            'zip'                    => 'Msimbo wa posta',
                            'country'                => 'Nchi',
                            'country-currency-name'  => 'Jina la sarafu',
                            'country-phone-code'     => 'Msimbo wa simu',
                            'country-code'           => 'Msimbo',
                            'country-name'           => 'Jina la nchi',
                            'country-state-required' => 'Mkoa unahitajika',
                            'country-zip-required'   => 'Msimbo wa posta unahitajika',
                            'country-create'         => 'Unda nchi',
                            'state'                  => 'Mkoa',
                            'state-name'             => 'Jina la mkoa',
                            'state-code'             => 'Msimbo wa mkoa',
                            'zip-code'               => 'Msimbo wa posta',
                            'state-create'           => 'Unda mkoa',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Taarifa za ziada',

                        'entries' => [
                            'default-currency'        => 'Sarafu chaguo-msingi',
                            'currency-name'           => 'Jina la sarafu',
                            'currency-full-name'      => 'Jina kamili la sarafu',
                            'currency-symbol'         => 'Alama ya sarafu',
                            'currency-iso-numeric'    => 'Namba ya ISO ya sarafu',
                            'currency-decimal-places' => 'Nafasi za desimali',
                            'currency-rounding'       => 'Ukadiriaji wa sarafu',
                            'currency-status'         => 'Hali ya sarafu',
                            'currency-create'         => 'Unda sarafu',
                            'company-foundation-date' => 'Tarehe ya kuanzishwa kwa kampuni',
                            'status'                  => 'Hali',
                        ],
                    ],
                ],
            ],

            'contact-information' => [
                'title' => 'Taarifa za mawasiliano',

                'sections' => [
                    'contact-information' => [
                        'title' => 'Taarifa za mawasiliano',

                        'entries' => [
                            'email-address' => 'Anwani ya barua pepe',
                            'phone-number'  => 'Namba ya simu',
                            'mobile-number' => 'Namba ya simu',
                        ],
                    ],
                ],
            ],
        ],
    ],
];

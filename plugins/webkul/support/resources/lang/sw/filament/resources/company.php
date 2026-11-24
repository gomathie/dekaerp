<?php

return [
    'title' => 'Kampuni',

    'navigation' => [
        'title' => 'Kampuni',
    ],

    'global-search' => [
        'email' => 'Barua pepe',
    ],

    'form' => [
        'sections' => [
            'company-information' => [
                'title'  => 'Taarifa za kampuni',
                'fields' => [
                    'name'                  => 'Jina la kampuni',
                    'registration-number'   => 'Namba ya usajili',
                    'company-id'            => 'Kitambulisho cha kampuni',
                    'tax-id'                => 'Namba ya mlipakodi',
                    'tax-id-tooltip'        => 'Namba ya mlipakodi ni kitambulisho cha kipekee cha kampuni yako.',
                    'website'               => 'Tovuti',
                ],
            ],

            'address-information' => [
                'title'  => 'Taarifa za anwani',

                'fields' => [
                    'street1'        => 'Mtaa 1',
                    'street2'        => 'Mtaa 2',
                    'city'           => 'Jiji',
                    'zipcode'        => 'Msimbo wa posta',
                    'country'        => 'Nchi',
                    'currency-name'  => 'Jina la sarafu',
                    'phone-code'     => 'Msimbo wa simu',
                    'code'           => 'Msimbo',
                    'country-name'   => 'Jina la nchi',
                    'state-required' => 'Mkoa unahitajika',
                    'zip-required'   => 'Msimbo wa posta unahitajika',
                    'create-country' => 'Unda nchi',
                    'state'          => 'Mkoa',
                    'state-name'     => 'Jina la mkoa',
                    'state-code'     => 'Msimbo wa mkoa',
                    'create-state'   => 'Unda mkoa',
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
                    'company-foundation-date' => 'Tarehe ya kuanzishwa kwa kampuni',
                    'currency-create'         => 'Unda sarafu',
                    'status'                  => 'Hali',
                ],
            ],

            'branding' => [
                'title'  => 'Chapa',
                'fields' => [
                    'company-logo' => 'Nembo ya kampuni',
                    'color'        => 'Rangi',
                ],
            ],

            'contact-information' => [
                'title'  => 'Taarifa za mawasiliano',
                'fields' => [
                    'email'  => 'Anwani ya barua pepe',
                    'phone'  => 'Namba ya simu',
                    'mobile' => 'Namba ya simu',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'logo'                 => 'Nembo',
            'company-name'         => 'Jina la kampuni',
            'branches'             => 'Matawi',
            'email'                => 'Barua pepe',
            'city'                 => 'Jiji',
            'country'              => 'Nchi',
            'currency'             => 'Sarafu',
            'status'               => 'Hali',
            'created-by'           => 'Imeundwa na',
            'created-at'           => 'Iliundwa',
            'updated-at'           => 'Ilisasishwa',
        ],

        'groups' => [
            'company-name' => 'Jina la kampuni',
            'city'         => 'Jiji',
            'country'      => 'Nchi',
            'state'        => 'Mkoa',
            'email'        => 'Barua pepe',
            'phone'        => 'Simu',
            'currency'     => 'Sarafu',
            'created-by'   => 'Imeundwa na',
            'created-at'   => 'Iliundwa',
            'updated-at'   => 'Ilisasishwa',
        ],

        'filters' => [
            'status'  => 'Hali',
            'country' => 'Nchi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kampuni imehaririwa',
                    'body'  => 'Kampuni imehaririwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kampuni imefutwa',
                    'body'  => 'Kampuni imefutwa kikamilifu.',

                    'default-company' => [
                        'title' => 'Haiwezekani kufuta kampuni chaguo-msingi',
                        'body'  => 'Huwezi kufuta kampuni iliyowekwa kwa sasa kuwa kampuni yako chaguo-msingi.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kampuni imerejeshwa',
                    'body'  => 'Kampuni imerejeshwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kampuni imefutwa kabisa',
                        'body'  => 'Kampuni imefutwa kabisa.',
                    ],

                    'error' => [
                        'title' => 'Imeshindwa kufuta kampuni kabisa',
                        'body'  => 'Kampuni haikuweza kufutwa kabisa kwa sababu imeunganishwa na rekodi nyingine.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kampuni zimerejeshwa',
                    'body'  => 'Kampuni zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kampuni zimefutwa',
                    'body'  => 'Kampuni zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kampuni zimefutwa kabisa',
                    'body'  => 'Kampuni zimefutwa kabisa.',

                    'error' => [
                        'title' => 'Imeshindwa kufuta kampuni hizo kabisa',
                        'body'  => 'Kampuni hazikuweza kufutwa kabisa kwa sababu zimeunganishwa na rekodi nyingine.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Kampuni zimeundwa',
                    'body'  => 'Kampuni zimeundwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'company-information' => [
                'title'   => 'Taarifa za kampuni',
                'entries' => [
                    'name'                  => 'Jina la kampuni',
                    'registration-number'   => 'Namba ya usajili',
                    'company-id'            => 'Kitambulisho cha kampuni',
                    'tax-id'                => 'Namba ya mlipakodi',
                    'tax-id-tooltip'        => 'Namba ya mlipakodi ni kitambulisho cha kipekee cha kampuni yako.',
                    'website'               => 'Tovuti',
                ],
            ],

            'address-information' => [
                'title'  => 'Taarifa za anwani',

                'entries' => [
                    'street1'        => 'Mtaa 1',
                    'street2'        => 'Mtaa 2',
                    'city'           => 'Jiji',
                    'zipcode'        => 'Msimbo wa posta',
                    'country'        => 'Nchi',
                    'currency-name'  => 'Jina la sarafu',
                    'phone-code'     => 'Msimbo wa simu',
                    'code'           => 'Msimbo',
                    'country-name'   => 'Jina la nchi',
                    'state-required' => 'Mkoa unahitajika',
                    'zip-required'   => 'Msimbo wa posta unahitajika',
                    'create-country' => 'Unda nchi',
                    'state'          => 'Mkoa',
                    'state-name'     => 'Jina la mkoa',
                    'state-code'     => 'Msimbo wa mkoa',
                    'create-state'   => 'Unda mkoa',
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
                    'company-foundation-date' => 'Tarehe ya kuanzishwa kwa kampuni',
                    'currency-create'         => 'Unda sarafu',
                    'status'                  => 'Hali',
                ],
            ],

            'branding' => [
                'title'   => 'Chapa',
                'entries' => [
                    'company-logo' => 'Nembo ya kampuni',
                    'color'        => 'Rangi',
                ],
            ],

            'contact-information' => [
                'title'   => 'Taarifa za mawasiliano',
                'entries' => [
                    'email'  => 'Anwani ya barua pepe',
                    'phone'  => 'Namba ya simu',
                    'mobile' => 'Namba ya simu',
                ],
            ],
        ],
    ],
];

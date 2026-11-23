<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'name'                         => 'Jina',
                'tax-type'                     => 'Kodi Aina',
                'tax-computation'              => 'Kodi Computation',
                'tax-scope'                    => 'Kodi Scope',
                'status'                       => 'Hali',
                'amount'                       => 'Kiasi',
                'formula'                      => 'Formula',
                'formula-helper-text'          => 'Arithmetic tu: numbers, + - * / ( ), functions :functions na hizi variables: :variables',
                'children-taxes'               => 'Children Kodi',
                'children-taxes-helper-text'   => 'kiasi ya hii kodi ni sum ya yake children kodi.',
                'children-taxes-type-mismatch' => 'children kodi lazima kuwa ya :type kodi aina au ina hakuna kodi aina. Hizi si: :taxes',
            ],

            'repeater' => [
                'invoice-repartition-lines' => [
                    'label' => 'Ankara Repartition Mistari',
                ],

                'refund-repartition-lines' => [
                    'label' => 'Rudisho Repartition Mistari',
                ],

                'fields' => [
                    'type'           => 'Aina',
                    'factor-percent' => 'Factor %',
                    'account'        => 'Akaunti',
                ],
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Advanced Options',

                    'fields' => [
                        'invoice-label'       => 'Ankara lebo',
                        'tax-group'           => 'Kodi Kundi',
                        'country'             => 'Nchi',
                        'include-in-price'    => 'Imejumuishwa katika Bei',
                        'include-base-amount' => 'Affect Base ya Subsequent Kodi',
                        'is-base-affected'    => 'Base Affected na Iliyotangulia Kodi',
                    ],
                ],

                'fields' => [
                    'description' => 'Maelezo',
                    'legal-notes' => 'Legal Maelezo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                   => 'Jina',
            'amount-type'            => 'Kiasi Aina',
            'company'                => 'Kampuni',
            'tax-group'              => 'Kodi Kundi',
            'country'                => 'Nchi',
            'tax-type'               => 'Kodi Aina',
            'tax-scope'              => 'Kodi Scope',
            'amount-type'            => 'Kiasi Aina',
            'invoice-label'          => 'Ankara Lebo',
            'tax-exigibility'        => 'Kodi Exigibility',
            'price-include-override' => 'Bei Include Override',
            'amount'                 => 'Kiasi',
            'status'                 => 'Hali',
            'include-base-amount'    => 'Include Base Kiasi',
            'is-base-affected'       => 'Ni Base Affected',
        ],

        'groups' => [
            'name'         => 'Jina',
            'company'      => 'Kampuni',
            'tax-group'    => 'Kodi Kundi',
            'country'      => 'Nchi',
            'created-by'   => 'Imeundwa Na',
            'type-tax-use' => 'Aina Kodi Tumia',
            'tax-scope'    => 'Kodi Scope',
            'amount-type'  => 'Kiasi Aina',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kodi imefutwa',
                        'body'  => 'Kodi ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kodi could si kuwa imefutwa',
                        'body'  => 'kodi haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kodi imefutwa',
                        'body'  => 'kodi ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Kodi could si kuwa imefutwa',
                        'body'  => 'kodi haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'pages' => [
            'create' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Si halali Repartition Mistari',
                    ],
                ],
            ],

            'edit' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Si halali Repartition Mistari',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'            => 'Jina',
                'tax-type'        => 'Kodi Aina',
                'tax-computation' => 'Kodi Computation',
                'tax-scope'       => 'Kodi Scope',
                'status'          => 'Hali',
                'amount'          => 'Kiasi',
                'formula'         => 'Formula',
                'children-taxes'  => 'Children Kodi',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Advanced Options',

                    'entries' => [
                        'invoice-label'       => 'Ankara lebo',
                        'tax-group'           => 'Kodi Kundi',
                        'country'             => 'Nchi',
                        'include-in-price'    => 'Include katika bei',
                        'include-base-amount' => 'Include base kiasi',
                        'is-base-affected'    => 'Ni base affected',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'Maelezo & Ankara Legal Maelezo',
                    'entries' => [
                        'description' => 'Maelezo',
                        'legal-notes' => 'Legal Maelezo',
                    ],
                ],
            ],
        ],
    ],

];

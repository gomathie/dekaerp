<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Uingizaji wa jarida',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Uhasibu Taarifa',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Dedicated Noti ya mkopo Mfuatano',
                            'dedicated-payment-sequence'     => 'Dedicated Malipo Mfuatano',
                            'sort-code-placeholder'          => 'Ingiza jarida msimbo',
                            'sort-code'                      => 'Sort',
                            'currency'                       => 'Sarafu',
                            'color'                          => 'Rangi',
                            'default-account'                => 'Chaguo-msingi Akaunti',
                            'profit-account'                 => 'Faida Akaunti',
                            'loss-account'                   => 'Hasara Akaunti',
                            'suspense-account'               => 'Suspense Akaunti',
                            'bank-account'                   => 'Akaunti ya benki',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Akaunti ya benki Nambari',
                    ],
                ],
            ],

            'incoming-payments' => [
                'title'            => 'Incoming Malipo',
                'add-action-label' => 'Ongeza Mstari',

                'fields' => [
                    'payment-method'             => 'Malipo Method',
                    'display-name'               => 'Display Jina',
                    'account-number'             => 'Outstanding Stakabadhi Akaunti',
                    'relation-notes'             => 'Relation Maelezo',
                    'relation-notes-placeholder' => 'Ingiza yoyote relation maelezo',
                ],
            ],

            'outgoing-payments' => [
                'title'            => 'Outgoing Malipo',
                'add-action-label' => 'Ongeza Mstari',

                'fields' => [
                    'payment-method'             => 'Malipo Method',
                    'display-name'               => 'Display Jina',
                    'account-number'             => 'Outstanding Malipo Akaunti',
                    'relation-notes'             => 'Relation Maelezo',
                    'relation-notes-placeholder' => 'Ingiza yoyote relation maelezo',
                ],
            ],

            'advanced-settings' => [
                'title'  => 'Advanced Mipangilio',

                'fields' => [
                    'allowed-accounts'       => 'Inaruhusiwa Akaunti',
                    'control-access'         => 'Control Access',
                    'payment-communication'  => 'Malipo Mawasiliano',
                    'auto-check-on-post'     => 'Auto Hundi washa Post',
                    'communication-type'     => 'Mawasiliano Aina',
                    'communication-standard' => 'Mawasiliano Standard',
                ],
            ],
        ],

        'general' => [
            'title' => 'Jumla Taarifa',

            'fields' => [
                'name'    => 'Jina',
                'type'    => 'Aina',
                'company' => 'Kampuni',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'type'       => 'Aina',
            'code'       => 'Msimbo',
            'currency'   => 'Sarafu',
            'created-by' => 'Imeundwa Na',
            'status'     => 'Hali',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Jarida imefutwa',
                        'body'  => 'jarida ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Jarida deletion imeshindwa',
                        'body'  => 'jarida haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Jarida imefutwa',
                        'body'  => 'jarida ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Majarida deletion imeshindwa',
                        'body'  => 'majarida haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Uingizaji wa jarida',

                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'Uhasibu Taarifa',

                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Dedicated Noti ya mkopo Mfuatano',
                            'dedicated-payment-sequence'     => 'Dedicated Malipo Mfuatano',
                            'sort-code-placeholder'          => 'Ingiza jarida msimbo',
                            'sort-code'                      => 'Sort',
                            'currency'                       => 'Sarafu',
                            'color'                          => 'Rangi',
                            'default-account'                => 'Chaguo-msingi Akaunti',
                            'profit-account'                 => 'Faida Akaunti',
                            'loss-account'                   => 'Hasara Akaunti',
                            'suspense-account'               => 'Suspense Akaunti',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Akaunti ya benki Nambari',

                        'entries' => [
                            'account-number' => 'Akaunti Nambari',
                        ],
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'Incoming Malipo',

                'entries' => [
                    'payment-method'             => 'Malipo Method',
                    'display-name'               => 'Display Jina',
                    'account-number'             => 'Outstanding Stakabadhi Akaunti',
                    'relation-notes'             => 'Relation Maelezo',
                    'relation-notes-placeholder' => 'Ingiza yoyote relation maelezo',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'Outgoing Malipo',

                'entries' => [
                    'payment-method'             => 'Malipo Method',
                    'display-name'               => 'Display Jina',
                    'account-number'             => 'Outstanding Malipo Akaunti',
                    'relation-notes'             => 'Relation Maelezo',
                    'relation-notes-placeholder' => 'Ingiza yoyote relation maelezo',
                ],
            ],

            'advanced-settings' => [
                'title'   => 'Advanced Mipangilio',

                'allowed-accounts' => [
                    'title' => 'Inaruhusiwa Akaunti',

                    'entries' => [
                        'allowed-accounts'       => 'Inaruhusiwa Akaunti',
                        'control-access'         => 'Control Access',
                        'auto-check-on-post'     => 'Auto Hundi washa Post',
                    ],
                ],

                'payment-communication'  => [
                    'title' => 'Malipo Mawasiliano',

                    'entries' => [
                        'communication-type'     => 'Mawasiliano Aina',
                        'communication-standard' => 'Mawasiliano Standard',
                    ],
                ],
            ],
        ],

        'general' => [
            'title' => 'Jumla Taarifa',

            'entries' => [
                'name'    => 'Jina',
                'type'    => 'Aina',
                'company' => 'Kampuni',
            ],
        ],
    ],

];

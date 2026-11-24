<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Maingizo ya jarida',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Taarifa za uhasibu',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Mfuatano maalum wa hati za mkopo',
                            'dedicated-payment-sequence'     => 'Mfuatano maalum wa malipo',
                            'sort-code-placeholder'          => 'Weka msimbo wa jarida',
                            'sort-code'                      => 'Mpangilio',
                            'currency'                       => 'Sarafu',
                            'color'                          => 'Rangi',
                            'default-account'                => 'Akaunti chaguo-msingi',
                            'profit-account'                 => 'Akaunti ya faida',
                            'loss-account'                   => 'Akaunti ya hasara',
                            'suspense-account'               => 'Akaunti ya muda',
                            'bank-account'                   => 'Akaunti ya benki',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Nambari ya akaunti ya benki',
                    ],
                ],
            ],

            'incoming-payments' => [
                'title'            => 'Malipo yanayoingia',
                'add-action-label' => 'Ongeza mstari',

                'fields' => [
                    'payment-method'             => 'Njia ya malipo',
                    'display-name'               => 'Jina la kuonyesha',
                    'account-number'             => 'Akaunti za stakabadhi ambazo hazijashughulikiwa',
                    'relation-notes'             => 'Vidokezo vya uhusiano',
                    'relation-notes-placeholder' => 'Weka maelezo yoyote ya uhusiano',
                ],
            ],

            'outgoing-payments' => [
                'title'            => 'Malipo yanayotoka',
                'add-action-label' => 'Ongeza mstari',

                'fields' => [
                    'payment-method'             => 'Njia ya malipo',
                    'display-name'               => 'Jina la kuonyesha',
                    'account-number'             => 'Akaunti za malipo ambayo hayajashughulikiwa',
                    'relation-notes'             => 'Vidokezo vya uhusiano',
                    'relation-notes-placeholder' => 'Weka maelezo yoyote ya uhusiano',
                ],
            ],

            'advanced-settings' => [
                'title'  => 'Mipangilio ya kina',

                'fields' => [
                    'allowed-accounts'       => 'Akaunti zinazoruhusiwa',
                    'control-access'         => 'Dhibiti ufikiaji',
                    'payment-communication'  => 'Maelezo ya malipo',
                    'auto-check-on-post'     => 'Kagua kiotomatiki wakati wa kurekodi',
                    'communication-type'     => 'Aina ya maelezo',
                    'communication-standard' => 'Kiwango cha maelezo',
                ],
            ],
        ],

        'general' => [
            'title' => 'Taarifa za jumla',

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
            'created-by' => 'Imeundwa na',
            'status'     => 'Hali',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Jarida limefutwa',
                        'body'  => 'Jarida limefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Imeshindwa kufuta jarida',
                        'body'  => 'Jarida haliwezi kufutwa kwa sababu linatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Majarida yamefutwa',
                        'body'  => 'Majarida yamefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Imeshindwa kufuta majarida',
                        'body'  => 'Majarida hayawezi kufutwa kwa sababu yanatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Maingizo ya jarida',

                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'Taarifa za uhasibu',

                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Mfuatano maalum wa hati za mkopo',
                            'dedicated-payment-sequence'     => 'Mfuatano maalum wa malipo',
                            'sort-code-placeholder'          => 'Weka msimbo wa jarida',
                            'sort-code'                      => 'Mpangilio',
                            'currency'                       => 'Sarafu',
                            'color'                          => 'Rangi',
                            'default-account'                => 'Akaunti chaguo-msingi',
                            'profit-account'                 => 'Akaunti ya faida',
                            'loss-account'                   => 'Akaunti ya hasara',
                            'suspense-account'               => 'Akaunti ya muda',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Nambari ya akaunti ya benki',

                        'entries' => [
                            'account-number' => 'Nambari ya akaunti',
                        ],
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'Malipo yanayoingia',

                'entries' => [
                    'payment-method'             => 'Njia ya malipo',
                    'display-name'               => 'Jina la kuonyesha',
                    'account-number'             => 'Akaunti za stakabadhi ambazo hazijashughulikiwa',
                    'relation-notes'             => 'Vidokezo vya uhusiano',
                    'relation-notes-placeholder' => 'Weka maelezo yoyote ya uhusiano',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'Malipo yanayotoka',

                'entries' => [
                    'payment-method'             => 'Njia ya malipo',
                    'display-name'               => 'Jina la kuonyesha',
                    'account-number'             => 'Akaunti za malipo ambayo hayajashughulikiwa',
                    'relation-notes'             => 'Vidokezo vya uhusiano',
                    'relation-notes-placeholder' => 'Weka maelezo yoyote ya uhusiano',
                ],
            ],

            'advanced-settings' => [
                'title'   => 'Mipangilio ya kina',

                'allowed-accounts' => [
                    'title' => 'Akaunti zinazoruhusiwa',

                    'entries' => [
                        'allowed-accounts'       => 'Akaunti zinazoruhusiwa',
                        'control-access'         => 'Dhibiti ufikiaji',
                        'auto-check-on-post'     => 'Kagua kiotomatiki wakati wa kurekodi',
                    ],
                ],

                'payment-communication'  => [
                    'title' => 'Maelezo ya malipo',

                    'entries' => [
                        'communication-type'     => 'Aina ya maelezo',
                        'communication-standard' => 'Kiwango cha maelezo',
                    ],
                ],
            ],
        ],

        'general' => [
            'title' => 'Taarifa za jumla',

            'entries' => [
                'name'    => 'Jina',
                'type'    => 'Aina',
                'company' => 'Kampuni',
            ],
        ],
    ],

];

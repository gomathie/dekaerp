<?php

return [
    'global-search' => [
        'code' => 'Msimbo',
        'type' => 'Aina',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code'                   => 'Msimbo',
                'account-name'           => 'Jina la akaunti',
                'accounting'             => 'Uhasibu',
                'account-type'           => 'Aina ya akaunti',
                'parent-account'         => 'Akaunti mzazi',
                'parent-account-helper'  => 'Chagua akaunti iliyopo ili hii iwe akaunti ndogo yake.',
                'default-taxes'          => 'Kodi chaguo-msingi',
                'tags'                   => 'Lebo',
                'journals'               => 'Majarida',
                'journals-helper'        => 'Hupendekezwa kiotomatiki kulingana na aina ya akaunti iliyochaguliwa. Unaweza kubadilisha chaguo.',
                'currency'               => 'Sarafu',
                'deprecated'             => 'Imesitishwa',
                'reconcile'              => 'Ruhusu upatanisho',
                'non-trade'              => 'Isiyo ya biashara',
                'companies'              => 'Kampuni',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'           => 'Msimbo',
            'account-name'   => 'Jina la akaunti',
            'account-type'   => 'Akaunti',
            'parent-account' => 'Akaunti mzazi',
            'currency'       => 'Sarafu',
            'journals'       => 'Majarida',
            'reconcile'      => 'Ruhusu upatanisho',
        ],

        'grouping' => [
            'account-type' => 'Aina ya akaunti',
        ],

        'filters' => [
            'account-type'     => 'Aina ya akaunti',
            'parent-account'   => 'Akaunti mzazi',
            'allow-reconcile'  => 'Ruhusu upatanisho',
            'currency'         => 'Sarafu',
            'account-journals' => 'Majarida',
            'non-trade'        => 'Isiyo ya biashara',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Akaunti imesasishwa',
                    'body'  => 'Akaunti imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Akaunti imefutwa',
                        'body'  => 'Akaunti imefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Imeshindwa kufuta akaunti',
                        'body'  => 'Akaunti haikuweza kufutwa kwa sababu ina vipengee vya jarida vinavyohusiana nayo.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Akaunti zimefutwa',
                        'body'  => 'Akaunti zimefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Imeshindwa kufuta akaunti',
                        'body'  => 'Akaunti hazikuweza kufutwa kwa sababu zina vipengee vya jarida vinavyohusiana nazo.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'           => 'Msimbo',
                'account-name'   => 'Jina la akaunti',
                'accounting'     => 'Uhasibu',
                'account-type'   => 'Aina ya akaunti',
                'parent-account' => 'Akaunti mzazi',
                'sub-accounts'   => 'Akaunti ndogo',
                'default-taxes'  => 'Kodi chaguo-msingi',
                'tags'           => 'Lebo',
                'journals'       => 'Majarida',
                'currency'       => 'Sarafu',
                'deprecated'     => 'Imesitishwa',
                'reconcile'      => 'Upatanisho',
                'non-trade'      => 'Isiyo ya biashara',
            ],
        ],
    ],
];

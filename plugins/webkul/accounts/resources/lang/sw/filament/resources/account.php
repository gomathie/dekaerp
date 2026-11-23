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
                'account-name'           => 'Akaunti Jina',
                'accounting'             => 'Uhasibu',
                'account-type'           => 'Akaunti Aina',
                'parent-account'         => 'Parent Akaunti',
                'parent-account-helper'  => 'Chagua existing akaunti hadi fanya hii sub-akaunti.',
                'default-taxes'          => 'Chaguo-msingi Kodi',
                'tags'                   => 'Lebo',
                'journals'               => 'Majarida',
                'journals-helper'        => 'Suggested kiotomatiki based washa imechaguliwa Akaunti Aina. Wewe unaweza override selection.',
                'currency'               => 'Sarafu',
                'deprecated'             => 'Deprecated',
                'reconcile'              => 'Ruhusu Patanisha',
                'non-trade'              => 'Non Trade',
                'companies'              => 'Makampuni',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'           => 'Msimbo',
            'account-name'   => 'Akaunti Jina',
            'account-type'   => 'Akaunti',
            'parent-account' => 'Parent Akaunti',
            'currency'       => 'Sarafu',
            'journals'       => 'Majarida',
            'reconcile'      => 'Ruhusu Patanisha',
        ],

        'grouping' => [
            'account-type' => 'Akaunti Aina',
        ],

        'filters' => [
            'account-type'     => 'Akaunti Aina',
            'parent-account'   => 'Parent Akaunti',
            'allow-reconcile'  => 'Ruhusu Patanisha',
            'currency'         => 'Sarafu',
            'account-journals' => 'Majarida',
            'non-trade'        => 'Non Trade',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Akaunti imesasishwa',
                    'body'  => 'akaunti ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Akaunti imefutwa',
                        'body'  => 'akaunti ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Akaunti deletion imeshindwa',
                        'body'  => 'akaunti could si kuwa imefutwa kwa sababu hii ina associated jarida vipengee.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Akaunti imefutwa',
                        'body'  => 'akaunti ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Akaunti deletion imeshindwa',
                        'body'  => 'akaunti could si kuwa imefutwa kwa sababu wao ina associated jarida vipengee.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'           => 'Msimbo',
                'account-name'   => 'Akaunti Jina',
                'accounting'     => 'Uhasibu',
                'account-type'   => 'Akaunti Aina',
                'parent-account' => 'Parent Akaunti',
                'sub-accounts'   => 'Sub Akaunti',
                'default-taxes'  => 'Chaguo-msingi Kodi',
                'tags'           => 'Lebo',
                'journals'       => 'Majarida',
                'currency'       => 'Sarafu',
                'deprecated'     => 'Deprecated',
                'reconcile'      => 'Patanisha',
                'non-trade'      => 'Non Trade',
            ],
        ],
    ],
];

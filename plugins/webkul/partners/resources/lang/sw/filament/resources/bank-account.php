<?php

return [
    'navigation' => [
        'group' => 'Benki',
        'title' => 'Akaunti za benki',
    ],

    'form' => [
        'account-number'     => 'Namba ya akaunti',
        'bank'               => 'Benki',
        'account-holder'     => 'Mmiliki wa akaunti',
        'can-send-money'     => 'Inaweza kutuma pesa',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Namba ya akaunti',
            'bank'           => 'Benki',
            'account-holder' => 'Mmiliki wa akaunti',
            'send-money'     => 'Inaweza kutuma pesa',
            'created-at'     => 'Iliundwa',
            'updated-at'     => 'Ilisasishwa',
            'deleted-at'     => 'Ilifutwa',
        ],

        'filters' => [
            'bank'           => 'Benki',
            'account-holder' => 'Mmiliki wa akaunti',
            'creator'        => 'Muundaji',
            'can-send-money' => 'Inaweza kutuma pesa',
        ],

        'groups' => [
            'bank'               => 'Benki',
            'can-send-money'     => 'Inaweza kutuma pesa',
            'created-at'         => 'Iliundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imesasishwa',
                    'body'  => 'Akaunti ya benki imesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imerejeshwa',
                    'body'  => 'Akaunti ya benki imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imefutwa',
                    'body'  => 'Akaunti ya benki imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Akaunti ya benki imefutwa kabisa',
                    'body'  => 'Akaunti ya benki imefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Akaunti za benki zimerejeshwa',
                    'body'  => 'Akaunti za benki zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Akaunti za benki zimefutwa',
                    'body'  => 'Akaunti za benki zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Akaunti za benki zimefutwa kabisa',
                    'body'  => 'Akaunti za benki zimefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],
    ],
];

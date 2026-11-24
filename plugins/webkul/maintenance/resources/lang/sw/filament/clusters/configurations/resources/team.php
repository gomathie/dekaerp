<?php

return [
    'navigation' => [
        'title' => 'Timu',
    ],

    'form' => [
        'name'    => 'Jina',
        'company' => 'Kampuni',
        'users'   => 'Wanatimu',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'company'    => 'Kampuni',
            'users'      => 'Wanatimu',
            'created-at' => 'Iliundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Timu imesasishwa',
                    'body'  => 'Timu imesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Timu imerejeshwa',
                    'body'  => 'Timu imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Timu imefutwa',
                    'body'  => 'Timu imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Timu imefutwa kabisa',
                        'body'  => 'Timu imefutwa kabisa kwa mafanikio.',
                    ],
                    'error' => [
                        'title' => 'Timu haikuweza kufutwa kabisa',
                        'body'  => 'Timu inatumika na haiwezi kufutwa kabisa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Timu zimerejeshwa',
                    'body'  => 'Timu zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Timu zimefutwa',
                    'body'  => 'Timu zimefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];

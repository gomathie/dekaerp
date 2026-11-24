<?php

return [
    'navigation' => [
        'title' => 'Sekta',
    ],

    'form' => [
        'name'      => 'Jina',
        'full-name' => 'Jina kamili',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'full-name'  => 'Jina kamili',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sekta imesasishwa',
                    'body'  => 'Sekta imesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Sekta imerejeshwa',
                    'body'  => 'Sekta imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sekta imefutwa',
                    'body'  => 'Sekta imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sekta imefutwa kabisa',
                    'body'  => 'Sekta imefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sekta zimerejeshwa',
                    'body'  => 'Sekta zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sekta zimefutwa',
                    'body'  => 'Sekta zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Sekta zimefutwa kabisa',
                    'body'  => 'Sekta zimefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
